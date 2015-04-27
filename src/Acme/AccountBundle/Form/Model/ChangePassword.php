<?php
namespace Acme\AccountBundle\Form\Model;

use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;
use Acme\AccountBundle\Validator\Constraints as AcmeAssert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class ChangePassword
{
    /**
     * @Assert\Length(
     *     min = 6,
     *     minMessage = "Password should by at least 6 chars long"
     * )
     * @AcmeAssert\EqualstoOldPassword
     */
    //@AcmeAssert\EqualstoOldPassword
    protected $oldPassword;

    /**
     * @Assert\Length(
     *     min = 6,
     *     minMessage = "Password should by at least 6 chars long"
     * )
     */
    protected $newPassword;

    /**
     * 
     */
    //@Assert\Callback
    /*public function validate(ExecutionContextInterface $context)
    {
        //$user = $this->session->get('uid');
        //print_r($context);
        //echo $context->getEmail();
        //if($value != 'test') echo 'dili';
    }
    */


    public function setOldPassword($oldPassword)
    {
        $this->oldPassword = $oldPassword;
    }

    public function getOldPassword()
    {
        return $this->oldPassword;
    }

    public function setNewPassword($newPassword)
    {
        $this->newPassword = $newPassword;
    }

    public function getNewPassword()
    {
        return $this->newPassword;
    }
}