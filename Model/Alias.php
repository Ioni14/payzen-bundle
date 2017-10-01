<?php

namespace Ioni\PayzenBundle\Model;

/**
 * Class Alias.
 * Represents an alias created by Payzen.
 *
 * @author Thomas Talbot <talbot.thomas14@gmail.com>
 */
class Alias
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $email;

    /**
     * @see https://payzen.io/fr-FR/form-payment/subscription-token/vads-card-brand.html
     *
     * @var string
     */
    protected $cardType;

    /**
     * @var string
     */
    protected $cardNumber;

    /**
     * @var int
     */
    protected $expiryMonth;

    /**
     * @var int
     */
    protected $expiryYear;

    /**
     * @return null|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param null|string $identifier
     */
    public function setIdentifier(string $identifier = null)
    {
        $this->identifier = $identifier;
    }

    /**
     * @return null|string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param null|string $email
     */
    public function setEmail(string $email = null)
    {
        $this->email = $email;
    }

    /**
     * @return null|string
     */
    public function getCardType()
    {
        return $this->cardType;
    }

    /**
     * @param null|string $cardType
     */
    public function setCardType(string $cardType = null)
    {
        $this->cardType = $cardType;
    }

    /**
     * @return null|string
     */
    public function getCardNumber()
    {
        return $this->cardNumber;
    }

    /**
     * @param null|string $cardNumber
     */
    public function setCardNumber(string $cardNumber = null)
    {
        $this->cardNumber = $cardNumber;
    }

    /**
     * @return null|int
     */
    public function getExpiryMonth()
    {
        return $this->expiryMonth;
    }

    /**
     * @param null|int $expiryMonth
     */
    public function setExpiryMonth(int $expiryMonth = null)
    {
        $this->expiryMonth = $expiryMonth;
    }

    /**
     * @return null|int
     */
    public function getExpiryYear()
    {
        return $this->expiryYear;
    }

    /**
     * @param null|int $expiryYear
     */
    public function setExpiryYear(int $expiryYear = null)
    {
        $this->expiryYear = $expiryYear;
    }
}
