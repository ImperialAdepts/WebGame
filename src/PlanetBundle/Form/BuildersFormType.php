<?php
namespace PlanetBundle\Form;

use PlanetBundle\Entity\Blueprint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BuildersFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['blueprints'] as $blueprint) {
            $builder
                ->add($blueprint->getId(), BlueprintCountType::class, [
                    'blueprint' => $blueprint,
                ]);
        }
        $builder->add('build', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setRequired('blueprints');
    }


}