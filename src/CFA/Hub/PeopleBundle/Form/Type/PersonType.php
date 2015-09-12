<?php

namespace CFA\Hub\PeopleBundle\Form\Type;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use CFA\Hub\SharedBundle\Entity\Person;

/**
 * @DI\Service("cfa.shared.form.type.person")
 * @DI\Tag("form.type", attributes={ "alias" = "person_form" })
 */
class PersonType extends AbstractType
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

        $builder->add('username', 'text', [
            'label'    => 'Username',
            'required' => true,
        ]);

        if ($options['show_all']) {
            $builder->add('password', 'password', [
                'label'    => 'Password',
                'required' => false,
            ]);
        }

        $builder->add('email', 'email', [
            'label'    => 'Email',
            'required' => false,
        ]);

        $builder->add('phone', 'text', [
            'label'    => 'Phone',
            'required' => false,
        ]);

        $builder->add('birthday', 'date', [
            'label'    => 'Birthday',
            'widget'   => 'single_text',
            'required' => true,
        ]);

        $builder->add('position', 'choice', [
            'label'       => 'Position',
            'choices'     => Person::getValidPositions(false),
            'required'    => true,
            'empty_value' => '-- Position --',
        ]);

        $builder->add('hireDate', 'date', [
            'label'    => 'Hire Date',
            'widget'   => 'single_text',
            'required' => true,
        ]);

        $builder->add('roles', 'choice', [
            'label'   => 'Roles',
            'choices' => Person::getValidRoles(),
            'expanded' => true,
            'multiple' => true,
            'required' => true,
        ]);

        if (! $options['show_all']) {
            $builder->add('active', 'choice', [
                'label'    => 'Employee Status',
                'choices'  => [
                    true  => 'Active',
                    false => 'No longer employed',
                ],
                'multiple' => false,
                'expanded' => true,
                'required' => true,
            ]);
        }

        $builder->add('picture', 'file', [
            'label'    => 'Picture',
            'required' => false,
            'mapped'   => false,
        ]);

        $builder->getForm();
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'CFA\Hub\SharedBundle\Entity\Person',
        ]);

        $resolver->setRequired([
            'show_all',
        ]);
    }

    public function getName ()
    {
        return 'person_form';
    }
}
