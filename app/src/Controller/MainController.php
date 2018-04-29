<?php
/**
 * Main controller.
 */
namespace Controller;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Repository\VideoRepository;
use Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Repository\SignUpRepository;
use Form\ForPasswordType;
use Form\PanelEditType;

/**
 * Class MainController.
 */
class MainController implements ControllerProviderInterface
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
        $controller->get('/', [$this, 'indexAction'])->bind('main_index');

        $controller->get('/panel/{id}', [$this, 'viewActionPanelUser'])
            ->assert('id', '[1-9]\d*')
            ->bind('user_panel');
//        $controller->match('/panel/{id}/edit', [$this, 'editActionPanelUser'])
//            ->method('POST|GET')
//            ->assert('id', '[1-9]\d*')
//            ->bind('user_panel_edit');
//        $controller->match('/panel/{id}/edit_password', [$this, 'editActionForUserPassword'])
//            ->method('POST|GET')
//            ->assert('id', '[1-9]\d*')
//            ->bind('user_panel_edit_password');

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
            'main/index.html.twig',
            ['video' => $videoRepository->findNewestVideo(),
                'user_id' => $userId,]
        );
    }

    /**
     * View action
     * pozwala użytkownikowi zobaczyć swój panel użytkownika
     *
     * @param Application $app
     * @param $id
     * @return mixed
     */
    public function viewActionPanelUser(Application $app, $id)
    {
        $signupRepository = new SignUpRepository($app['db']);
        $userRepository = new UserRepository($app['db']);
        $user = $signupRepository->findOneById($id);
        $userId = $userRepository->getLoggedUserId($app);

        //nie pozwala użytkonikowi wchodzić na nie swoje strony, no chyba że jest administratorem
        if($user['id'] === $userId or $app['security.authorization_checker']->isGranted('ROLE_ADMIN')) {

            $token = $app['security.token_storage']->getToken();
            if (null !== $token) {
                $user = $token->getUser();
                $userLogin = $user->getUsername();
            }

            return $app['twig']->render(
                'user/panel.html.twig',
                [
                    'user' => $signupRepository->findOneById($id),
                    'user_id' => $userId,
                ]
            );
        } else {
            throw new AccessDeniedException("You don't have access to this page!");
        }
    }

//    /**
//     * Edit action
//     * edytowanie danych użytkownika przez użytkownika (w swoim panelu) - edycja bez hasła
//     *
//     * @param Application $app
//     * @param $id
//     * @param Request $request
//     * @return \Symfony\Component\HttpFoundation\RedirectResponse
//     */
//    public function editActionPanelUser(Application $app, $id, Request $request)
//    {
//        $signUpRepository = new SignUpRepository($app['db']);
//        $user = $signUpRepository->findOneById($id);
//        $userRepository = new UserRepository($app['db']);
//        $userId = $userRepository->getLoggedUserId($app);
//
//        //nie pozwala użytkonikowi wchodzić na nie swoje strony, no chyba że jest administratorem
//        if($user['id'] === $userId  or $app['security.authorization_checker']->isGranted('ROLE_ADMIN')) {
//
//            if (!$user) {
//                $app['session']->getFlashBag()->add(
//                    'messages',
//                    [
//                        'type' => 'warning',
//                        'message' => 'message.record_not_found',
//                    ]
//                );
//
//                return $app->redirect($app['url_generator']->generate('user_panel', array('id' => $id)));
//            }
//
//            $form = $app['form.factory']->createBuilder(PanelEditType::class, $user,
//                ['login_repository' => new SignUpRepository($app['db'])]
//            )->getForm();
//            $form->handleRequest($request);
//
//            if ($form->isSubmitted() && $form->isValid()) {
//                $signUpRepository->save3($form->getData(), $app);
//
//                $app['session']->getFlashBag()->add(
//                    'messages',
//                    [
//                        'type' => 'success',
//                        'message' => 'message.element_successfully_edited',
//                    ]
//                );
//
//                return $app->redirect($app['url_generator']->generate('user_panel', array('id' => $id)), 301);
//            }
//
//            return $app['twig']->render(
//                'user/editprofil.html.twig',
//                [
//                    'user' => $user,
//                    'form' => $form->createView(),
//                    'user_id' => $id,
//                ]
//            );
//        } else {
//            throw new AccessDeniedException("You don't have access to this page!");
//        }
//    }
//
//    /**
//     * Edit action
//     * edytowanie hasła użytkownika przez użytkownika (w swoim panelu)
//     *
//     * @param Application $app
//     * @param $id
//     * @param Request $request
//     * @return \Symfony\Component\HttpFoundation\RedirectResponse
//     */
//    public function editActionForUserPassword(Application $app, $id, Request $request)
//    {
//        $signUpRepository = new SignUpRepository($app['db']);
//        $user = $signUpRepository->findOneById($id);
//        $userRepository = new UserRepository($app['db']);
//        $userId = $userRepository->getLoggedUserId($app);
//
//        //nie pozwala użytkonikowi wchodzić na nie swoje strony, no chyba że jest administratorem
//        if($user['id'] === $userId or $app['security.authorization_checker']->isGranted('ROLE_ADMIN')) {
//
//            if (!$user) {
//                $app['session']->getFlashBag()->add(
//                    'messages',
//                    [
//                        'type' => 'warning',
//                        'message' => 'message.record_not_found',
//                    ]
//                );
//
//                return $app->redirect($app['url_generator']->generate('user_panel', array('id' => $id)));
//            }
//
//            $form = $app['form.factory']->createBuilder(ForPasswordType::class, $user)->getForm();
//            $form->handleRequest($request);
//
//            if ($form->isSubmitted() && $form->isValid()) {
//                $signUpRepository->save2($form->getData(), $app);
//
//                $app['session']->getFlashBag()->add(
//                    'messages',
//                    [
//                        'type' => 'success',
//                        'message' => 'message.element_successfully_edited',
//                    ]
//                );
//
//                return $app->redirect($app['url_generator']->generate('user_panel', array('id' => $id)), 301);
//            }
//
//            return $app['twig']->render(
//                'user/password.html.twig',
//                [
//                    'user' => $user,
//                    'form' => $form->createView(),
//                    'user_id' => $id,
//                ]
//            );
//        } else {
//            throw new AccessDeniedException("You don't have access to this page!");
//        }
//    }


}
