<?php
namespace Acme\AccountBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class EqualstoOldPassword extends Constraint
{
    public $message = 'Old Password does not match';
}