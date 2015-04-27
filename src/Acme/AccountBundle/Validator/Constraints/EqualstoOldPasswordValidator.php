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
        //var_dump($session); exit;
        $this->session = $session;
        //$this->session = new Session();
    }
    

    

    public function validate($value, Constraint $constraint)
    {
        //echo $value;echo '<br />';
        $user = $this->session->get('uid');

        //hash('sha256', $salt . $raw);
        $pass = hash('sha256', $user->getSalt() . $value);
        //echo $pass;
        //if($user->getPlainPassword() === $pass)
        //    echo 'pareho';
        //else echo 'dili';



        if($user->getPlainPassword() !== $pass) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();

            // If you're using the old 2.4 validation API
            /*
            $this->context->addViolation(
                $constraint->message,
                array('%string%' => $value)
            );
            */
        }
    }
}