<?php

namespace Ioni\PayzenBundle\Service;

use Doctrine\Common\Persistence\ManagerRegistry;
use Ioni\PayzenBundle\Event\TransactionEvent;
use Ioni\PayzenBundle\Exception\CorruptedPaymentNotificationException;
use Ioni\PayzenBundle\Exception\TransactionNotFoundException;
use Ioni\PayzenBundle\Fetchers\TransactionFetcherInterface;
use Ioni\PayzenBundle\Model\Transaction;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PaymentNotificationHandler.
 * Handles the payment responses from Payzen.
 *
 * @author Thomas Talbot <talbot.thomas14@gmail.com>
 */
class PaymentNotificationHandler
{
    const RESULT_CODE_OK = '00';

    /**
     * @var SignatureHandler
     */
    protected $signatureHandler;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var ManagerRegistry
     */
    protected $registry;

    /**
     * @var TransactionFetcherInterface
     */
    protected $transactionFetcher;

    /**
     * PaymentNotificationHandler constructor.
     *
     * @param SignatureHandler            $signatureHandler
     * @param EventDispatcherInterface    $dispatcher
     * @param ManagerRegistry             $registry
     * @param TransactionFetcherInterface $transactionFetcher
     */
    public function __construct(
        SignatureHandler $signatureHandler,
        EventDispatcherInterface $dispatcher,
        ManagerRegistry $registry,
        TransactionFetcherInterface $transactionFetcher
    ) {
        $this->signatureHandler = $signatureHandler;
        $this->dispatcher = $dispatcher;
        $this->registry = $registry;
        $this->transactionFetcher = $transactionFetcher;
    }

    /**
     * @param Request $request
     *
     * @throws CorruptedPaymentNotificationException
     */
    public function handle(Request $request)
    {
        // https://payzen.io/fr-FR/form-payment/standard-payment/gerer-le-dialogue-vers-le-site-marchand.html
        $fields = $request->request->all();

        if (!isset($fields['signature']) || !$this->signatureHandler->isEquals($fields['signature'], $fields)) {
            throw new CorruptedPaymentNotificationException('Signature not found or corrupted response.');
        }
        if (!isset($fields['vads_order_id'])) {
            throw new CorruptedPaymentNotificationException('No order id given in the response.');
        }
        if (!isset($fields['vads_url_check_src'])) {
            throw new CorruptedPaymentNotificationException('No check src given in the response.');
        }
        if (!isset($fields['vads_auth_result'])) {
            throw new CorruptedPaymentNotificationException('No auth result in the response.');
        }

        try {
            $transaction = $this->transactionFetcher->findTransaction($fields['vads_order_id'], $fields);
        } catch (TransactionNotFoundException $e) {
            $transaction = null;
        }
        if ($transaction === null) {
            $this->dispatcher->dispatch(TransactionEvent::UNFOUND_EVENT);

            return;
        }

        /** @see https://payzen.io/en-EN/form-payment/subscription-token/analyzing-the-nature-of-notification.html */
        if ($fields['vads_url_check_src'] === 'REC') {
            $res = $this->handleRecurrent($fields, $transaction);
        } elseif (in_array($fields['vads_url_check_src'], ['PAY', 'BO'], true)) {
            $res = $this->handlePayment($fields, $transaction);
        } else {
            // TODO : handle the other sources
            return;
        }

        if (!$res) {
            // something bad happened
            return;
        }

        $transaction->setUpdatedAt(new \DateTime());
        $manager = $this->registry->getManager();
        $manager->merge($transaction);
        $manager->flush();
    }

