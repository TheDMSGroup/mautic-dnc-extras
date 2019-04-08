<?php

/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Digital Media Solutions, LLC
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticDoNotContactExtrasBundle\Constraints;

use Mautic\CoreBundle\Helper\PhoneNumberHelper;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class PhoneValidator.
 */
class PhoneEmailValidator extends ConstraintValidator
{
    /**
     * @param mixed      $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $result = false;

        if (!$constraint instanceof PhoneEmail) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\PhoneEmail');
        }

        if (false !== $value && !empty($value)) {
            if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $result = true;
            } else {
                try {
                    $normalized = (new PhoneNumberHelper())->format($value);
                    if (!empty($normalized) || strlen($normalized) < 10) {
                        $result = true;
                    }
                } catch (\Exception $e) {
                }
            }
        }

        if (!$result) {
            if ($this->context instanceof ExecutionContextInterface) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ value }}', $this->formatValue($value))
                    ->setCode(PhoneEmail::PHONE_EMAIL_ERROR)
                    ->addViolation();
            } else {
                $this->buildViolation($constraint->message)
                    ->setParameter('{{ value }}', $this->formatValue($value))
                    ->setCode(PhoneEmail::PHONE_EMAIL_ERROR)
                    ->addViolation();
            }
        }
    }
}
