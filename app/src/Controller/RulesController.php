<?php
/**
 * Rules controller.
 */
namespace Controller;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Repository\UserRepository;

/**
 * Class RulesController.
 */
class RulesController implements ControllerProviderInterface
{
    /**
     * Routing settings.
     *
     * @param \Silex\Application $app Silex application
     *
     * @return \Silex\ControllerCollection Result
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->get('/', [$this, 'indexAction'])->bind('rules_index');

        return $controller;
    }

    /**
     * Index action.
     *
     * @param \Silex\Application                        $app     Silex application
     * @param \Symfony\Component\HttpFoundation\Request $request Request object
     *
     * @return string Response
     */
    public function indexAction(Application $app)
    {
        $userRepository = new UserRepository($app['db']);
        $userId = $userRepository->getLoggedUserId($app);

        return $app['twig']->render(
            'rules/index.html.twig',
            [
                'user_id' => $userId,
            ]
        );
    }
}
