<?php
// src/Acme/AccountBundle/Form/Type/UserType.php
namespace Acme\AccountBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('plainPassword', 'repeated', array(
           'first_name'  => 'newPassword',
           'second_name' => 'confirm',
           'type'        => 'password',
        ));
        $builder->add('Submit', 'submit');
    }

    public function getName()
    {
        return 'reset_password';
    }
}