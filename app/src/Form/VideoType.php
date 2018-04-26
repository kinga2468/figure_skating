<?php
/**
 * Video type.
 */
namespace Form;
use Repository\VideoRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Class VideoType.
 *
 * @package Form
 */
class VideoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder  ->add(
            'championship',
            ChoiceType::class,
            [
                'label' => 'championship',
                'required' => true,
                'choices' => $this->prepareVideoForChoices($options['video_repository'], 'offer_types'),
                'expanded' => true,
                'multiple' => false
            ]
        )
            ->add(
                'year',
                ChoiceType::class,
                [
                    'label' => 'label.year',
                    'required' => true,
                    'choices' => $this->prepareVideoForChoices($options['video_repository'], 'property_types'),
                    'expanded' => true,
                    'multiple' => false
                ]
            )
            ->add(
                'skater',
                ChoiceType::class,
                [
                    'label' => 'label.skater',
                    'required' => true,
                    'choices' => $this->prepareVideoForChoices($options['video_repository'], 'property_types'),
                    'expanded' => true,
                    'multiple' => false
                ]
            )
            ->add(
                'type',
                ChoiceType::class,
                [
                    'label' => 'label.type',
                    'required' => true,
                    'choices' => $this->prepareVideoForChoices($options['video_repository'], 'property_types'),
                    'expanded' => true,
                    'multiple' => false
                ]
            )
        ;
    }
    /**
     * {@inheritdoc}
     */
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'validation_groups' => 'video-default',
                'video_repository' => null,
            ]
        );
    }
    public function getBlockPrefix()
    {
        return 'video_type';
    }
    protected function prepareVideoForChoices($videoRepository, $table)
    {
        $propertyTypes = $videoRepository->findAll($table);
        $choices = [];
        foreach ($propertyTypes as $propertyType) {
            $choices[$propertyType['name']] = $propertyType['id'];
        }
        return $choices;
    }
}