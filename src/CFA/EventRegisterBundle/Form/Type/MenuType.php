<?php

namespace CFA\EventRegisterBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use CFA\EventRegisterBundle\Entity\Menu;

class MenuType extends AbstractType
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

        $builder->add('price', 'text', [
            'label'    => 'Price',
            'required' => true,
        ]);

        $builder->add('type', 'choice', [
            'label'    => 'Type',
            'choices'  => Menu::getValidTypes(),
            'required' => true,
        ]);

        $builder->add('picture', 'file', [
            'label'    => 'Image',
            'mapped'   => false,
            'required' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'CFA\EventRegisterBundle\Entity\Menu',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'menu';
    }
}
