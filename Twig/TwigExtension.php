<?php

namespace Ioni\PayzenBundle\Twig;

use Ioni\PayzenBundle\Model\Transaction;
use Ioni\PayzenBundle\Model\TransactionView;

/**
 * Class TwigExtension.
 * Renders Transaction forms.
 *
 * @author Thomas Talbot <talbot.thomas14@gmail.com>
 */
class TwigExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('renderTransactionForm', [$this, 'renderTransactionForm'], [
                'needs_environment' => true,
                'is_safe' => ['html'],
            ]),
        ];
    }

    /**
     * @param \Twig_Environment $env
     * @param TransactionView   $transactionView
     *
     * @return string the rendered html
     */
    public function renderTransactionForm(\Twig_Environment $env, TransactionView $transactionView): string
    {
        return $env->render('@IoniPayzen/Transaction/form_transaction.html.twig', [
            'fields' => $transactionView->fields,
            'signature' => $transactionView->signature,
        ]);
    }
}
