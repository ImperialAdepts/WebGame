<?php
namespace PlanetBundle\Form;

use PlanetBundle\Concept\ConceptToBlueprintAdapter;
use PlanetBundle\Concept\Reactor;
use PlanetBundle\Entity\Resource\Blueprint;
use Symfony\Component\Form\AbstractType;
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
        $builder->add('name', TextType::class, [
        ]);
        $structure = ConceptToBlueprintAdapter::getStructure($options['concept']);
        foreach ($structure as $partName => $useCase) {
            if (isset($useCase['class'])) {
                $builder->add($partName, BlueprintChoiceType::class, ['useCase' => $useCase['class']]);
            } else {
                $builder->add($partName, TextType::class);
            }
        }
        $builder->add('concept', HiddenType::class, ['data' => $options['concept']]);
        $builder->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setRequired('concept');
        $resolver->setDefaults([
            'data_class' => BlueprintDTO::class,
        ]);
    }


}