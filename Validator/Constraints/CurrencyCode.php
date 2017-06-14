<?php

namespace Ioni\PayzenBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class CurrencyCode.
 *
 * @author Thomas Talbot <talbot.thomas14@gmail.com>
 *
 * @Annotation
 */
class CurrencyCode extends Constraint
{
    public $message = 'validator.constraint.currency_code.message';

    /**
     * All Payzen supported currency codes with multi-currency compatible.
     */
    const CURRENCY_CODES = [
        'AUD' => '036',
        'CAD' => '124',
        'CNY' => '156',
        'CZK' => '203',
        'DKK' => '208',
        'HKD' => '344',
        'HUF' => '348',
        'INR' => '356',
        'JPY' => '392',
        'KWD' => '414',
        'MAD' => '504',
        'NZD' => '554',
        'NOK' => '578',
        'SGD' => '702',
        'ZAR' => '710',
        'SEK' => '752',
        'CHF' => '756',
        'TND' => '788',
        'GBP' => '826',
        'USD' => '840',
        'TRY' => '949',
        'EUR' => '978',
        'PLN' => '985',
        'BRL' => '986',
    ];

    /**
     * @return string
     */
    public function validatedBy(): string
    {
        return get_class($this).'Validator';
    }
}
