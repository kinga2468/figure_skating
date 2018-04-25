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

$app->mount('/auth', new AuthController());
$app->mount('/main', new MainController());
$app->mount('/rules', new RulesController());
$app->mount('/video', new VideoController());
$app->mount('/skater', new SkaterController());
$app->mount('/signup', new AuthController());