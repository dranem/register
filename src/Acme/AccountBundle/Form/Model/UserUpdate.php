<?php
// src/Acme/AccountBundle/Form/Model/UserUpdate.php
namespace Acme\AccountBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

use Acme\AccountBundle\Entity\User;

class UserUpdate
{
    /**
     * @Assert\Type(type="Acme\AccountBundle\Entity\User")
     * @Assert\Valid()
     */
    protected $user;

    
   

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

}