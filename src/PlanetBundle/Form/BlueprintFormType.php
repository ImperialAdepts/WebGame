<?php
namespace PlanetBundle\Form;

use PlanetBundle\Concept\ConceptToBlueprintAdapter;
use PlanetBundle\Concept\Reactor;
use PlanetBundle\Entity\Resource\Blueprint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tracy\Debugger;

class BlueprintFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, []);
        $traits = ConceptToBlueprintAdapter::getTraits($options['concept']);
        foreach ($traits as $traitName => $traitConstraints) {
            $builder->add($traitName, TextType::class, [
                'required' => true,
            ]);
        }
        $builder->add('blueprintId', HiddenType::class);
        $builder->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setRequired('concept');
        $resolver->setDefaults([
            'data_class' => BlueprintAdapter::class,
            'csrf_protection' => false,
        ]);
    }


}