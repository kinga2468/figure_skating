<?php
/**
 * Video controller.
 */
namespace Controller;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Repository\VideoRepository;
use Repository\CommentRepository;
use Form\FindVideoType;
use Form\CommentType;
use Form\VideoType;
use Repository\SkaterRepository;
use Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

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
        $controller->get('/{id}', [$this, 'viewAction'])
            ->assert('id', '[1-9]\d*')
            ->bind('video_view');
        $controller->match('/add', [$this, 'addAction'])
            ->method('POST|GET')
            ->bind('video_add');
        $controller->match('/{id}/edit', [$this, 'editAction'])
            ->method('GET|POST')
            ->assert('id', '[1-9]\d*')
            ->bind('video_edit');
        $controller->match('/{id}/delete', [$this, 'deleteAction'])
            ->method('GET|POST')
            ->assert('id', '[1-9]\d*')
            ->bind('video_delete');

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
        $userRepository = new UserRepository($app['db']);
        $userId = $userRepository->getLoggedUserId($app);

        return $app['twig']->render(
            'video/index.html.twig',
            ['video' => $videoRepository->findAll(),
                'championship' => $videoRepository->findChampionship(),
                'year' => $videoRepository->findYear(),
                'skater' => $videoRepository->findSkater(),
                'type' => $videoRepository->findType(),
                'user_id' => $userId,
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
    public function viewAction(Application $app, $id, Request $request)
    {
        $videoRepository = new VideoRepository($app['db']);
        $userRepository = new UserRepository($app['db']);
        $userId = $userRepository->getLoggedUserId($app);
        $commentRepository = new CommentRepository($app['db']);

//        $app['session']->remove('form');
        $comment = [];
        $commentForm = $app['form.factory']->createBuilder(
            CommentType::class,
            $comment,
            ['comment_repository' => new CommentRepository($app['db'])]
        )->getForm();
        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $commentRepository = new CommentRepository($app['db']);
            $comment = $commentForm->getData();
            $comment['users_id'] = $userId;
            $comment['video_id'] = $id;
            $commentRepository->save($comment);

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.element_successfully_added',
                ]
            );
        }

        return $app['twig']->render(
            'video/view.html.twig',
            [
                'video' => $videoRepository->findOneById($id),
                'id' => $id,
                'skater' => $videoRepository->getVideoSkater($id),
                'user_id' => $userId,
                'comments' => $commentRepository->findVideoComments($id),
                'form_comment' => $commentForm->createView(),
            ]
        );
    }

    public function findMatchingAction(Application $app, Request $request)
    {
        $userRepository = new UserRepository($app['db']);
        $userId = $userRepository->getLoggedUserId($app);
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
                'user_id' => $userId,
            ]
        );
    }

    public function displayMatchingAction(Application $app, Request $request, $page = 1)
    {
        $userRepository = new UserRepository($app['db']);
        $userId = $userRepository->getLoggedUserId($app);

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
                'user_id' => $userId,
            ]
        );
    }

    /**
     * Add action.
     *
     * @param \Silex\Application                        $app     Silex application
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP Request
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function addAction(Application $app, Request $request)
    {
        $video = [];
        $userRepository = new UserRepository($app['db']);
        $userId = $userRepository->getLoggedUserId($app);

        $token = $app['security.token_storage']->getToken();
        if (null !== $token) {
            $user = $token->getUser();
            $userLogin = $user->getUsername();
        }

        if($app['security.authorization_checker']->isGranted('ROLE_ADMIN')) {

            $form = $app['form.factory']->createBuilder(
                VideoType::class,
                $video,
                ['skaters_repository' => new SkaterRepository($app['db'])]
            )->getForm();
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $videoRepository = new VideoRepository($app['db']);
                $videoRepository->save($form->getData(), $userLogin);

                $app['session']->getFlashBag()->add(
                    'messages',
                    [
                        'type' => 'success',
                        'message' => 'message.element_successfully_added',
                    ]
                );

                return $app->redirect($app['url_generator']->generate('video_index'), 301);
            }

            return $app['twig']->render(
                'video/add.html.twig',
                [
                    'video' => $video,
                    'form' => $form->createView(),
                    'user_id' => $userId,
                ]
            );
        } else {
            throw new AccessDeniedException("You don't have access to this page!");
        }
    }

    /**
     * Edit action.
     *
     * @param \Silex\Application                        $app     Silex application
     * @param int                                       $id      Record id
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP Request
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function editAction(Application $app, $id, Request $request)
    {
        $videoRepository = new VideoRepository($app['db']);
        $video = $videoRepository->findOneById($id);
        $userRepository = new UserRepository($app['db']);
        $userId = $userRepository->getLoggedUserId($app);

        $token = $app['security.token_storage']->getToken();
        if (null !== $token) {
            $user = $token->getUser();
            $userLogin = $user->getUsername();
        }

        if($app['security.authorization_checker']->isGranted('ROLE_ADMIN')) {
            if (!$video) {
                $app['session']->getFlashBag()->add(
                    'messages',
                    [
                        'type' => 'warning',
                        'message' => 'message.record_not_found',
                    ]
                );

                return $app->redirect($app['url_generator']->generate('video_index'));
            }

            $form = $app['form.factory']->createBuilder(
                VideoType::class,
                $video,
                ['skaters_repository' => new SkaterRepository($app['db'])]
            )->getForm();
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $videoRepository->saveForEdit($form->getData(), $userLogin);

                $app['session']->getFlashBag()->add(
                    'messages',
                    [
                        'type' => 'success',
                        'message' => 'message.element_successfully_edited',
                    ]
                );

                return $app->redirect($app['url_generator']->generate('video_index'), 301);
            }

            return $app['twig']->render(
                'video/edit.html.twig',
                [
                    'video' => $video,
                    'form' => $form->createView(),
                    'user_id' => $userId,
                ]
            );
        } else {
            throw new AccessDeniedException("You don't have access to this page!");
        }
    }

    /**
     * Delete action.
     *
     * @param \Silex\Application                        $app     Silex application
     * @param int                                       $id      Record id
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP Request
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function deleteAction(Application $app, $id, Request $request)
    {
        $videoRepository = new VideoRepository($app['db']);
        $video = $videoRepository->findOneById($id);

        $userRepository = new UserRepository($app['db']);
        $userId = $userRepository->getLoggedUserId($app);

        if($app['security.authorization_checker']->isGranted('ROLE_ADMIN')) {
            if (!$video) {
                $app['session']->getFlashBag()->add(
                    'messages',
                    [
                        'type' => 'warning',
                        'message' => 'message.record_not_found',
                    ]
                );

                return $app->redirect($app['url_generator']->generate('video_index'));
            }

            $form = $app['form.factory']->createBuilder(FormType::class, $video)->add('id', HiddenType::class)->getForm();
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $videoRepository->delete($form->getData());

                $app['session']->getFlashBag()->add(
                    'messages',
                    [
                        'type' => 'success',
                        'message' => 'message.element_successfully_deleted',
                    ]
                );

                return $app->redirect(
                    $app['url_generator']->generate('video_index'),
                    301
                );
            }

            return $app['twig']->render(
                'video/delete.html.twig',
                [
                    'video' => $video,
                    'form' => $form->createView(),
                    'user_id' => $userId,
                ]
            );
        } else {
            throw new AccessDeniedException("You don't have access to this page!");
        }
    }
}
