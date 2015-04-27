<?php
namespace Acme\AccountBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class EqualstoOldPassword extends Constraint
{
	/*
	protected $session;

	public function __construct($options)
    {
        if($options['session'] and $options['session'] instanceof Session)
        {
            $this->session = $options['session'];
        }
        else
        {
            throw new MissingOptionException();
        }
    }

    public function getSession()
    {
        return $this->session;
    }
    */
    public $message = '"%string%" Old Password does not match';

    public function validatedBy()
    {
        return 'unique.password.validator';
    }
}