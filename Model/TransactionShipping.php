<?php

namespace Ioni\PayzenBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class TransactionShipping.
 * Represents the shipping details of a Payzen transaction. Stores all Payzen properties for a shipping.
 *
 * @author Thomas Talbot <talbot.thomas14@gmail.com>
 */
class TransactionShipping extends TransactionIdentifiableData
{
    /**
     * @var string
     *
     * @Assert\Length(max="255")
     */
    protected $complementaryAddress;

    /**
     * Get ComplementaryAddress.
     *
     * @return string
     */
    public function getComplementaryAddress()
    {
        return $this->complementaryAddress;
    }

    /**
     * Set ComplementaryAddress.
     *
     * @param string $complementaryAddress
     */
    public function setComplementaryAddress(string $complementaryAddress)
    {
        $this->complementaryAddress = $complementaryAddress;
    }
}
