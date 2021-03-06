<?php

namespace Ioni\PayzenBundle\Controller;

use Ioni\PayzenBundle\Exception\CorruptedPaymentNotificationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        $this->get('logger')->addInfo('[IoniPayzenBundle] returnAction', ['request_params' => $request->request->all()]);

        $returnRoute = $this->getParameter('payzen_return_route');
        if ($returnRoute !== null) {
            return $this->redirectToRoute($returnRoute, ['transactionId' => $request->request->get('vads_order_id')]);
        }

        return new JsonResponse(null, 204);
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
        $this->get('logger')->addInfo('[IoniPayzenBundle] paymentNotificationAction', ['request_params' => $request->request->all()]);

        $handler = $this->get('ioni_payzen.payment_notification_handler');
        try {
            $handler->handle($request);
        } catch (CorruptedPaymentNotificationException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }

        return new JsonResponse(null, 204);
    }
}
