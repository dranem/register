<?php
// src/Acme/AccountBundle/Form/Type/ChangePasswordType.php
namespace Acme\AccountBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('oldPassword', 'password', array('label' => 'Current Password'));
        $builder->add('newPassword', 'repeated', array(
            'type' => 'password',
            'invalid_message' => 'The password doesnt match.',
            'required' => true,
            'first_options'  => array('label' => 'New Password'),
            'second_options' => array('label' => 'Confirm Password'),
        ));
        $builder->add('update', 'submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acme\AccountBundle\Form\Model\ChangePassword',
        ));
    }

    public function getName()
    {
        return 'change_password';
    }
}