    /**
     * @param array       $fields
     * @param Transaction $transaction
     *
     * @return bool true if success
     *
     * @throws CorruptedPaymentNotificationException
     */
    protected function handlePayment(array $fields, Transaction &$transaction): bool
    {
        if (!isset($fields['vads_trans_id']) || $transaction->getNumber() !== $fields['vads_trans_id']) {
            throw new CorruptedPaymentNotificationException('Bad trans id in the response for the transaction '.$transaction->getId());
        }
        if (!isset($fields['vads_payment_config']) || $fields['vads_payment_config'] !== 'SINGLE') {
            // TODO : handle other payment_config
            return false;
        }
        if (isset($fields['vads_operation_type']) && $fields['vads_operation_type'] !== 'DEBIT') {
            // we want only Debit operation if this is a payment operation (not like REGISTER_SUBSCRIBE)
            return false;
        }

        if ($transaction->getStatus() !== Transaction::STATUS_WAITING) {
            // transaction has already updated
            return false;
        }

        $transaction->setResultCode($fields['vads_auth_result']);
        $transaction->setResponse($fields);

        $alias = $transaction->getAlias();
        if ($alias !== null
            && isset($fields['vads_identifier_status'], $fields['vads_identifier'])
            && in_array($fields['vads_identifier_status'], ['CREATED', 'UPDATED'], true)
        ) {
            $alias->setIdentifier($fields['vads_identifier']);
            $alias->setCardType($fields['vads_card_brand'] ?? null);
            $alias->setCardNumber($fields['vads_card_number'] ?? null);
            if (isset($fields['vads_expiry_month']) && $fields['vads_expiry_month'] !== '') {
                $alias->setExpiryMonth((int) $fields['vads_expiry_month']);
            }
            if (isset($fields['vads_expiry_year']) && $fields['vads_expiry_year'] !== '') {
                $alias->setExpiryYear((int) $fields['vads_expiry_year']);
            }
        }

        if (isset($fields['vads_subscription']) && $transaction->getSubscriptionInfos() !== null) {
            $transaction->getSubscriptionInfos()->setIdentifier($fields['vads_subscription']);
        }

        /** @see https://payzen.io/fr-FR/form-payment/standard-payment/traiter-les-donnees-de-la-reponse.html */
        //$fields['vads_trans_status'] !== 'AUTHORISED';

        // TODO : service for TransactionErrors ?
        // errors handling

        if ($transaction->getResultCode() === self::RESULT_CODE_OK) {
            $transaction->setStatus(Transaction::STATUS_SUCCEEDED);
            $transaction = $this->dispatcher->dispatch(TransactionEvent::SUCCEEDED_EVENT, new TransactionEvent($transaction))->getTransaction();
        } else {
            $transaction->setStatus(Transaction::STATUS_REJECTED);
            $transaction = $this->dispatcher->dispatch(TransactionEvent::REJECTED_EVENT, new TransactionEvent($transaction))->getTransaction();
        }

        return true;
    }

    /**
     * @param array       $fields
     * @param Transaction $transaction
     *
     * @return bool true if success
     */
    protected function handleRecurrent(array $fields, Transaction &$transaction): bool
    {
        if ($transaction->getSubscriptionInfos() === null) {
            // inconsistency transaction
            return false;
        }
        if ($transaction->getStatus() !== Transaction::STATUS_SUCCEEDED) {
            // transaction not valid
            return false;
        }
        if (!isset($fields['vads_recurrence_number'])) {
            return false;
        }
        if (!isset($fields['vads_operation_type']) || $fields['vads_operation_type'] !== 'DEBIT') {
            // we want only Debit operation
            return false;
        }

        $transaction->getSubscriptionInfos()->addResponse($fields);

        // TODO : service for TransactionErrors ?

        // traitement erreurs

        if ($transaction->getResultCode() === self::RESULT_CODE_OK) {
            $transaction->getSubscriptionInfos()->setLastRecurrenceNumber((int) $fields['vads_recurrence_number']);
            $transaction = $this->dispatcher->dispatch(TransactionEvent::SUCCEEDED_RECURRENT_EVENT, new TransactionEvent($transaction))->getTransaction();
        } else {
            $transaction = $this->dispatcher->dispatch(TransactionEvent::REJECTED_RECURRENT_EVENT, new TransactionEvent($transaction))->getTransaction();
        }

        return true;
    }
}
