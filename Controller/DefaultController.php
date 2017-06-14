<?php

namespace Ioni\PayzenBundle\Controller;

use Ioni\PayzenBundle\Model\Transaction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $transaction = new Transaction();
        $transaction->setCurrency('978');
        $transaction->setAmount(15050);

        $formFieldsGenerator = $this->get('ioni_payzen.form_fields_generator');
        $formFieldsGenerator->computeFields($transaction);
        dump($formFieldsGenerator);

        return $this->render('IoniPayzenBundle:Default:index.html.twig', [
            'transaction' => $transaction,
            'transactionView' => $formFieldsGenerator->createView(),
        ]);
    }
}
