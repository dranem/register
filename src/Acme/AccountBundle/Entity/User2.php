<?php
// src/Acme/AccountBundle/Entity/User.php
namespace Acme\AccountBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @UniqueEntity(fields="email", message="Email already taken")
 */
class User
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
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max = 4096)
     */
    protected $plainPassword;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(
     *  min = 6, 
     *  max = 4096
     * )
     */
    protected $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    protected $lastName;
/*
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('plainPassword', new Assert\Length(array(
            'min'        => 6,
            'max'        => 50,
            'minMessage' => 'Your first name must be at least {{ limit }} characters long',
            'maxMessage' => 'Your first name cannot be longer than {{ limit }} characters',
        )));
    }*/

    public function getId()
    {
        return $this->id;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }
}