<?php

namespace Ioni\PayzenBundle\Service;

/**
 * Class SignatureHandler.
 * Computes the signature of fields and verifies its integrity.
 *
 * @author Thomas Talbot <talbot.thomas14@gmail.com>
 */
class SignatureHandler
{
    const MODE_PROD = 'PRODUCTION';
    const MODE_TEST = 'TEST';
    const MODES = [self::MODE_TEST, self::MODE_PROD];

    /**
     * Mode of the transaction : TEST|PRODUCTION.
     *
     * @var string
     */
    private $ctxMode;

    /**
     * Certificate for PRODUCTION mode.
     *
     * @var string
     */
    private $certificateProd;

    /**
     * Certificate for TEST mode.
     *
     * @var string
     */
    private $certificateTest;

    /**
     * Computes all fields into a signature.
     *
     * @param array $fields the form fields (example : ['vads_page_action'=>'PAYMENT', 'vads_version'=>'V2'])
     *
     * @return string the signature
     */
    public function compute(array $fields): string
    {
        $certificate = $this->getCertificate();
        ksort($fields);

        $signature = '';
        foreach ($fields as $key => $value) {
            if (strpos($key, 'vads_') === 0) {
                $signature .= $value.'+';
            }
        }
        $signature = sha1($signature.$certificate);

        return $signature;
    }

    /**
     * @param string $signature the signature has to be compared
     * @param array  $fields    the fields to computes into the other signature
     *
     * @return bool true if the signatures are equals
     */
    public function isEquals(string $signature, array $fields): bool
    {
        return $signature === $this->compute($fields);
    }

    /**
     * Returns the right certificate for the selected mode.
     *
     * @return string the certificate
     */
    public function getCertificate(): string
    {
        switch ($this->ctxMode) {
            case self::MODE_TEST:
                return $this->certificateTest;
            case self::MODE_PROD:
                return $this->certificateProd;
        }

        return '';
    }

    /**
     * Set CtxMode.
     *
     * @param string $ctxMode Mode of the transaction : TEST|PRODUCTION (by default TEST)
     */
    public function setCtxMode(string $ctxMode)
    {
        $this->ctxMode = $ctxMode;
        if (!in_array($this->ctxMode, self::MODES, true)) {
            $this->ctxMode = self::MODE_TEST;
        }
    }

    /**
     * Get CtxMode.
     *
     * @return string
     */
    public function getCtxMode(): string
    {
        return $this->ctxMode;
    }

    /**
     * Set CertificateProd.
     *
     * @param string $certificateProd
     */
    public function setCertificateProd(string $certificateProd)
    {
        $this->certificateProd = $certificateProd;
    }

    /**
     * Set CertificateTest.
     *
     * @param string $certificateTest
     */
    public function setCertificateTest(string $certificateTest)
    {
        $this->certificateTest = $certificateTest;
    }
}
