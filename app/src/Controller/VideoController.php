<?php
/**
 * Video controller.
 */
namespace Controller;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Repository\VideoRepository;
use Form\FindVideoType;

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
//        $controller->get('/', [$this, 'indexAction'])->bind('video_index');
        $controller->get('/', [$this, 'findMatchingAction'])
            ->bind('video_index');
        $controller->get('/{id}', [$this, 'viewAction'])->bind('video_view');
        $controller->get('/{params}', [$this, 'displayMatchingAction'])
            ->value('params', '')
//            ->value('page', 1)
            ->bind('matching_video_paginated');

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

    public function findMatchingAction(Application $app, Request $request)
    {
        $app['session']->remove('form');
        $video = [];
        $videoRepository = new VideoRepository($app['db']);
        $form = $app['form.factory']->createBuilder(
            FindVideoType::class,
            $video,
            array('championship' => $videoRepository->findChampionship(),
                'year_championship' => $videoRepository->findYear(),
                'skater' => $videoRepository->findSkater(),
                'type'=> $videoRepository->findType()
            )
//            ['video_repository' => new VideoRepository($app['db'])]
        )->getForm();
//        var_dump($video);
        return $app['twig']->render(
            'video/index.html.twig',
            [
                'video' => $videoRepository->findAll(),
                'form' => $form->createView(),
            ]
        );
    }

    public function displayMatchingAction(Application $app, Request $request, $page = 1)
    {
        var_dump(1);
        if(!$app['session']->get('form')) {
            $form = $request->get('video_type');
            $app['session']->set('form', $form);
        }
        $match = $app['session']->get('form');
        $videoRepository = new VideoRepository($app['db']);
        $video = $videoRepository->getMatching($match, 'video');
        var_dump($video);
//        $paginator = $videoRepository->paginateMatchingVideo($video, $page);
        return $app['twig']->render(
            'video/match.html.twig',
            [
                'video' => $video,
            ]
        );
    }
}
