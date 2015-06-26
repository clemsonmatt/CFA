<?php

namespace CFA\EventRegisterBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EventType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text', [
            'label'    => 'Title',
            'required' => true,
        ]);

        $builder->add('date', 'date', [
            'label'    => 'Date',
            'required' => true,
        ]);

        /* menu items */
        $menuItems = [];
        foreach ($options['menu_items'] as $item) {
            $menuItems[$item->getTitle()] = $item;
        }

        $builder->add('menuItems', 'choice', [
            'label'    => 'Menu Items',
            'choices'  => $menuItems,
            'multiple' => true,
            'expanded' => true,
            'required' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'CFA\EventRegisterBundle\Entity\Event',
        ]);

        $resolver->setRequired(['menu_items']);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'event';
    }
}
