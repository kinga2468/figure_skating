<?php
/**
 * Find Video Type.
 */
namespace Form;
use Repository\VideoRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Find Video Type.
 *
 * @package Form
 */
class FindVideoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        dump($this->findOptions($options['championship']));
        $builder->add(
            'championship',
            ChoiceType::class,
            [
                'label' => 'label.championship',
                'required' => false,
                'placeholder' => 'wszystkie',
                'choices' => $this->findOptions($options['championship']),
//                    $options['championship'],
                'expanded' => true,
                'multiple' => false,
            ]
        );
        $builder->add(
            'year_championship',
            ChoiceType::class,
            [
                'label' => 'label.year',
                'required' => false,
                'placeholder' => 'wszystkie',
                'choices' => $this->findOptions($options['year_championship']),
                'expanded' => true,
                'multiple' => false,
            ]
        );
//        $builder->add(
//            'skater_id',
//            ChoiceType::class,
//            [
//                'label' => 'label.skater',
//                'choices' => $this->findOptions($options['skater_id']),
//                'expanded' => true,
//                'multiple' => false,
//            ]
//        );
        $builder->add(
            'type',
            ChoiceType::class,
            [
                'label' => 'label.type',
                'required' => false,
                'placeholder' => 'wszystkie',
                'choices' => $this->findOptions($options['type']),
                'expanded' => true,
                'multiple' => false,
            ]
        );

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
                'year_championship' => null,
                'championship' => null,
//                'skater_id' => null,
                'type' => null
            ]
        );
        $resolver->setRequired('championship');
        $resolver->setRequired('year_championship');
        $resolver->setAllowedTypes('year_championship', array('array'));
        $resolver->setAllowedTypes('championship', array('array'));
//        $resolver->setRequired('skater_id');
//        $resolver->setAllowedTypes('skater_id', array('array'));
        $resolver->setRequired('type');
        $resolver->setAllowedTypes('type', array('array'));
    }
    public function getBlockPrefix()
    {
        return 'video_type';
    }

    protected function findOptions($column)
    {
        $choices = [];
//        $iterator = 0;
//        dump($column);
        foreach ($column as $first) {
//            dump($first);
            foreach ($first as $second){
                $choices[$second] = $second;
//                $iterator = $iterator +1;
            }
        }
//        dump($choices);
        return $choices;
    }

}