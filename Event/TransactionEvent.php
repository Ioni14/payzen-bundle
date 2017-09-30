<?php

namespace Ioni\PayzenBundle\Event;

use Ioni\PayzenBundle\Model\Transaction;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class TransactionUnfoundEvent.
 * Dispatched when a notification payment is handled.
 *
 * @author Thomas Talbot <talbot.thomas14@gmail.com>
 */
class TransactionEvent extends Event
{
    const REJECTED_EVENT = 'ioni_payzen.transaction.rejected';
    const SUCCEEDED_EVENT = 'ioni_payzen.transaction.succeeded';
    const UNFOUND_EVENT = 'ioni_payzen.transaction.unfound';

    /**
     * @var Transaction
     */
    protected $transaction;

    /**
     * TransactionCorruptedEvent constructor.
     *
     * @param Transaction $transaction
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * @return Transaction
     */
    public function getTransaction(): Transaction
    {
        return $this->transaction;
    }
}
