<?php

namespace CFA\Hub\MarketingBundle\Form\Type;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

/**
 * @DI\Service("cfa.marketing.form.type.order")
 * @DI\Tag("form.type", attributes={ "alias" = "cfa_marketing_order" })
 */
class OrderType extends AbstractType
{
    public function buildForm (FormBuilderInterface $builder, array $options)
    {
        $builder->add('product', 'entity', [
            'label'         => 'Product',
            'class'         => 'CFAHubSharedBundle:Product',
            'required'      => true,
            'placeholder'   => '-- Product --',
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('p')
                    ->orderBy('p.category', 'asc');
            },
        ]);

        $builder->add('qty', 'number', [
            'label'    => 'Qty',
            'data'     => '1',
            'required' => true,
        ]);

        $builder->add('specialRequest', 'textarea', [
            'label'    => 'Comments',
            'required' => false,
            'attr'     => [
                'rows' => 2,
            ],
        ]);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'CFA\Hub\SharedBundle\Entity\Sale',
        ]);
    }

    public function getName ()
    {
        return 'cfa_marketing_order';
    }
}
