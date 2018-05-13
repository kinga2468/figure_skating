<?php
/**
 * PanelEdit form.
 */

namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Validator\Constraints as CustomAssert;

/**
 * Class PanelEditType
 *
 * @package Form
 */
class PanelEditType extends AbstractType
{
    /**
     * buildFrom
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'login',
            TextType::class,
            [
                'label' => 'label.login',
                'required' => true,
                'error_bubbling'=> true, //do wyświetlania błędów u góry
                'attr' => [
                    'max_length' => 32,
                ],
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['login-default']]
                    ),
                    new Assert\Length(
                        [
                            'groups' => ['login-default'],
                            'min' => 8,
                            'minMessage' => 'validators_login_min',
                            'max' => 32,
                            'maxMessage' => 'validators_login_max',
                        ]
                    ),
                    new CustomAssert\UniqueLogin(
                        [
                            'groups' => ['login-default'],
                            'repository' => isset($options['login_repository']) ? $options['login_repository'] : null,
                            'elementId' => isset($options['data']['id']) ? $options['data']['id'] : null,
                        ]
                    ),
                ],
            ]
        );
    }

    /**
     * configureOptions
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'validation_groups' =>
                    [
                        'login-default',
                        'name-default',
                        'email-default',
                        'surname-default',
                    ],
                'login_repository' => null,
            ]
        );
    }

    /**
     * getBlockPrefix
     *
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'PanelEdit_type';
    }

}