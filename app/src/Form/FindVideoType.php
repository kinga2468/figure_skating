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
//        $championship = array();
//        foreach ($this->praticiens as $id => $praticien) {
//            $cle = $praticien->__toString();
//            $choices[$cle] = $id;
//        }

        $builder->add(
            'championship',
            ChoiceType::class,
            [
                'label' => 'label.championship',
                'choices' => $this->findOptions($options['championship']),
//                    $options['championship'],
                'expanded' => true,
                'multiple' => false,
            ]
        );
        $builder->add(
            'year',
            ChoiceType::class,
            [
                'label' => 'label.year',
                'choices' => $this->findOptions($options['year_championship']),
                'expanded' => true,
                'multiple' => false,
            ]
        );
        $builder->add(
            'skater',
            ChoiceType::class,
            [
                'label' => 'label.skater',
                'choices' => $this->findOptions($options['skater']),
                'expanded' => true,
                'multiple' => false,
            ]
        );
        $builder->add(
            'type',
            ChoiceType::class,
            [
                'label' => 'label.type',
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
                'skater' => null,
                'type' => null
            ]
        );
        $resolver->setRequired('championship');
        $resolver->setRequired('year_championship');
        $resolver->setAllowedTypes('year_championship', array('array'));
        $resolver->setAllowedTypes('championship', array('array'));
        $resolver->setRequired('skater');
        $resolver->setAllowedTypes('skater', array('array'));
        $resolver->setRequired('type');
        $resolver->setAllowedTypes('type', array('array'));
    }
    public function getBlockPrefix()
    {
        return 'video_type';
    }

    protected function findOptions($column)
    {
//        $propertyTypes = $videoRepository->findChampionship();
        $choices = [];
//        var_dump($column);
        foreach ($column as $first) {
//            var_dump($first['name']);
//            $choices[$first['name']] = $first['surname'];

            foreach ($first as $second){
//                var_dump($second);
//                var_dump($choices[$first]);
                $choices[$second] = 1;
            }

        }
//            $choices[$propertyType['name']] = $propertyType['id'];
//        }
        return $choices;
    }
}