<?php
/**
 * Comment type.
 */
namespace Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Class CommentType.
 *
 * @package Form
 */
class CommentType extends AbstractType
{
    /**
     * Build Form
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'text',
            TextareaType::class,
            [
                'label' => 'label.text',
                'required'   => false,
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['video-default']]
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
                'validation_groups' => ['comment-default'],
                'comment_repository' => null,
            ]
        );
    }
    /**
     * Get Block Prefix
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'comment_type';
    }
}