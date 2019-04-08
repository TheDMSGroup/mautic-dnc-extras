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

use Symfony\Component\Validator\Constraint;

/**
 * Class PhoneEmail.
 */
class PhoneEmail extends Constraint
{
    const PHONE_EMAIL_ERROR = '5347dbfa-7b28-4f01-922d-595c973d1f4a';

    /** @var array */
    protected static $errorNames = [
        self::PHONE_EMAIL_ERROR => 'PHONE_EMAIL_ERROR',
    ];

    /** @var string */
    public $message = 'This value is not a valid phone number';
}
