<?php

namespace Ioni\PayzenBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class CurrencyCodeValidator.
 *
 * @author Thomas Talbot <talbot.thomas14@gmail.com>
 */
class CurrencyCodeValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!in_array($value, CurrencyCode::CURRENCY_CODES, true)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation()
            ;
        }
    }
}
