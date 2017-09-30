<?php

namespace Ioni\PayzenBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class TransactionCustomer.
 * Represents a customer into a Payzen transaction. Stores all Payzen properties for a customer.
 *
 * @author Thomas Talbot <talbot.thomas14@gmail.com>
 */
class TransactionCustomer extends TransactionIdentifiableData
{
    /**
     * Customer identifier in order to retrieve him from the response.
     * Example : user's id.
     *
     * @var string
     *
     * @Assert\Length(max="63")
     */
    protected $customerId;

    /**
     * Civility (Mr, Mrs...)
     *
     * @var string
     *
     * @Assert\Length(max="63")
     */
    protected $title;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(max="150")
     */
    protected $email;

    /**
     * Get CustomerId.
     *
     * @return string
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * Set CustomerId.
     *
     * @param string $customerId
     */
    public function setCustomerId(string $customerId)
    {
        $this->customerId = $customerId;
    }

    /**
     * Get Title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set Title.
     *
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * Get Email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set Email.
     *
     * @param string $email
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }
}
