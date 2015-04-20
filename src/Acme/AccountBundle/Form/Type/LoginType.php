<?php
// src/Acme/AccountBundle/Form/Type/LoginType.php
namespace Acme\AccountBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;


class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
           ->add('email', 'text', array(
               'constraints' => new Email(),
           ))
           ->add('plainPassword', 'password', array(
               'constraints' => array(
                   new NotBlank(),
                   new Length(array('min' => 6)),
               ),
               'label' => 'Password',
           ))
           ->add('Register', 'submit')
        ;
       
    }

    public function getName()
    {
        return 'user';
    }
}