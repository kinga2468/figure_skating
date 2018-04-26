<?php
/**
 * Video controller.
 */
namespace Controller;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Repository\VideoRepository;
use Form\VideoType;

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
//        $controller->get('/', [$this, 'findMatchingAction'])->bind('video_index');
        $controller->get('/{id}', [$this, 'viewAction'])->bind('video_view');
//        $controller->get('/{params}', [$this, 'displayMatchingAction'])
//            ->value('params', '')
//            ->bind('matching_video');

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
            ['video' => $videoRepository->findAll(),
                'championship' => $videoRepository->findChampionship(),
                'year' => $videoRepository->findYear(),
                'skater' => $videoRepository->findSkater(),
                'type' => $videoRepository->findType(),
                ]
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
//        $skater = $videoRepository->getVideoSkater($id);
//        var_dump($skater);

        return $app['twig']->render(
            'video/view.html.twig',
            ['video' => $videoRepository->findOneById($id),
            'skater' => $videoRepository->getVideoSkater($id)]
        );
    }

//    public function findMatchingAction(Application $app, Request $request)
//    {
//        $app['session']->remove('form');
//        $video = [];
//        $form = $app['form.factory']->createBuilder(
//            VideoType::class,
//            $video,
//            ['video_repository' => new VideoRepository($app['db'])]
//        )->getForm();
//        return $app['twig']->render(
//            'video/index.html.twig',
//            [
//                'form' => $form->createView(),
//            ]
//        );
//    }
//
//    public function displayMatchingAction(Application $app, Request $request)
//    {
//        if(!$app['session']->get('form')) {
//            $form = $request->get('video_type');
//            $app['session']->set('form', $form);
//        }
//        $match = $app['session']->get('form');
//        $videoRepository = new VideoRepository($app['db']);
//        $video = $videoRepository->getMatching($match, 'video');
////        $paginator = $videoRepository->paginateMatchingOffers($offers, $page);
//        return $app['twig']->render(
//            'main/index.html.twig',
//            [
//                'video' => $video,
//            ]
//        );
//    }
}
