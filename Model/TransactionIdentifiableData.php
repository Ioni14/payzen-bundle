<?php

namespace Ioni\PayzenBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class TransactionIdentifiableData.
 * Represents the identifiable informations of a person.
 *
 * @author Thomas Talbot <talbot.thomas14@gmail.com>
 */
abstract class TransactionIdentifiableData
{
    const STATUS_PRIVATE = 'PRIVATE';
    const STATUS_COMPANY = 'COMPANY';
    const STATUSES = [self::STATUS_PRIVATE, self::STATUS_COMPANY];

    /**
     * Is a company or a private person ?
     *
     * @var string
     */
    protected $status;

    /**
     * @var string
     *
     * @Assert\Length(max="63")
     */
    protected $firstname;

    /**
     * @var string
     *
     * @Assert\Length(max="63")
     */
    protected $lastname;

    /**
     * @var string
     *
     * @Assert\Length(max="100")
     */
    protected $legalName;

    /**
     * @var string
     *
     * @Assert\Length(max="5")
     */
    protected $streetNumber;

    /**
     * @var string
     *
     * @Assert\Length(max="255")
     */
    protected $address;

    /**
     * Postal or Zip code.
     *
     * @var string
     *
     * @Assert\Length(max="64")
     */
    protected $postalCode;

    /**
     * @var string
     *
     * @Assert\Length(max="128")
     */
    protected $city;

    /**
     * State or region.
     *
     * @var string
     *
     * @Assert\Length(max="127")
     */
    protected $state;

    /**
     * Country code in ISO 3166.
     *
     * @var string
     *
     * @see http://data.okfn.org/data/core/country-list List of all country codes.
     *
     * @Assert\Length(min="2", max="2")
     */
    protected $country;

    /**
     * @var string
     *
     * @Assert\Length(max="32")
     */
    protected $phone;

    /**
     * Payment constructor.
     */
    public function __construct()
    {
        $this->status = self::STATUS_PRIVATE;
    }

    /**
     * Get Status.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set Status.
     *
     * @param string $status
     */
    public function setStatus(string $status)
    {
        $this->status = $status;
        if (!in_array($this->status, self::STATUSES, true)) {
            $this->status = self::STATUS_PRIVATE;
        }
    }

    /**
     * Get Firstname.
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set Firstname.
     *
     * @param string $firstname
     */
    public function setFirstname(string $firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * Get Lastname.
     *
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set Lastname.
     *
     * @param mixed $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * Get LegalName.
     *
     * @return string
     */
    public function getLegalName()
    {
        return $this->legalName;
    }

    /**
     * Set LegalName.
     *
     * @param string $legalName
     */
    public function setLegalName(string $legalName)
    {
        $this->legalName = $legalName;
    }

    /**
     * Get StreetNumber.
     *
     * @return string
     */
    public function getStreetNumber()
    {
        return $this->streetNumber;
    }

    /**
     * Set StreetNumber.
     *
     * @param string $streetNumber
     */
    public function setStreetNumber(string $streetNumber)
    {
        $this->streetNumber = $streetNumber;
    }

    /**
     * Get Address.
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set Address.
     *
     * @param string $address
     */
    public function setAddress(string $address)
    {
        $this->address = $address;
    }

    /**
     * Get PostalCode.
     *
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set PostalCode.
     *
     * @param string $postalCode
     */
    public function setPostalCode(string $postalCode)
    {
        $this->postalCode = $postalCode;
    }

    /**
     * Get City.
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set City.
     *
     * @param string $city
     */
    public function setCity(string $city)
    {
        $this->city = $city;
    }

    /**
     * Get State.
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set State.
     *
     * @param string $state
     */
    public function setState(string $state)
    {
        $this->state = $state;
    }

    /**
     * Get Country.
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set Country.
     *
     * @param string $country
     */
    public function setCountry(string $country)
    {
        $this->country = $country;
    }

    /**
     * Get Phone.
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set Phone.
     *
     * @param string $phone
     */
    public function setPhone(string $phone)
    {
        $this->phone = $phone;
    }
}
