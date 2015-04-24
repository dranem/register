<?php
// src/Acme/AccountBundle/Form/Type/UserType.php
namespace Acme\AccountBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;


class UpdatepassType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('plainPassword', 'password', array(
               'constraints' => array(
                   new NotBlank(),
                   new Length(array('min' => 6)),
               ),
               'label' => 'Old Password',
           ));
        $builder->add('newPassword', 'repeated', array(
                'constraints' => array(
                   new NotBlank(),
                   new Length(array('min' => 6)),
               ),
               'first_name'  => 'new_password',
               'second_name' => 'confirm',
               'type'        => 'password',
             ));
        $builder->add('update', 'submit');
    }

    public function getName()
    {
        return 'update_pass';
    }
}