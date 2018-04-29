<?php
/**
 * Skater controller.
 */
namespace Controller;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Repository\SkaterRepository;
use Symfony\Component\HttpFoundation\Request;
use Repository\UserRepository;

/**
 * Class SkaterController.
 */
class SkaterController implements ControllerProviderInterface
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
        $controller->get('/', [$this, 'indexAction'])->bind('skater_index');
        $controller->get('/{id}', [$this, 'viewAction'])->bind('skater_view');

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
        $skaterRepository = new SkaterRepository($app['db']);
        $userRepository = new UserRepository($app['db']);
        $userId = $userRepository->getLoggedUserId($app);

        return $app['twig']->render(
            'skater/index.html.twig',
            ['skater' => $skaterRepository->findAll(),
            'user_id' => $userId,]
        );
    }
    /**
     * View action.
     *
     * @param \Silex\Application $app Silex application
     * @param string             $id  Element Id
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function viewAction(Application $app, $id)
    {
        $skaterRepository = new SkaterRepository($app['db']);
        $userRepository = new UserRepository($app['db']);
        $userId = $userRepository->getLoggedUserId($app);

        return $app['twig']->render(
            'skater/view.html.twig',
            ['skater' => $skaterRepository->findOneById($id),
                'user_id' => $userId,]
        );
    }
}
