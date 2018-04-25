<?php
/**
 * Unique Login constraint.
 */
namespace Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class UniqueLogin.
 *
 * @package Validator\Constraints
 */
class UniqueLogin extends Constraint
{
    /**
     * Message.
     *
     * @var string $message
     */
    public $message = 'validators_login_unique';

    /**
     * Element id.
     *
     * @var int|string|null $elementId
     */
    public $elementId = null;

    /**
     * SignUp repository.
     *
     * @var null|\Repository\SignUpRepository $repository
     */
    public $repository = null;
}