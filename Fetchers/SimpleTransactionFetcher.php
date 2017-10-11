<?php

namespace Ioni\PayzenBundle\Fetchers;

use Doctrine\Common\Persistence\ManagerRegistry;
use Ioni\PayzenBundle\Exception\TransactionNotFoundException;
use Ioni\PayzenBundle\Model\Transaction;

/**
 * Class SimpleTransactionFetcher.
 * Represents a simple implementation of TransactionFetcherInterface.
 *
 * @author Thomas Talbot <talbot.thomas14@gmail.com>
 */
final class SimpleTransactionFetcher implements TransactionFetcherInterface
{
    /**
     * @var ManagerRegistry
     */
    private $registry;

    /**
     * SimpleTransactionFetcher constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Retrieves a Transaction with its base repository.
     *
     * {@inheritdoc}
     */
    public function findTransaction($id): Transaction
    {
        $transaction = $this->registry->getRepository('IoniPayzenBundle:Transaction')->find($id);
        if (!$transaction instanceof Transaction) {
            throw new TransactionNotFoundException($id);
        }

        return $transaction;
    }
}
