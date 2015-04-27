<?php
// src/Acme/AccountBundle/Form/Type/ForgotPasswordType.php
namespace Acme\AccountBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;


class ForgotPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', 'email',array(
               'constraints' => array(new Email(), new NotBlank())
           ));
        $builder->add('Send', 'submit');
    }

    public function getName()
    {
        return 'forgot_password';
    }
}