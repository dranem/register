<?php
// src/Acme/AccountBundle/Form/Type/UpdateType.php
namespace Acme\AccountBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$builder->add('user', new UserType());
        $builder->add('email', 'email',array(
        	'read_only' => true
    	));
        $builder->add('plainPassword', 'repeated', array(
           'first_name'  => 'password',
           'second_name' => 'confirm',
           'type'        => 'password',
        ));
        $builder->add('firstName','text');
        $builder->add('lastName','text');
        $builder->add('update', 'submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acme\AccountBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'update';
    }
}