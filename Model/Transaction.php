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
    const STATUS_REJECTED = 'rejected';
    const STATUS_SUCCEEDED = 'succeeded';
    const STATUSES = [self::STATUS_WAITING, self::STATUS_REJECTED, self::STATUS_SUCCEEDED];

    /**
     * @var mixed
     */
    protected $id;

    /**
     * Total amount in the smallest currency unit.
     * @see https://payzen.io/en-EN/form-payment/standard-payment/vads-amount.html
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
     * @see https://payzen.io/en-EN/form-payment/standard-payment/vads-trans-id.html
     *
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="6", max="6")
     * @Assert\Regex(pattern="/^[0-8][0-9]{5}$/")
     */
    protected $number;

    /**
     * @var string
     */
    protected $status;

    /**
     * field vads_auth_result from Payzen
     * @see https://payzen.io/en-EN/form-payment/standard-payment/vads-auth-result.html
     *
     * @var string
     */
    protected $resultCode;

    /**
     * the fields returned by the platform.
     *
     * @var array
     */
    protected $response;

    /**
     * @var TransactionCustomer;
     *
     * @Assert\Valid()
     */
    protected $customer;

    /**
     * @var TransactionShipping
     *
     * @Assert\Valid()
     */
    protected $shipping;

    /**
     * @var TransactionProduct[]|Collection
     *
     * @Assert\Valid()
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
        $this->response = [];
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->number = '000000';
    }

    /**
     * Get Id.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get Amount.
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
     * Get Currency.
     *
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
     * @return string|null
     */
    public function getResultCode()
    {
        return $this->resultCode;
    }

    /**
     * @param string $resultCode
     */
    public function setResultCode(string $resultCode)
    {
        $this->resultCode = $resultCode;
    }

    /**
     * @return array|null
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param array|null $response
     */
    public function setResponse(array $response = null)
    {
        $this->response = $response;
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
     * @return TransactionProduct[]|Collection
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
        if ($product->getTransaction() !== $this) {
            $product->setTransaction($this);
        }
    }

    /**
     * Remove Product.
     *
     * @param TransactionProduct $product
     */
    public function removeProduct(TransactionProduct $product)
    {
        $this->products->removeElement($product);
        if ($product->getTransaction() !== null) {
            $product->setTransaction(null);
        }
    }

    /**
     * Sums the quantity*amount of products
     *
     * @return int
     */
    public function computeProductsTotalTTC(): int
    {
        $ttc = 0;
        foreach ($this->products as $product) {
            $ttc += $product->getAmount() * $product->getQuantity();
        }

        return $ttc;
    }

    /**
     * Get CreatedAt.
     *
     * @return \DateTimeInterface
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Get CreatedAt in UTC timezone.
     * Does not modify $createdAt.
     *
     * @return \DateTimeInterface
     */
    public function getUtcCreatedAt(): \DateTimeInterface
    {
        $date = clone $this->createdAt;
        $date->setTimezone(new \DateTimeZone('UTC'));

        return $date;
    }

    /**
     * Set CreatedAt.
     *
     * @param \DateTimeInterface $createdAt
     */
    public function setCreatedAt(\DateTimeInterface $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get UpdatedAt.
     *
     * @return \DateTimeInterface
     */
    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * Set UpdatedAt.
     *
     * @param \DateTimeInterface $updatedAt
     */
    public function setUpdatedAt(\DateTimeInterface $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }
}
