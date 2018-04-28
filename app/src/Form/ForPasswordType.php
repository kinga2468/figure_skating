<?php
/**
 * ForPassword form.
 */

namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;


/**
 * Class ForPasswordType
 *
 * @package Form
 */
class ForPasswordType extends AbstractType
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
            'password',
            RepeatedType::class,
            [
                'type' => PasswordType::class,
                'error_bubbling'=> true,
                'required' => true,
                'attr' => [
                    'max_length' => 32,
                ],
                'invalid_message' => 'validators_password_match',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(
                        [
                            'min' => 8,
                            'minMessage' => 'validators_password_min',
                            'max' => 32,
                            'maxMessage' => 'validators_password_max',
                        ]
                    ),
                ],
                'first_options'  => [
                    'label' => 'label.password',
                ],
                'second_options' => [
                    'label' => 'label.repeatPassword',
                ],
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
        return 'password_type';
    }

}