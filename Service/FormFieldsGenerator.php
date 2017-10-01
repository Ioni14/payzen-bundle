<?php

namespace Ioni\PayzenBundle\Service;

use Doctrine\Common\Collections\Collection;
use Ioni\PayzenBundle\Model\SubscriptionInfos;
use Ioni\PayzenBundle\Model\Transaction;
use Ioni\PayzenBundle\Model\TransactionCustomer;
use Ioni\PayzenBundle\Model\TransactionProduct;
use Ioni\PayzenBundle\Model\TransactionShipping;
use Ioni\PayzenBundle\Model\TransactionView;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class FormFieldsGenerator.
 * Generates a transaction form.
 *
 * @author Thomas Talbot <talbot.thomas14@gmail.com>
 */
class FormFieldsGenerator
{
    /**
     * Identifier of the shop.
     *
     * @var string
     */
    private $siteId;

    /**
     * Filepath for the generation of transaction numbers.
     *
     * @var string
     */
    private $transNumbersPath;

    /**
     * Fields of the transaction form.
     *
     * @var array
     *
     * @see https://payzen.io/en-EN/form-payment/standard-payment/data-dictionary.html All form fields.
     */
    protected $fields;

    /**
     * Signature of the transaction fields.
     *
     * @var string
     */
    protected $signature;

    /**
     * @var UrlGeneratorInterface
     */
    protected $router;

    /**
     * @var SignatureHandler
     */
    protected $signatureHandler;

    /**
     * FormFieldsGenerator constructor.
     */
    public function __construct(UrlGeneratorInterface $router, SignatureHandler $signatureHandler)
    {
        $this->router = $router;
        $this->signatureHandler = $signatureHandler;
        $this->fields = [];
    }

    /**
     * Generates a new transaction number and returns it.
     *
     * @throw IOException cannot get the lock of the transNumbers file
     */
    public function generateTransactionNumber(): string
    {
        $fs = new Filesystem();
        if (!$fs->exists($this->transNumbersPath)) {
            $fs->touch($this->transNumbersPath);
        }

        $file = (new \SplFileInfo($this->transNumbersPath))->openFile('rb+');
        if (!$file->flock(LOCK_EX)) {
            throw new IOException('Cannot get the lock for '.$this->transNumbersPath);
        }

        $count = (int) $file->fread(6);
        ++$count;
        if ($count < 0 || $count > 899999) {
            $count = 0;
        }

        $file->seek(0); // back to the begining
        $file->ftruncate(0);
        $file->fwrite($count);
        $file->flock(LOCK_UN);

        return sprintf('%06d', $count);
    }

    /**
     * @param TransactionCustomer $customer
     */
    protected function computeCustomerFields(TransactionCustomer $customer = null)
    {
        if ($customer === null) {
            return;
        }
        $this->fields = array_merge($this->fields, [
            'vads_cust_email' => $customer->getEmail(),
            'vads_cust_id' => $customer->getCustomerId(),
            'vads_cust_status' => $customer->getStatus(),
            'vads_cust_title' => $customer->getTitle(),
            'vads_cust_first_name' => $customer->getFirstname(),
            'vads_cust_last_name' => $customer->getLastname(),
            'vads_cust_legal_name' => $customer->getLegalName(),
            'vads_cust_address_number' => $customer->getStreetNumber(),
            'vads_cust_address' => $customer->getAddress(),
            'vads_cust_zip' => $customer->getPostalCode(),
            'vads_cust_city' => $customer->getCity(),
            'vads_cust_state' => $customer->getState(),
            'vads_cust_country' => $customer->getCountry(),
            'vads_cust_phone' => $customer->getPhone(),
        ]);
    }

    /**
     * @param TransactionShipping|null $shipping
     */
    protected function computeShippingFields(TransactionShipping $shipping = null)
    {
        if ($shipping === null) {
            return;
        }

        $this->fields = array_merge($this->fields, [
            'vads_ship_to_status' => $shipping->getStatus(),
            'vads_ship_to_first_name' => $shipping->getFirstname(),
            'vads_ship_to_last_name' => $shipping->getLastname(),
            'vads_ship_to_legal_name' => $shipping->getLegalName(),
            'vads_ship_to_phone_num' => $shipping->getPhone(),
            'vads_ship_to_street_number' => $shipping->getStreetNumber(),
            'vads_ship_to_street' => $shipping->getAddress(),
            'vads_ship_to_street2' => $shipping->getComplementaryAddress(),
            'vads_ship_to_zip' => $shipping->getPostalCode(),
            'vads_ship_to_city' => $shipping->getCity(),
            'vads_ship_to_state' => $shipping->getState(),
            'vads_ship_to_country' => $shipping->getCountry(),
        ]);
    }

    /**
     * @param TransactionProduct[]|Collection $products
     */
    protected function computeProductsFields(Collection $products)
    {
        if ($products->count() === 0) {
            return;
        }
        $this->fields['vads_nb_products'] = $products->count();
        $cc = 0;
        /** @var TransactionProduct $product */
        foreach ($products as $product) {
            $this->fields = array_merge($this->fields, [
                'vads_product_label'.$cc => $product->getLabel(),
                'vads_product_amount'.$cc => $product->getAmount(),
                'vads_product_type'.$cc => $product->getType(),
                'vads_product_ref'.$cc => $product->getRef(),
                'vads_product_qty'.$cc => $product->getQuantity(),
                'vads_product_vat'.$cc => $product->getVat(),
            ]);
            ++$cc;
        }
    }

