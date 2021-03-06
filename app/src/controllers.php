<?php
/**
 * Routing and controllers.
 *
 * @copyright (c) 2016 Tomasz Chojna
 * @link http://epi.chojna.info.pl
 */
use Controller\AuthController;
use Controller\MainController;
use Controller\RulesController;
use Controller\VideoController;
use Controller\SkaterController;
use Controller\UserController;
use Controller\CommentController;

$app->mount('/', new MainController());
$app->mount('/auth', new AuthController());
$app->mount('/main', new MainController());
$app->mount('/rules', new RulesController());
$app->mount('/video', new VideoController());
$app->mount('/skater', new SkaterController());
$app->mount('/user', new UserController());
$app->mount('/comment', new CommentController());
