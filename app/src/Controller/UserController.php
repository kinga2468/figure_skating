<?php
/**
 * User controller.
 * Controller do zarządzania użytkownikami przez admina
 */
namespace Controller;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Form\RoleType;
use Form\ForPasswordType;
use Form\PanelEditType;
use Repository\SignUpRepository;
use Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;



/**
 * Class UserController.
 *
 * @package Controller
 */
class UserController implements ControllerProviderInterface
{
    /**
     * Routing settings.
     * @param Application $app
     * @return mixed
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->get('/', [$this, 'indexAction'])->bind('user_index');
        $controller->get('/page/{page}', [$this, 'indexAction'])
            ->value('page', 1)
            ->bind('user_index_paginated');
        $controller->get('/{id}', [$this, 'viewAction'])
            ->assert('id', '[1-9]\d*')
            ->bind('user_view');
        $controller->match('/{id}/editPassword', [$this, 'editActionPassword'])
            ->method('GET|POST')
            ->assert('id', '[1-9]\d*')
            ->bind('user_editPassword');
        $controller->match('/{id}/editPanel', [$this, 'editActionPanel'])
            ->method('GET|POST')
            ->assert('id', '[1-9]\d*')
            ->bind('user_editPanel');
        $controller->match('/{id}/delete', [$this, 'deleteAction'])
            ->method('GET|POST')
            ->assert('id', '[1-9]\d*')
            ->bind('user_delete');
        $controller->match('/{id}/change', [$this, 'changeRole'])
            ->method('GET|POST')
            ->assert('id', '[1-9]\d*')
            ->bind('user_change');


        return $controller;
    }

    /**
     * Index action.
     * Wyświetlenie wszystkich użytkowników przez admina (paginacja)
     *
     * @param Application $app
     * @param int $page
     * @return mixed
     */
    public function indexAction(Application $app, $page=1)
    {
        $signupRepository = new SignUpRepository($app['db']);
        $userRepository = new UserRepository($app['db']);
        $userId = $userRepository->getLoggedUserId($app);

//        $token = $app['security.token_storage']->getToken();
//        if (null !== $token) {
//            $user = $token->getUser();
//            $userLogin = $user->getUsername();
//        }

        return $app['twig']->render(
            'user/index.html.twig',
                [
                    'user' => $signupRepository-> findAll(),
                    'user_id' => $userId,
//                    'user_id' => $userRepository->findUserId($userLogin),
                    'paginator' => $signupRepository->findAllPaginated($page)
                ]
        );
    }

    /**
     * View action.
     * Podgląd konkretnego użytkownika przez admina
     *
     * @param Application $app
     * @param $id
     * @return mixed
     */
    public function viewAction(Application $app, $id)
    {
        $signupRepository = new SignUpRepository($app['db']);
        $userRepository = new UserRepository($app['db']);
        $userId = $userRepository->getLoggedUserId($app);

        return $app['twig']->render(
            'user/view.html.twig',
            ['user' => $signupRepository->findOneById($id),
                'user_id' => $userId,
            ]
        );
    }

    /**
     * Edit action
     * Edytowanie hasła użytkownika przez admina
     *
     * @param Application $app
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editActionPassword(Application $app, $id, Request $request)
    {
        $signUpRepository = new SignUpRepository($app['db']);
        $user = $signUpRepository->findOneById($id);
        $userRepository = new UserRepository($app['db']);
        $userId = $userRepository->getLoggedUserId($app);


        if (!$user) {
            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'warning',
                    'message' => 'message.record_not_found',
                ]
            );

            return $app->redirect($app['url_generator']->generate('user_view', array('id' => $id)));
        }

        $form = $app['form.factory']->createBuilder(ForPasswordType::class, $user)->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $signUpRepository->save2($form->getData(), $app);

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.element_successfully_edited',
                ]
            );

            return $app->redirect($app['url_generator']->generate('user_view', array('id' => $id)), 301);
        }

        return $app['twig']->render(
            'user/passwordByAdmin.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
                'user_id' => $userId,
            ]
        );
    }

    /**
     * Edit action
     * Edycja danych użytkownika przez admina, bez edycji hasła
     *
     * @param Application $app
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editActionPanel(Application $app, $id, Request $request)
    {
        $signUpRepository = new SignUpRepository($app['db']);
        $user = $signUpRepository->findOneById($id);
        $userRepository = new UserRepository($app['db']);
        $userId = $userRepository->getLoggedUserId($app);


        if (!$user) {
            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'warning',
                    'message' => 'message.record_not_found',
                ]
            );

            return $app->redirect($app['url_generator']->generate('user_view', array('id' => $id)));
        }

       $form = $app['form.factory']->createBuilder(PanelEditType::class, $user,
           ['login_repository' => new SignUpRepository($app['db'])]
       )->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $signUpRepository->save3($form->getData(), $app);

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.element_successfully_edited',
                ]
            );

            return $app->redirect($app['url_generator']->generate('user_view', array('id' => $id)), 301);
        }

        return $app['twig']->render(
            'user/editProfilByAdmin.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
                'user_id' => $userId,
            ]
        );
    }

    /**
     * Delete action
     * Usuwanie użytkownia przez admina
     *
     * @param Application $app
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Application $app, $id, Request $request)
    {
        $userRepository = new SignUpRepository($app['db']);
        $user = $userRepository->findOneById($id);
        $userRepository = new UserRepository($app['db']);
        $userId = $userRepository->getLoggedUserId($app);

        if (!$user) {
            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'warning',
                    'message' => 'message.record_not_found',
                ]
            );

            return $app->redirect($app['url_generator']->generate('user_index'));
        }

        $form = $app['form.factory']->createBuilder(FormType::class, $user)->add('id', HiddenType::class)->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->delete($form->getData());

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.element_successfully_deleted',
                ]
            );

            return $app->redirect(
                $app['url_generator']->generate('user_index'),
                301
            );
        }

        return $app['twig']->render(
            'user/delete.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
                'user_id' => $userId,
            ]
        );
    }

    /**
     * Change role
     * Zmienia role użytkownika przez admina (admin, user)
     *
     * @param Application $app
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function changeRole(Application $app, $id, Request $request)
    {
        $signUpRepository = new SignUpRepository($app['db']);
        $user = $signUpRepository->findOneById($id);
        $userRepository = new UserRepository($app['db']);
        $userId = $userRepository->getLoggedUserId($app);

        if (!$user) {
            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'warning',
                    'message' => 'message.record_not_found',
                ]
            );

            return $app->redirect($app['url_generator']->generate('user_index'));
        }

        $form = $app['form.factory']->createBuilder(RoleType::class, $user)->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $signUpRepository->save3($form->getData(), $app);

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.element_successfully_edited',
                ]
            );

            return $app->redirect($app['url_generator']->generate('user_index'), 301);
        }

        return $app['twig']->render(
            'user/change.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
                'user_id' => $userId,
            ]
        );
    }
}