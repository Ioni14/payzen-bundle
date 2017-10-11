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
     * @param $id
     *
     * @return Transaction
     */
    public function findTransaction($id): Transaction;
}
