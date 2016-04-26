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

// give Twig templates access to global variables, dump() function, Slim View Extras, Markdown
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
  $sections = $app->cardsService->getSections();
  $data = array(
    'sections' => $sections
  );
  $app->render('home.html', array('data' => $data));

});

$app->get('/section/:slug', function($slug) use($app) {

  $sections = $app->cardsService->getSections();
  $section = $app->cardsService->getSection($slug);
  $data = array(
    'sections' => $sections,
    'section' => $section
  );

  $app->render('section.html', array('data' => $data));

});

$app->get('/card/:slug', function($slug) use($app) {

  $sections = $app->cardsService->getSections();
  $card = $app->cardsService->getCard($slug);
  $data = array(
    'sections' => $sections,
    'card' => $card,
    'markdown' => $card['markdown']
  );

  $app->render('card.html', array('data' => $data));

});

$app->get('/search/:term', function($term) use($app) {

  $result = $app->cardsService->searchCards($term);
  echo json_encode($result);

});

// TEST ROUTES

/*********************************
    RUN
*********************************/

$app->run();
