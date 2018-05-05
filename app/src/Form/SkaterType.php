<?php
/**
 * Skater type.
 */
namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SkaterType.
 */
class SkaterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'name',
            TextType::class,
            [
                'label' => 'label.name',
                'required' => true,
                'error_bubbling'=> true,
                'attr' => [
                    'max_length' => 60,
                ],
            ]
        );
        $builder->add(
            'date_of_birth',
            BirthdayType::class,
            [
                'label' => 'label.date_of_birth',
                'error_bubbling'=> true,
                'format' => 'yyyy-MM-dd',
                'years' => range(1970,2010),
//                'error_bubbling'=> true, //do wyświetlania błędów u góry
//                'invalid_message' => 'dateStart_limit',
            ]
        );
        $builder->add(
            'country_repr',
            TextType::class,
            [
                'label' => 'label.country_repr',
                'error_bubbling'=> true,
                'required' => true,
                'attr' => [
                    'max_length' => 45,
                ],
            ]
        );
        $builder->add(
            'info',
            TextareaType::class,
            [
                'label' => 'label.info',
                'error_bubbling'=> true,
                'required' => true,
                'attr' => [
                    'max_length' => 1000,
                ],
            ]
        );
        $builder->add(
            'couch',
            TextType::class,
            [
                'label' => 'label.couch',
                'error_bubbling'=> true,
                'required' => true,
                'attr' => [
                    'max_length' => 100,
                ],
            ]
        );
        $builder->add(
            'short_record',
            NumberType::class,
            [
                'label' => 'label.short_record',
                'error_bubbling'=> true,
                'required' => true,
            ]
        );
        $builder->add(
            'free_record',
            NumberType::class,
            [
                'label' => 'label.free_record',
                'error_bubbling'=> true,
                'required' => true,
            ]
        );
        $builder->add(
            'total_record',
            NumberType::class,
            [
                'label' => 'label.total_record',
                'error_bubbling'=> true,
                'required' => true,
            ]
        );
        $builder->add(
            'img',
            TextType::class,
            [
                'label' => 'label.img',
                'error_bubbling'=> true,
                'required' => true,
                'attr' => [
                    'max_length' => 60,
                ],
            ]
        );
        $builder->add(
            'birth_place',
            TextType::class,
            [
                'label' => 'label.birth_place',
                'error_bubbling'=> true,
                'required' => true,
                'attr' => [
                    'max_length' => 45,
                ],
            ]
        );
        $builder->add(
            'height',
            NumberType::class,
            [
                'error_bubbling'=> true,
                'label' => 'label.height',
                'required' => true,

            ]
        );


    }


    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'skater_type';
    }

}