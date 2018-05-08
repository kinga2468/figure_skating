<?php
/**
 * Video type.
 */
namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ]
         );
        $builder->add(
            'type',
            TextType::class,
            [
                'label' => 'label.type',
                'required' => true,
                'attr' => [
                    'max_length' => 2,
                ],
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