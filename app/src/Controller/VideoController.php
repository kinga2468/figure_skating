<?php
/**
 * Video controller.
 */
namespace Controller;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Repository\VideoRepository;

/**
 * Class VideoController.
 */
class VideoController implements ControllerProviderInterface
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
        $controller->get('/', [$this, 'indexAction'])->bind('video_index');
        $controller->get('/{id}', [$this, 'viewAction'])->bind('video_view');

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
        $videoRepository = new VideoRepository($app['db']);

        return $app['twig']->render(
            'video/index.html.twig',
            ['video' => $videoRepository->findAll()]
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
        $videoRepository = new VideoRepository($app['db']);

        return $app['twig']->render(
            'video/view.html.twig',
            ['video' => $videoRepository->findOneById($id)]
        );
    }
}
