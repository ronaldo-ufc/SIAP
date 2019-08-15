<?php
use siap\imagem\models\m2brimagem;

$app->get('/', function ($request, $response, $args) {

//  $usuario = Usuario::getByLogin('02064061347');
//  var_dump($usuario);
  return $this->renderer->render($response, 'home.html', array());
   
})->setName('home')->add($auth);




$app->get('/teste', function ($request, $response, $args) {
  
  $posicao = strpos('0201/2019', '0');
  var_dump($posicao);
  //echo $posicao;
  echo substr('0201/2019', $posicao+1);
    
   
});

$app->group('/autenticacao', function() use($app) 
{
  require_once(__DIR__ . '/auth/routes.php');
  
});

$app->group('/usuario', function() use($app) 
{
  require_once(__DIR__ . '/usuario/routes.php');
  
})->add($auth);

$app->group('/setor', function() use($app) 
{

  require_once(__DIR__ . '/setor/routes.php');
  
})->add($auth);

$app->group('/services', function() use($app) 
{
  require_once(__DIR__ . '/services/routes.php');
  
})->add($auth);

$app->group('/cadastro', function() use($app) 
{

  require_once(__DIR__ . '/cadastro/routes.php');
  
})->add($auth);

$app->group('/home', function() use($app) 
{
  require_once(__DIR__ . '/home/routes.php');
  
})->add($auth);

$app->group('/ativo', function() use($app) 
{
 
  require_once(__DIR__ . '/produto/routes.php');
  
})->add($auth);

$app->group('/relatorios', function() use($app) 
{
 
  require_once(__DIR__ . '/relatorios/routes.php');
  
})->add($auth);

$app->group('/materiais', function() use($app) 
{
 
  require_once(__DIR__ . '/material/routes.php');
  
})->add($auth);