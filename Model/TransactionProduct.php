<?php

namespace Ioni\PayzenBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class TransactionProduct.
 * Represents a product in a Payzen transaction. Stores all Payzen properties for a product.
 *
 * @author Thomas Talbot <talbot.thomas14@gmail.com>
 */
class TransactionProduct
{
    const TYPE_FOOD_AND_GROCERY = 'FOOD_AND_GROCERY';
    const TYPE_AUTOMOTIVE = 'AUTOMOTIVE';
    const TYPE_ENTERTAINMENT = 'ENTERTAINMENT';
    const TYPE_HOME_AND_GARDEN = 'HOME_AND_GARDEN';
    const TYPE_HOME_APPLIANCE = 'HOME_APPLIANCE';
    const TYPE_AUCTION_AND_GROUP_BUYING = 'AUCTION_AND_GROUP_BUYING';
    const TYPE_FLOWERS_AND_GIFTS = 'FLOWERS_AND_GIFTS';
    const TYPE_COMPUTER_AND_SOFTWARE = 'COMPUTER_AND_SOFTWARE';
    const TYPE_HEALTH_AND_BEAUTY = 'HEALTH_AND_BEAUTY';
    const TYPE_SERVICE_FOR_INDIVIDUAL = 'SERVICE_FOR_INDIVIDUAL';
    const TYPE_SERVICE_FOR_BUSINESS = 'SERVICE_FOR_BUSINESS';
    const TYPE_SPORTS = 'SPORTS';
    const TYPE_CLOTHING_AND_ACCESSORIES = 'CLOTHING_AND_ACCESSORIES';
    const TYPE_TRAVEL = 'TRAVEL';
    const TYPE_HOME_AUDIO_PHOTO_VIDEO = 'HOME_AUDIO_PHOTO_VIDEO';
    const TYPE_TELEPHONY = 'TELEPHONY';

    /**
     * @see https://payzen.io/en-EN/form-payment/subscription-token/vads-product-typen.html List of all types.
     */
    const TYPES = [
        self::TYPE_FOOD_AND_GROCERY, self::TYPE_AUTOMOTIVE, self::TYPE_ENTERTAINMENT,
        self::TYPE_HOME_AND_GARDEN, self::TYPE_HOME_APPLIANCE, self::TYPE_AUCTION_AND_GROUP_BUYING,
        self::TYPE_FLOWERS_AND_GIFTS, self::TYPE_COMPUTER_AND_SOFTWARE, self::TYPE_HEALTH_AND_BEAUTY,
        self::TYPE_SERVICE_FOR_INDIVIDUAL, self::TYPE_SERVICE_FOR_BUSINESS, self::TYPE_SPORTS,
        self::TYPE_CLOTHING_AND_ACCESSORIES, self::TYPE_TRAVEL, self::TYPE_HOME_AUDIO_PHOTO_VIDEO,
        self::TYPE_TELEPHONY,
    ];

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $label;

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
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $ref;

    /**
     * @var int
     *
     * @Assert\GreaterThan(value="0")
     */
    protected $quantity;

    /**
     * Between 0% and 100%.
     *
     * @var float
     *
     * @Assert\Range(min="0", max="100")
     */
    protected $vat;

    /**
     * @var Transaction
     */
    protected $transaction;

    /**
     * TransactionProduct constructor.
     *
     * @param Transaction|null $transaction
     */
    public function __construct(Transaction $transaction = null)
    {
        $this->amount = 0;
        $this->quantity = 1;
        $this->vat = 0.0;
        $this->transaction = $transaction;
    }

    /**
     * @return null|mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function computeHT(): int
    {
        return (int) ($this->amount / (1 + $this->vat / 100.0));
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
     * Get Label.
     *
     * @return string|null
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set Label.
     *
     * @param string $label
     */
    public function setLabel(string $label)
    {
        $this->label = $label;
    }

    /**
     * Get Type.
     *
     * @return string|null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set Type.
     *
     * @param string $type
     */
    public function setType(string $type)
    {
        if (!in_array($type, self::TYPES, true)) {
            return;
        }
        $this->type = $type;
    }

    /**
     * Get Ref.
     *
     * @return string|null
     */
    public function getRef()
    {
        return $this->ref;
    }

    /**
     * Set Ref.
     *
     * @param string $ref
     */
    public function setRef(string $ref)
    {
        $this->ref = $ref;
    }

    /**
     * Get Quantity.
     *
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * Set Quantity.
     *
     * @param int $quantity
     */
    public function setQuantity(int $quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * Get Vat.
     *
     * @return float [0, +infinity[
     */
    public function getVat(): float
    {
        return $this->vat;
    }

    /**
     * Set Vat.
     *
     * @param float $vat
     */
    public function setVat(float $vat)
    {
        $this->vat = $vat;
        if ($this->vat < 0) {
            $this->vat = 0.0;
        }
    }

    /**
     * @return null|Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * @param null|Transaction $transaction
     */
    public function setTransaction(Transaction $transaction = null)
    {
        $this->transaction = $transaction;
        if ($transaction !== null && !$transaction->getProducts()->contains($this)) {
            $transaction->addProduct($this);
        }
    }
}
