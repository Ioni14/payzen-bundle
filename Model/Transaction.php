<?php

namespace Ioni\PayzenBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ioni\PayzenBundle\Validator\Constraints\CurrencyCode;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Transaction.
 * Represents a Payzen transaction. Stores all Payzen properties for a payment.
 *
 * @author Thomas Talbot <talbot.thomas14@gmail.com>
 */
class Transaction
{
    const STATUS_WAITING = 'waiting';
    const STATUS_COMPLETE = 'complete';
    const STATUSES = [self::STATUS_WAITING, self::STATUS_COMPLETE];

    /**
     * Total amount in the smallest currency unit.
     *
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\GreaterThan(value="0")
     */
    protected $amount;

    /**
     * Numeric code ISO 4217.
     *
     * @var string
     *
     * @see https://fr.iban.com/currency-codes.html List of currency codes ISO 4217.
     * @see https://payzen.io/en-EN/form-payment/standard-payment/vads-currency.html The supported currency codes.
     *
     * @CurrencyCode()
     */
    protected $currency;

    /**
     * 6 digits from 000000 to 899999.
     * Must be unique on one day.
     *
     * @var string
     *
     * @Assert\Length(min="6", max="6")
     * @Assert\Regex(pattern="/^[0-8][0-9]{5}$/")
     */
    protected $number;

    /**
     * In order to retrieve the order after the Payzen response.
     *
     * @var string
     */
    protected $orderId;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var TransactionCustomer;
     */
    protected $customer;

    /**
     * @var TransactionShipping
     */
    protected $shipping;

    /**
     * @var TransactionProduct[]|Collection
     */
    protected $products;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * Transaction constructor.
     */
    public function __construct()
    {
        $this->amount = 0;
        $this->status = self::STATUS_WAITING;
        $this->customer = new TransactionCustomer();
        $this->products = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * Get amount.
     *
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * Total amount in the smallest currency unit.
     *
     * @param int $amount
     */
    public function setAmount(int $amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return string|null
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency(string $currency)
    {
        $this->currency = $currency;
    }

    /**
     * Get OrderId.
     *
     * @return string|null
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set OrderId.
     *
     * @param string $orderId
     */
    public function setOrderId(string $orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * Get Number.
     *
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * Set Number.
     *
     * @param string $number
     */
    public function setNumber(string $number)
    {
        $this->number = $number;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status)
    {
        if (in_array($this->status, self::STATUSES, true)) {
            $this->status = $status;
        }
    }

    /**
     * Get Customer.
     *
     * @return TransactionCustomer
     */
    public function getCustomer(): TransactionCustomer
    {
        return $this->customer;
    }

    /**
     * Set Customer.
     *
     * @param TransactionCustomer $customer
     */
    public function setCustomer(TransactionCustomer $customer)
    {
        $this->customer = $customer;
    }

    /**
     * Get Shipping.
     *
     * @return TransactionShipping|null
     */
    public function getShipping()
    {
        return $this->shipping;
    }

    /**
     * Set Shipping.
     *
     * @param TransactionShipping $shipping
     */
    public function setShipping(TransactionShipping $shipping)
    {
        $this->shipping = $shipping;
    }

    /**
     * Get Products.
     *
     * @return Collection
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    /**
     * Add Product.
     *
     * @param TransactionProduct $product
     */
    public function addProduct(TransactionProduct $product)
    {
        $this->products->add($product);
    }

    /**
     * Remove Product.
     *
     * @param TransactionProduct $product
     */
    public function removeProduct(TransactionProduct $product)
    {
        $this->products->removeElement($product);
    }

    /**
     * Get CreatedAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * Get CreatedAt in UTC timezone.
     * Does not modify $createdAt.
     *
     * @return \DateTime
     */
    public function getUtcCreatedAt(): \DateTime
    {
        $date = clone $this->createdAt;
        $date->setTimezone(new \DateTimeZone('UTC'));

        return $date;
    }

    /**
     * Set CreatedAt.
     *
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get UpdatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * Set UpdatedAt.
     *
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }
}
