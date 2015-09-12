<?php

namespace CFA\Hub\SharedBundle\Form\Type;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @DI\Service("cfa.shared.form.type.customer")
 * @DI\Tag("form.type", attributes={ "alias" = "cfa_customer" })
 */
class CustomerType extends AbstractType
{
    public function buildForm (FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstName', 'text', [
            'label'    => 'First Name',
            'required' => true,
        ]);

        $builder->add('lastName', 'text', [
            'label'    => 'Last Name',
            'required' => true,
        ]);

        $builder->add('email', 'email', [
            'label'    => 'Email',
            'required' => false,
        ]);

        $builder->add('phone', 'text', [
            'label'    => 'Phone',
            'required' => false,
        ]);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'CFA\Hub\SharedBundle\Entity\Customer',
        ]);
    }

    public function getName ()
    {
        return 'cfa_customer';
    }
}
