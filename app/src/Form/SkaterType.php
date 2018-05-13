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
use Symfony\Component\Validator\Constraints as Assert;

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
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['skater-default']]
                    ),
                    new Assert\Length(
                        [
                            'groups' => ['skater-default'],
                            'min' => 2,
                            'minMessage' => 'validators_skater_name_min',
                            'max' => 60,
                            'maxMessage' => 'validators_skater_name_max',
                        ]
                    ),
                    new Assert\Regex(
                        [
                            'pattern'     => '/^[a-ż ]+$/i',
                            'htmlPattern' => '^[a-żA-Ż ]+$',
                            'message' => 'validators_skater_name_pattern'
                        ]
                    ),
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
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['skater-default']]
                    ),
                    new Assert\Length(
                        [
                            'groups' => ['skater-default'],
                            'min' => 2,
                            'minMessage' => 'validators_country_repr_min',
                            'max' => 45,
                            'maxMessage' => 'validators_country_repr_max',
                        ]
                    ),
                    new Assert\Regex(
                        [
                            'pattern'     => '/^[a-ż ]+$/i',
                            'htmlPattern' => '^[a-żA-Ż ]+$',
                            'message' => 'validators_country_repr_pattern'
                        ]
                    ),
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
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['skater-default']]
                    ),
                    new Assert\Length(
                        [
                            'groups' => ['skater-default'],
                            'min' => 5,
                            'minMessage' => 'validators_info_min',
                            'max' => 1000,
                            'maxMessage' => 'validators_info_max',
                        ]
                    ),
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
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['skater-default']]
                    ),
                    new Assert\Length(
                        [
                            'groups' => ['skater-default'],
                            'min' => 2,
                            'minMessage' => 'validators_couch_min',
                            'max' => 100,
                            'maxMessage' => 'validators_couch_max',
                        ]
                    ),
                    new Assert\Regex(
                        [
                            'pattern'     => '/^[a-ż ]+$/i',
                            'htmlPattern' => '^[a-żA-Ż ]+$',
                            'message' => 'validators_couch_pattern'
                        ]
                    ),
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
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['skater-default']]
                    ),
                    new Assert\Range(
                        [
                            'min' => 1,
                            'minMessage' => 'validators_record_min',
                            'max' => 500,
                            'maxMessage' => 'validators_record_max',
                        ]
                    ),
                ],
            ]
        );
        $builder->add(
            'free_record',
            NumberType::class,
            [
                'label' => 'label.free_record',
                'error_bubbling'=> true,
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['skater-default']]
                    ),
                    new Assert\Range(
                        [
                            'min' => 1,
                            'minMessage' => 'validators_record_min',
                            'max' => 500,
                            'maxMessage' => 'validators_record_max',
                        ]
                    ),
                ],
            ]
        );
        $builder->add(
            'total_record',
            NumberType::class,
            [
                'label' => 'label.total_record',
                'error_bubbling'=> true,
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['skater-default']]
                    ),
                    new Assert\Range(
                        [
                            'min' => 1,
                            'minMessage' => 'validators_record_min',
                            'max' => 500,
                            'maxMessage' => 'validators_record_max',
                        ]
                    ),
                ],
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
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['skater-default']]
                    ),
                    new Assert\Length(
                        [
                            'groups' => ['skater-default'],
                            'min' => 2,
                            'minMessage' => 'validators_img_min',
                            'max' => 60,
                            'maxMessage' => 'validators_img_max',
                        ]
                    ),
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
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['skater-default']]
                    ),
                    new Assert\Length(
                        [
                            'groups' => ['skater-default'],
                            'min' => 2,
                            'minMessage' => 'validators_birth_place_min',
                            'max' => 45,
                            'maxMessage' => 'validators_birth_place_max',
                        ]
                    ),
                    new Assert\Regex(
                        [
                            'pattern'     => '/^[a-ż ]+$/i',
                            'htmlPattern' => '^[a-żA-Ż ]+$',
                            'message' => 'validators_birth_place_pattern'
                        ]
                    ),
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
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['skater-default']]
                    ),
                    new Assert\Range(
                        [
                            'min' => 100,
                            'minMessage' => 'validators_height_min',
                            'max' => 300,
                            'maxMessage' => 'validators_heigh_max',
                        ]
                    ),
                ],
            ]
        );


    }

    /**
     * Configure Options
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'validation_groups' => ['skater-default'],
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