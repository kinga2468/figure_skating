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

$app->mount('/auth', new AuthController());
$app->mount('/main', new MainController());
$app->mount('/rules', new RulesController());