<?php
require 'vendor/autoload.php';
require 'BasketController.php';
// instantiate the App object
$app = new \Slim\App([
	'settings' => [
		'displayErrorDetails' => true
	]
]);

// Get container
$container = $app->getContainer();

// Register component on container
// Register Twig View helper
$container['view'] = function ($c) {
	$view = new \Slim\Views\Twig('/var/www/html/template', []);
	
	// Instantiate and add Slim specific extension
	$router = $c->get('router');
	$uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
	$view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

	return $view;
};

$app->get('/', function ($request, $response, $args) {
	$basketController = new BasketController($this->view);
	return $basketController->view($response, $request);
});
// Run application
$app->run();
