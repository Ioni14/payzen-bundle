<?php

namespace Ioni\PayzenBundle\Fetchers;

use Ioni\PayzenBundle\Model\Transaction;

/**
 * Interface TransactionFetcherInterface.
 *
 * @author Thomas Talbot <talbot.thomas14@gmail.com>
 */
interface TransactionFetcherInterface
{
    /**
     * @param mixed $id the transaction ID
     * @param array $responseFields all Payzen response fields
     *
     * @return Transaction
     */
    public function findTransaction($id, array $responseFields = []): Transaction;
}
