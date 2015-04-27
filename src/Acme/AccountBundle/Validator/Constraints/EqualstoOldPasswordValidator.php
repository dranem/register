<?php
namespace Acme\AccountBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\HttpFoundation\Response;


class EqualstoOldPasswordValidator extends ConstraintValidator
{
    protected $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }
    

    

    public function validate($value, Constraint $constraint)
    {
        $user = $this->session->get('uid');

        $pass = hash('sha256', $user->getSalt() . $value);
 
        if($user->getPlainPassword() !== $pass) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}