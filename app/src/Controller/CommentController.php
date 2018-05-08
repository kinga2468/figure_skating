<?php
/**
 * Comment controller.
 */
namespace Controller;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Repository\CommentRepository;
use Repository\UserRepository;
use Repository\VideoRepository;
use Form\CommentType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class CommentController.
 */
class CommentController implements ControllerProviderInterface
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
        $controller->match('/{id}/delete', [$this, 'deleteAction'])
            ->method('GET|POST')
            ->assert('id', '[1-9]\d*')
            ->bind('comment_delete');
        $controller->match('/{id}/edit', [$this, 'editAction'])
            ->method('GET|POST')
            ->assert('id', '[1-9]\d*')
            ->bind('comment_edit');

        return $controller;
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
        $commentRepository = new CommentRepository($app['db']);
        $comment = $commentRepository->findOneById($id);
        $userRepository = new UserRepository($app['db']);
        $userId = $userRepository->getLoggedUserId($app);


        if($comment['users_id'] === $userId or $app['security.authorization_checker']->isGranted('ROLE_ADMIN')) {
            if (!$comment) {
                $app['session']->getFlashBag()->add(
                    'messages',
                    [
                        'type' => 'warning',
                        'message' => 'message.record_not_found',
                    ]
                );

                return $app->redirect($app['url_generator']->generate('video_index'));
            }

            $form = $app['form.factory']->createBuilder(FormType::class, $comment)->add('id', HiddenType::class)->getForm();
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $commentRepository->delete($form->getData());

                $app['session']->getFlashBag()->add(
                    'messages',
                    [
                        'type' => 'success',
                        'message' => 'message.element_successfully_deleted',
                    ]
                );

                return $app->redirect(
                    $app['url_generator']->generate('video_index', ['id' => $id]),
                    301
                );
            }

            return $app['twig']->render(
                'comments/delete.html.twig',
                [
                    'comment' => $comment,
                    'form' => $form->createView(),
                    'user_id' => $userId,
                    'videoId' => $commentRepository->findVideoByComments($id),
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
        $commentRepository = new commentRepository($app['db']);
        $comment = $commentRepository->findOneById($id);
        $userRepository = new UserRepository($app['db']);
        $userId = $userRepository->getLoggedUserId($app);
        $videoId = $commentRepository->findVideoByComments($id);

        //nie pozwala użytkonikowi wchodzić na nie swoje strony, no chyba że jest administratorem
        if($comment['users_id'] === $userId or $app['security.authorization_checker']->isGranted('ROLE_ADMIN')) {

            if (!$comment) {
                $app['session']->getFlashBag()->add(
                    'messages',
                    [
                        'type' => 'warning',
                        'message' => 'message.record_not_found',
                    ]
                );

                return $app->redirect($app['url_generator']->generate('video_index'));
            }

            $form = $app['form.factory']->createBuilder(CommentType::class, $comment)->getForm();
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $commentRepository->save($form->getData());

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
                'comments/edit.html.twig',
                [
                    'comment' => $comment,
                    'form' => $form->createView(),
                    'user_id' => $userId,
                    'videoId' => $videoId,
                ]
            );
        } else {
            throw new AccessDeniedException("You don't have access to this page!");
        }
    }
}