    /**
     * @param Transaction $transaction
     */
    protected function computeSubscriptionFields(Transaction $transaction)
    {
        $subscriptionInfos = $transaction->getSubscriptionInfos();
        if ($subscriptionInfos === null) {
            return;
        }

        $this->fields['vads_sub_amount'] = $subscriptionInfos->getAmount();
        $this->fields['vads_sub_currency'] = $transaction->getCurrency();
        $this->fields['vads_sub_effect_date'] = $subscriptionInfos->getBeginDate()->format('Ymd');

        $rule = 'RRULE:FREQ=';
        switch ($subscriptionInfos->getFrequency()) {
            case SubscriptionInfos::FREQ_DAY:
                $rule .= 'DAILY;';
                break;
            case SubscriptionInfos::FREQ_WEEK:
                $rule .= 'WEEKLY;';
                break;
            case SubscriptionInfos::FREQ_MONTH:
                $rule .= 'MONTHLY;';
                break;
            case SubscriptionInfos::FREQ_YEAR:
                $rule .= 'YEARLY;';
                break;
        }
        if (in_array($subscriptionInfos->getFrequency(), [SubscriptionInfos::FREQ_YEAR, SubscriptionInfos::FREQ_MONTH], true)) {
            $byMonthDay = (string) $subscriptionInfos->getMonthDay();
            for ($i = $subscriptionInfos->getMonthDay() - 1; $i >= 28; --$i) {
                $byMonthDay = $i.','.$byMonthDay;
            }
            $rule .= "BYMONTHDAY={$byMonthDay};";
        }
        if ($subscriptionInfos->getCount() > 0) {
            $rule .= "COUNT={$subscriptionInfos->getCount()};";
        }
        $rule .= "INTERVAL={$subscriptionInfos->getInterval()};";
        if ($subscriptionInfos->getEndDate() !== null) {
            $rule .= 'UNTIL='.$subscriptionInfos->getEndDate()->format('Ymd').';';
        }
        $this->fields['vads_sub_desc'] = $rule;
    }

    /**
     * Fills fields of the form from a transaction.
     *
     * @param Transaction $transaction (not modified)
     */
    public function computeFields(Transaction $transaction)
    {
        $validAlias = $transaction->isValidAlias();
        $action = 'PAYMENT';
        switch ($transaction->getType()) {
            case Transaction::TYPE_PAYMENT:
                $action = 'PAYMENT';
                if (!$validAlias) {
                    $action = 'REGISTER_PAY';
                }
                break;
            case Transaction::TYPE_SUBSCRIBE:
                $action = 'SUBSCRIBE';
                if (!$validAlias) {
                    $action = 'REGISTER_SUBSCRIBE';
                }
                break;
            case Transaction::TYPE_PAYMENT_SUBSCRIBE:
                $action = 'REGISTER_PAY_SUBSCRIBE';
                break;
        }

        /**
         * vads_page_action {@see https://payzen.io/fr-FR/form-payment/standard-payment/vads-page-action.html}.
         */
        $this->fields = [
            'vads_version' => 'V2',
            'vads_page_action' => $action,
            'vads_action_mode' => 'INTERACTIVE', /** @see https://payzen.io/fr-FR/form-payment/subscription-token/vads-action-mode.html */
            'vads_payment_config' => 'SINGLE',
            'vads_site_id' => $this->siteId,
            'vads_capture_delay' => 0,
            'vads_return_mode' => 'POST',
            'vads_url_return' => $this->router->generate('ioni_payzen_payment_return',
                [], UrlGeneratorInterface::ABSOLUTE_URL
            ),
            'vads_url_check' => $this->router->generate('ioni_payzen_instant_payment_notification',
                [], UrlGeneratorInterface::ABSOLUTE_URL
            ),
            'vads_ctx_mode' => $this->signatureHandler->getCtxMode(),
            'vads_currency' => $transaction->getCurrency(),
            'vads_trans_date' => $transaction->getUtcCreatedAt()->format('YmdHis'),
            'vads_trans_id' => $transaction->getNumber(),
            'vads_order_id' => $transaction->getId(), // A transaction should be linked oneToOne to an order
        ];
        if ($transaction->getType() !== Transaction::TYPE_SUBSCRIBE) {
            $this->fields['vads_amount'] = $transaction->getAmount();
        }
        if ($validAlias) {
            $this->fields['vads_identifier'] = $transaction->getAlias()->getIdentifier();
        }
        $this->computeCustomerFields($transaction->getCustomer());
        $this->computeShippingFields($transaction->getShipping());
        $this->computeProductsFields($transaction->getProducts());
        $this->computeSubscriptionFields($transaction);
        $this->signature = $this->signatureHandler->compute($this->fields);
    }

    /**
     * @return TransactionView
     */
    public function createView(): TransactionView
    {
        $view = new TransactionView();
        $view->signature = $this->signature;
        $view->fields = $this->fields;

        return $view;
    }

    /**
     * Set SiteId.
     *
     * @param string $siteId Identifier of the shop
     */
    public function setSiteId(string $siteId)
    {
        $this->siteId = $siteId;
    }

    /**
     * Set TransNumbersPath.
     *
     * @param string $transNumbersPath
     */
    public function setTransNumbersPath(string $transNumbersPath)
    {
        $this->transNumbersPath = $transNumbersPath;
    }
}
