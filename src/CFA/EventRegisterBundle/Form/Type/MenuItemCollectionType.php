<?php

namespace CFA\EventRegisterBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuItemCollectionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('items', 'collection', [
            'label'        => false,
            'type'         => 'text',
            'allow_add'    => true,
            'allow_delete' => true,
            'delete_empty' => true,
            'required'     => false,
            'by_reference' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'CFAEventRegisterBundle:Transaction',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'menuItemCollection';
    }
}
