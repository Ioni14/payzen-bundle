<?php

namespace Ioni\PayzenBundle\Service;

use Doctrine\Common\Persistence\ManagerRegistry;
use Ioni\PayzenBundle\Event\TransactionEvent;
use Ioni\PayzenBundle\Exception\CorruptedPaymentNotificationException;
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
     * PaymentNotificationHandler constructor.
     *
     * @param SignatureHandler         $signatureHandler
     * @param EventDispatcherInterface $dispatcher
     * @param ManagerRegistry          $registry
     */
    public function __construct(
        SignatureHandler $signatureHandler,
        EventDispatcherInterface $dispatcher,
        ManagerRegistry $registry
    ) {
        $this->signatureHandler = $signatureHandler;
        $this->dispatcher = $dispatcher;
        $this->registry = $registry;
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

        if (!isset($fields['vads_url_check_src']) || !in_array($fields['vads_url_check_src'], ['PAY', 'BO', 'REC'], true)) {
            // we want only payment or recurrent payment notifications
            return;
        }

        if (!isset($fields['vads_payment_config']) || $fields['vads_payment_config'] !== 'SINGLE') {
            // we want only Single payment, not multi
            return;
        }

        // TODO : vads_operation_type does not exist for subscriptions
//        if (!isset($fields['vads_operation_type']) || $fields['vads_operation_type'] !== 'DEBIT') {
//            // we want only Debit operation
//            return;
//        }

        /** @var Transaction $transaction */
        $transaction = $this->registry->getRepository('IoniPayzenBundle:Transaction')->find($fields['vads_order_id']);
        if ($transaction === null) {
            $this->dispatcher->dispatch(TransactionEvent::UNFOUND_EVENT);

            return;
        }

        if ($transaction->getStatus() !== Transaction::STATUS_WAITING) {
            // transaction has already updated
            return;
        }

        if (!isset($fields['vads_trans_id']) || $transaction->getNumber() !== $fields['vads_trans_id']) {
            throw new CorruptedPaymentNotificationException('Bad trans id in the response for the transaction '.$transaction->getId());
        }
        if (!isset($fields['vads_auth_result'])) {
            throw new CorruptedPaymentNotificationException('No auth result in the response.');
        }

        $transaction->setResultCode($fields['vads_auth_result']);
        $transaction->setResponse($fields);

        /** @see https://payzen.io/fr-FR/form-payment/standard-payment/traiter-les-donnees-de-la-reponse.html */
        //$fields['vads_trans_status'] !== 'AUTHORISED';

        // TODO : service for TransactionErrors ?

        // traitement erreurs

        if ($transaction->getResultCode() !== '00') {
            $transaction->setStatus(Transaction::STATUS_REJECTED);
            $transaction = $this->dispatcher->dispatch(TransactionEvent::REJECTED_EVENT, new TransactionEvent($transaction))->getTransaction();
        } else {
            // success
            $transaction->setStatus(Transaction::STATUS_SUCCEEDED);
            $transaction = $this->dispatcher->dispatch(TransactionEvent::SUCCEEDED_EVENT, new TransactionEvent($transaction))->getTransaction();
        }

        $transaction->setUpdatedAt(new \DateTime());
        $manager = $this->registry->getManager();
        $manager->merge($transaction);
        $manager->flush();
    }
}
