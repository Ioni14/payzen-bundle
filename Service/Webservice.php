<?php

namespace Ioni\PayzenBundle\Service;

use Ioni\PayzenBundle\Model\Transaction;

/**
 * Class Webservice.
 *
 * @author Thomas Talbot <talbot.thomas14@gmail.com>
 */
class Webservice
{
    /**
     * Identifier of the shop.
     *
     * @var string
     */
    private $siteId;

    /**
     * @var SignatureHandler
     */
    private $signatureHandler;

    /**
     * Client SOAP.
     *
     * @var \SoapClient
     */
    private $soapClient;

    /**
     * Namespace for Soap elements.
     *
     * @var string
     */
    private $namespace;

    /**
     * Webservice constructor.
     *
     * @param string           $wsdl
     * @param SignatureHandler $signatureHandler
     */
    public function __construct(string $wsdl, SignatureHandler $signatureHandler)
    {
        $this->soapClient = new \SoapClient($wsdl, [
            'soap_version' => SOAP_1_2,
            'encoding' => 'UTF-8',
        ]);
        $this->signatureHandler = $signatureHandler;
    }

    /**
     * Generates an UUID.
     *
     * @see https://en.wikipedia.org/wiki/Universally_unique_identifier
     *
     * @return string the UUID
     */
    protected function genUUID(): string
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            random_int(0, 0xffff), random_int(0, 0xffff),
            random_int(0, 0xffff),
            random_int(0, 0x0fff) | 0x4000,
            random_int(0, 0x3fff) | 0x8000,
            random_int(0, 0xffff), random_int(0, 0xffff), random_int(0, 0xffff)
        );
    }

    /**
     * Utility function, generates the PayZen authToken.
     *
     * @param string $requestId, UUID for the request
     * @param string $timestamp, timestamp of the request
     * @param string $format     ("request" or "response"), the expected format of the authToken
     *
     * @return string the authToken
     */
    protected function buildAuthToken(string $requestId, string $timestamp, $format = 'request'): string
    {
        // the request's authToken must be based on $requestId.$timeStamp
        // the response's authToken must be based on $timeStamp.$requestId
        $data = ($format === 'request') ? $requestId.$timestamp : $timestamp.$requestId;
        $cert = $this->signatureHandler->getCertificate();

        return base64_encode(hash_hmac('sha256', $data, $cert, true));
    }

    /**
     * @return array
     */
    protected function genSoapHeaders(): array
    {
        $timestamp = (new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d\TH:i:s\Z');
        $requestId = $this->genUUID();

        $paramHeaders = [
            'shopId' => $this->siteId,
            'requestId' => $requestId,
            'timestamp' => $timestamp,
            'mode' => $this->signatureHandler->getCtxMode(),
            'authToken' => $this->buildAuthToken($requestId, $timestamp),
        ];

        $soapHeaders = [];
        foreach ($paramHeaders as $header => $value) {
            $soapHeaders[] = new \SOAPHeader($this->namespace, $header, $value);
        }

        return $soapHeaders;
    }

    /**
     * @param Transaction $transaction
     *
     * @return bool true if no errors
     */
    public function cancelSubscription(Transaction $transaction): bool
    {
        if ($transaction->getSubscriptionInfos() === null || $transaction->getAlias() === null) {
            throw new \InvalidArgumentException("The transaction {$transaction->getId()} has no subscription infos or alias.");
        }
        $this->soapClient->__setSoapHeaders($this->genSoapHeaders());

        $cancelSubscriptionParams = (object) [
            'commonRequest' => [
                'submissionDate' => (new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d\TH:i:s\Z'),
            ],
            'queryRequest' => [
                'paymentToken' => $transaction->getAlias()->getIdentifier(),
                'subscriptionId' => $transaction->getSubscriptionInfos()->getIdentifier(),
            ],
        ];

        $response = $this->soapClient->__soapCall('cancelSubscription', [$cancelSubscriptionParams]);

        return $response->cancelSubscriptionResult->commonResponse->responseCode === 0;
    }

    /**
     * Set SiteId.
     *
     * @param string $siteId Identifier of the shop
     */
    public function setSiteId(string $siteId)
    {
        $this->siteId = $siteId;
    }

    /**
     * Set Namespace.
     *
     * @param string $namespace Namespace of Payzen Soap headers
     */
    public function setNamespace(string $namespace)
    {
        $this->namespace = $namespace;
    }
}
