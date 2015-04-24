<?php
namespace Acme\AccountBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\HttpFoundation\Response;


class EqualstoOldPasswordValidator extends ConstraintValidator
{
    protected $session;

    public function __construct($session)
    {
        var_dump($session); exit;
        $this->session = $session;
        //$this->session = new Session();
    }
    
    

    public function validate($value, Constraint $constraint)
    {
        if($value != 'test') {$user = $this->session;
echo 1;            print_r($user);
        //$user = $this->get('session')->get('uid');
        //$id = $user->getId();echo $id;
            exit;
        //if (!preg_match('/^[a-zA-Za0-9]+$/', $value, $matches)) {
            // If you're using the new 2.5 validation API (you probably are!)
            $this->context->buildViolation($constraint->message)
                ->setParameter('%string%', $value)
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