<?php

namespace Acme\AccountBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @UniqueEntity(fields="email", message="Email already taken")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    protected $email;
    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *  min = 6, 
     *  max = 4096,
     *  minMessage = "Your passowrd must be at least {{ limit }} characters long"
     * )
     */
    protected $plainPassword;


    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    protected $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    protected $lastName;

    /**
     * @ORM\Column(type="boolean", options={"default":0})
     */
    protected $active = 0;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $activationLink;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $resetpassLink;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(max = 4096)
     */
    protected $salt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $resetlinkDate;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set plainPassword
     *
     * @param string $plainPassword
     * @return User
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
        //$this->plainPassword = hash('sha256',$plainPassword);

        return $this;
    }

    /**
     * Get plainPassword
     *
     * @return string 
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return User
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }


    public function getActivationLink()
    {
        return $this->activationLink;//md5(rand(0,1000));
    }

    public function setActivationLink($activationLink)
    {
        $this->activationLink =  md5($activationLink.time());

        return $this;
    }

    public function getResetpassLink()
    {
        return $this->resetpassLink;//md5(rand(0,1000));
    }

    public function setResetpassLink($resetpassLink)
    {
        $this->resetpassLink =  md5($resetpassLink.time());

        return $this;
    }

    public function getResetlinkDate()
    {
        return $this->resetlinkDate;//md5(rand(0,1000));
    }

    public function setResetlinkDate($resetlinkDate)
    {
        $this->resetlinkDate =  $resetlinkDate;

        return $this;
    }

    

    /**
     * Set password
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }
    /**
     * Get salt
     *
     * @return string 
     */
    public function getSalt()
    {
        return $this->salt;
    }

    public function getRoles() {

    }
    public function setPassword($password) {
        $this->password = $password;

        return $this;
    }
    public function getPassword() {
        return $this->password;
    }

    public function getUsername() {

    }

    public function eraseCredentials() {

    }
}
