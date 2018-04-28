<?php
/**
 * Role type.
 */
namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class RoleType.
 *
 * @package Form
 */
class RoleType extends AbstractType
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
            'role_id',
            ChoiceType::class,
            [
                'label' => 'label.role',
                'choices'  => [
                    'admin' => 1,
                    'user' => 2,
                ],
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['role-default']]
                    ),
                    new Assert\Choice(
                        [
                            'groups' => ['role-default'],
                            'choices' => [1, 2],
                        ]
                    ),
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
        return 'role_type';
    }

}