<?php

namespace CFA\Hub\MarketingBundle\Form\Type;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use CFA\Hub\SharedBundle\Entity\Product;

/**
 * @DI\Service("cfa.marketing.form.type.product")
 * @DI\Tag("form.type", attributes={ "alias" = "cfa_marketing_product" })
 */
class ProductType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', [
            'label'    => 'Name',
            'required' => true,
        ]);

        $builder->add('category', 'choice', [
            'label'       => 'Category',
            'required'    => true,
            'choices'     => Product::getCategoryList(),
            'placeholder' => '-- Category --',
        ]);

        $builder->add('group', 'choice', [
            'label'       => 'Group',
            'required'    => false,
            'choices'     => Product::getGroupList(),
            'placeholder' => '-- Group --',
        ]);

        $builder->add('description', 'textarea', [
            'label'    => 'Description',
            'required' => false,
            'attr'     => [
                'rows' => 8,
            ],
        ]);

        $builder->add('servingSize', 'text', [
            'label'    => 'Serving Size',
            'required' => false,
        ]);

        $builder->add('countDescription', 'text', [
            'label'    => 'Number of items',
            'required' => false,
        ]);

        $builder->add('price', 'number', [
            'label'    => 'Price',
            'required' => true,
        ]);

        $builder->add('photo', 'file', [
            'label'    => 'Photo',
            'required' => false,
            'mapped'   => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'CFA\Hub\SharedBundle\Entity\Product',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'cfa_marketing_product';
    }
}
