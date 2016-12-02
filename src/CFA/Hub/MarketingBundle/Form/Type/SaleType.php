<?php

namespace CFA\Hub\MarketingBundle\Form\Type;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

use CFA\Hub\SharedBundle\Entity\Sale;

/**
 * @DI\Service("cfa.marketing.form.type.sale")
 * @DI\Tag("form.type", attributes={ "alias" = "cfa_marketing_sale" })
 */
class SaleType extends AbstractType
{
    public function buildForm (FormBuilderInterface $builder, array $options)
    {
        $builder->add('comments', 'textarea', [
            'label'    => 'Comments',
            'required' => false,
            'attr'     => [
                'rows' => 10,
            ],
        ]);

        $builder->add('pickupDate', 'date', [
            'label'    => 'Pickup Date',
            'widget'   => 'single_text',
            'format'   => 'MM/dd/yyyy',
            'html5'    => false,
            'required' => true,
            'attr'     => [
                'class'            => 'datepicker',
                'data-date-format' => 'mm/dd/yyyy',
                'placeholder'      => 'Pickup Date',
            ],
        ]);

        $builder->add('pickupTime', 'text', [
            'label'    => 'Pickup Time',
            'required' => false,
            'attr'     => [
                'class'       => 'timepicker',
                'placeholder' => 'Pickup Time',
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
        return 'cfa_marketing_sale';
    }
}
