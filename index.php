<?
/*********************************
    HOUSEKEEPING
*********************************/

session_start();

// php housekeeping
date_default_timezone_set('America/Denver');

// composer bootstrapping
require 'vendor/autoload.php';


/*********************************
    INITIALIZE SLIM & COMPONENTS
*********************************/

$app = new \Slim\Slim(array(
    'templates.path' => 'templates',
));

// Config params
$app->configs = include('config.php');

// app wide utility functions and constants
define('BASE_URL', 'http://pvcard-alpha.dev');

// prepare Twig view
$app->view(new \Slim\Views\Twig());
$app->view->parserOptions = array(
    'charset' => 'utf-8',
    'cache' => realpath('../templates/cache'),
    'auto_reload' => true,
    'strict_variables' => false,
    'autoescape' => true,
    'debug' => true
);

// give Twig templates access to global variables, dump() function, Slim View Extras
$twig = $app->view->getEnvironment();
$twig->addGlobal('base_url', BASE_URL);
$twig->addGlobal('session', $_SESSION);
$twig->addExtension(new \Twig_Extension_Debug());

// Model services
$app->cardsService = new \JV\CardsService();


/*********************************
    ROUTES
*********************************/

// include all route files
// $routeFiles = (array) glob('src/routes/*.php');
// foreach($routeFiles as $routeFile) {
//     require_once $routeFile;
// }

// HOME
$app->get('/', function() use($app) {

  $cards = $app->cardsService->searchCards('acs');
  echo "<pre>";
  print_r($cards);

});


// TEST ROUTES

/*********************************
    RUN
*********************************/

$app->run();
