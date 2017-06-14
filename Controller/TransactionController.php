<?php

namespace Ioni\PayzenBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TransactionController.
 * Allows to discuss with the payment page.
 *
 * @author Thomas Talbot <talbot.thomas14@gmail.com>
 */
class TransactionController extends Controller
{
    /**
     * Back from the payment page.
     * Gives some informations of the payment tunnel : success, cancelled...
     *
     * @param Request $request
     *
     * @return Response
     */
    public function returnAction(Request $request): Response
    {
        $this->get('logger')->addDebug('[returnAction] request="'.json_encode($request->request).'"');

        return new Response('', 204);
    }

    /**
     * Allows to handle the response of the payment.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function paymentNotificationAction(Request $request): Response
    {
        $this->get('logger')->addDebug('[paymentNotificationAction] request="'.json_encode($request->request).'"');

        $handler = $this->get('ioni_payzen.payment_notification_handler');
        $handler->handle($request);

        return new Response('', 204);
    }
}
