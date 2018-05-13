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
use Form\SkaterType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

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
//        $controller->get('/', [$this, 'indexAction'])->bind('skater_index');
        $controller->get('/page/{page}', [$this, 'indexAction'])
            ->value('page', 1)
            ->bind('skater_index');
        $controller->get('/{id}', [$this, 'viewAction'])
            ->assert('id', '[1-9]\d*')
            ->bind('skater_view');
        $controller->get('/{id}/page/{page}', [$this, 'viewAction'])
            ->assert('id', '[1-9]\d*')
            ->value('page', 1)
            ->bind('skater_view_paginated');
        $controller->match('/add', [$this, 'addAction'])
            ->method('POST|GET')
            ->bind('skater_add');
        $controller->match('/{id}/edit', [$this, 'editAction'])
            ->method('GET|POST')
            ->assert('id', '[1-9]\d*')
            ->bind('skater_edit');
        $controller->match('/{id}/delete', [$this, 'deleteAction'])
            ->method('GET|POST')
            ->assert('id', '[1-9]\d*')
            ->bind('skater_delete');


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
    public function indexAction(Application $app, $page = 1)
    {
        $skaterRepository = new SkaterRepository($app['db']);
        $userRepository = new UserRepository($app['db']);
        $userId = $userRepository->getLoggedUserId($app);

        return $app['twig']->render(
            'skater/index.html.twig',
            ['paginator' => $skaterRepository->findAllPaginated($page),
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
    public function viewAction(Application $app, $id, $page = 1)
    {
        $skaterRepository = new SkaterRepository($app['db']);
        $userRepository = new UserRepository($app['db']);
        $userId = $userRepository->getLoggedUserId($app);

        return $app['twig']->render(
            'skater/view.html.twig',
            [
                'id' => $id,
                'skater' => $skaterRepository->findOneById($id),
                'user_id' => $userId,
                'paginator'=>$skaterRepository->findSkaterVideoPaginated($id, $page)]
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
        $skater = [];
        $userRepository = new UserRepository($app['db']);
        $userId = $userRepository->getLoggedUserId($app);

//        $token = $app['security.token_storage']->getToken();
//        if (null !== $token) {
//            $user = $token->getUser();
//            $userLogin = $user->getUsername();
//        }

        if($app['security.authorization_checker']->isGranted('ROLE_ADMIN')) {

            $form = $app['form.factory']->createBuilder(
                SkaterType::class,
                $skater
            )->getForm();
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $skaterRepository = new SkaterRepository($app['db']);
                $skaterRepository->save($form->getData());

                $app['session']->getFlashBag()->add(
                    'messages',
                    [
                        'type' => 'success',
                        'message' => 'message.element_successfully_added',
                    ]
                );

                return $app->redirect($app['url_generator']->generate('skater_index'), 301);
            }

            return $app['twig']->render(
                'skater/add.html.twig',
                [
                    'skater' => $skater,
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
        $skaterRepository = new SkaterRepository($app['db']);
        $skater = $skaterRepository->findOneById($id);
        $userRepository = new UserRepository($app['db']);
        $userId = $userRepository->getLoggedUserId($app);

        $skater['date_of_birth'] = new \DateTime($skater['date_of_birth']);

//        $token = $app['security.token_storage']->getToken();
//        if (null !== $token) {
//            $user = $token->getUser();
//            $userLogin = $user->getUsername();
//        }

        if($app['security.authorization_checker']->isGranted('ROLE_ADMIN')) {
            if (!$skater) {
                $app['session']->getFlashBag()->add(
                    'messages',
                    [
                        'type' => 'warning',
                        'message' => 'message.record_not_found',
                    ]
                );

                return $app->redirect($app['url_generator']->generate('skater_index'));
            }

            $form = $app['form.factory']->createBuilder(
                SkaterType::class, $skater)->getForm();
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $skaterRepository->save($form->getData());

                $app['session']->getFlashBag()->add(
                    'messages',
                    [
                        'type' => 'success',
                        'message' => 'message.element_successfully_edited',
                    ]
                );

                return $app->redirect($app['url_generator']->generate('skater_index'), 301);
            }

            return $app['twig']->render(
                'skater/edit.html.twig',
                [
                    'skater' => $skater,
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
        $skaterRepository = new SkaterRepository($app['db']);
        $skater = $skaterRepository->findOneById($id);

        $userRepository = new UserRepository($app['db']);
        $userId = $userRepository->getLoggedUserId($app);

        if($app['security.authorization_checker']->isGranted('ROLE_ADMIN')) {
            if (!$skater) {
                $app['session']->getFlashBag()->add(
                    'messages',
                    [
                        'type' => 'warning',
                        'message' => 'message.record_not_found',
                    ]
                );

                return $app->redirect($app['url_generator']->generate('skater_index'));
            }

            $form = $app['form.factory']->createBuilder(FormType::class, $skater)->add('id', HiddenType::class)->getForm();
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $skaterRepository->delete($form->getData());

                $app['session']->getFlashBag()->add(
                    'messages',
                    [
                        'type' => 'success',
                        'message' => 'message.element_successfully_deleted',
                    ]
                );

                return $app->redirect(
                    $app['url_generator']->generate('skater_index'),
                    301
                );
            }

            return $app['twig']->render(
                'skater/delete.html.twig',
                [
                    'skater' => $skater,
                    'form' => $form->createView(),
                    'user_id' => $userId,
                ]
            );
        } else {
            throw new AccessDeniedException("You don't have access to this page!");
        }
    }
}
