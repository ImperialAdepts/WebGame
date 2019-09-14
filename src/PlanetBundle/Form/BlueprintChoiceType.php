<?php
namespace PlanetBundle\Form;

use PlanetBundle\Concept\ConceptToBlueprintAdapter;
use PlanetBundle\Concept\Reactor;
use PlanetBundle\Repository\BlueprintRepository;
use PlanetBundle\Repository\ConceptRepository;
use PlanetBundle\Entity\Resource\Blueprint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlueprintChoiceType extends AbstractType
{
    /** @var ConceptRepository */
    private $conceptRepository;

    /** @var BlueprintRepository */
    private $blueprintRepository;

    /**
     * BlueprintChoiceType constructor.
     * @param BlueprintRepository $blueprintRepository
     */
    public function __construct(BlueprintRepository $blueprintRepository)
    {
        $this->conceptRepository = new ConceptRepository();
        $this->blueprintRepository = $blueprintRepository;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('existing', ChoiceType::class, [
            'multiple' => false,
            'expanded' => false,
            'required' => false,
            'choices' => $this->blueprintRepository->getByUseCase($options['useCase']),
            'choice_label' => function (Blueprint $choice) {
                return $choice->getDescription();
            }
        ]);
        $builder->add('createFromConcept', ChoiceType::class, [
            'multiple' => false,
            'expanded' => false,
            'required' => false,
            'choices' => $this->conceptRepository->getByUseCase($options['useCase']),
            'choices_as_values' => true,
            'choice_label' => function ($choice) {
                return $choice;
            }
        ]);
//        if (isset($options['data']) && isset($options['data']['createFromConcept'])) {
//            $builder->add('blueprint', BlueprintFormType::class, [
//                'concept' => $options['data']['createFromConcept'],
//            ]);
//        }
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            // checks if the Product object is "new"
            // If no data is passed to the form, the data is "null".
            // This should be considered a new "Product"
            if (isset($data['createFromConcept']) && !empty($data['createFromConcept'])) {
                $form->add('blueprint', BlueprintFormType::class, [
                    'concept' => $data['createFromConcept'],
                    'required' => true,
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setRequired('useCase');
        $resolver->setDefaults([
            'data_class' => BlueprintDTO::class,
        ]);
    }


}