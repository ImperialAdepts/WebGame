<?php
namespace PlanetBundle\Form;

use PlanetBundle\Entity\Resource\Blueprint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Button;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlueprintCountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('count', NumberType::class, [
                'label' => $options['blueprint']->getDescription() .' - '. $options['blueprint']->getMainProduct()->getDescription() . "×".$options['blueprint']->getMainProduct()->getAmount(),
                'required' => false,
                'empty_data' => 0,
                'data' => 0,
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setRequired('blueprint');
    }

 }