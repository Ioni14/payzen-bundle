<?php

namespace Ioni\PayzenBundle\Exception;

/**
 * Class TransactionNotFoundException.
 *
 * @author Thomas Talbot <talbot.thomas14@gmail.com>
 */
class TransactionNotFoundException extends \UnexpectedValueException
{
    public function __construct($transactionId)
    {
        parent::__construct("The transaction \"{$transactionId}\" was not found.");
    }
}
