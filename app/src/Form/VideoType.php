<?php
/**
 * Video type.
 */
namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class VideoType.
 */
class VideoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'link',
            TextType::class,
            [
                'label' => 'label.link',
                'required' => true,
                'attr' => [
                    'max_length' => 200,
                ],
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['video-default']]
                    ),
                    new Assert\Length(
                        [
                            'groups' => ['video-default'],
                            'min' => 2,
                            'minMessage' => 'validators_link_min',
                            'max' => 200,
                            'maxMessage' => 'validators_link_max',
                        ]
                    ),
                ],
            ]
        );
        $builder->add(
            'img_video_yt',
            TextType::class,
            [
                'label' => 'label.img_video_yt',
                'required' => true,
                'attr' => [
                    'max_length' => 200,
                ],
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['video-default']]
                    ),
                    new Assert\Length(
                        [
                            'groups' => ['video-default'],
                            'min' => 2,
                            'minMessage' => 'validators_img_vide_yt_min',
                            'max' => 200,
                            'maxMessage' => 'validators_img_vide_yt_max',
                        ]
                    ),
                ],
            ]
        );
        $builder->add(
            'title',
            TextType::class,
            [
                'label' => 'label.title',
                'required' => true,
                'attr' => [
                    'max_length' => 100,
                ],
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['video-default']]
                    ),
                    new Assert\Length(
                        [
                            'groups' => ['video-default'],
                            'min' => 2,
                            'minMessage' => 'validators_title_min',
                            'max' => 100,
                            'maxMessage' => 'validators_title_max',
                        ]
                    ),
                ],
            ]
        );
        $builder->add(
            'championship',
            TextType::class,
            [
                'label' => 'label.championship',
                'required' => true,
                'attr' => [
                    'max_length' => 45,
                ],
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['video-default']]
                    ),
                    new Assert\Length(
                        [
                            'groups' => ['video-default'],
                            'min' => 2,
                            'minMessage' => 'validators_championship_min',
                            'max' => 45,
                            'maxMessage' => 'validators_championship_max',
                        ]
                    ),
                    new Assert\Regex(
                        [
                            'pattern'     => '/^[a-ż ]+$/i',
                            'htmlPattern' => '^[a-żA-Ż ]+$',
                            'message' => 'validators_championship_pattern'
                        ]
                    ),
                ],
            ]
        );
        $builder->add(
            'year_championship',
            TextType::class,
            [
                'label' => 'label.year_championship',
                'required' => true,
                'attr' => [
                    'max_length' => 4,
                ],
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['video-default']]
                    ),
                    new Assert\Length(
                        [
                            'groups' => ['video-default'],
                            'min' => 4,
                            'minMessage' => 'validators_year_championship_min',
                            'max' => 4,
                            'maxMessage' => 'validators_year_championship_max',
                        ]
                    ),
                    new Assert\Regex(
                        [
                            'pattern'     => '/^[0-9]+$/i',
                            'htmlPattern' => '^[0-9]+$',
                            'message' => 'validators_year_championship_pattern'
                        ]
                    ),
                ],
            ]
         );
        $builder->add(
            'type',
            ChoiceType::class,
            [
                'label' => 'label.type',
                'required' => true,
                'choices'  => array(
                    'short_program' => 'program krótki',
                    'free_program' => 'program dowolny'
                )
            ]
        );
        $builder->add(
            'song',
            TextType::class,
            [
                'label' => 'label.song',
                'required' => true,
                'attr' => [
                    'max_length' => 50,
                ],
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['video-default']]
                    ),
                    new Assert\Length(
                        [
                            'groups' => ['skater-default'],
                            'min' => 4,
                            'minMessage' => 'validators_song_min',
                            'max' => 50,
                            'maxMessage' => 'validators_song_max',
                        ]
                    ),
                ],
            ]
        );
        $builder->add(
            'skaters_id',
            ChoiceType::class,
            [
                'label' => 'label.skaters_name',
                'required' => true,
                'choices' => $this->findOptions($options['skaters_repository']),
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
                'skaters_repository' => null,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'video_type';
    }

    protected function findOptions($skatersRepository)
    {
        $choice = [];
        $options = $skatersRepository->findAll();
        foreach ($options as $option) {
            $choice[$option['name']] = $option['id'];
        }
        return $choice;
    }
}