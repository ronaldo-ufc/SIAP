<?php
define('COD_ALMOXARIFADO', 22);

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    $view = new Slim\Views\Twig($settings['template_path']);
    
    $view->addExtension(new \Slim\Views\TwigExtension(
		$c->router,
		$c->request->getUri()
    ));
    return $view;
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

// Register provider
$container['flash'] = function () {
  session_start();
  return new \Slim\Flash\Messages();
};
//Override the default Not Found Handler
//$container['notFoundHandler'] = function ($c) {
//    return function ($request, $response) use ($c) {
//        return $c['response']
//            ->withStatus(200)
//            ->withHeader('Content-Type', 'text/html')
//            ->withRedirect('/siap');
//    };
//};

$container['upload_directory_imagem'] = '/var/www/html/siap/uploads/imagem/';
$container['upload_directory_documento'] = '/var/www/html/siap/uploads/documentos/';

