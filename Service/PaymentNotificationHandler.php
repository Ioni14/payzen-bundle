<?php

namespace Ioni\PayzenBundle\Service;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class PaymentNotificationHandler.
 * Handles the payment responses from Payzen.
 *
 * @author Thomas Talbot <talbot.thomas14@gmail.com>
 */
class PaymentNotificationHandler
{
    /**
     * PaymentNotificationHandler constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param Request $request
     */
    public function handle(Request $request)
    {
        // https://payzen.io/fr-FR/form-payment/standard-payment/gerer-le-dialogue-vers-le-site-marchand.html

        // $this->signatureHandler->isEquals($request['signature'], $fields);

        // doublon notification ?

        // traitement erreurs

        // dispatches
    }
}
