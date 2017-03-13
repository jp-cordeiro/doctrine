<?php

use App\Entity\Category;
use App\Entity\Post;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\PhpRenderer;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\ServerRequestFactory;
use Aura\Router\RouterContainer;

/**Gera uma requesta baseado na PS7, passando as variaveis globais**/
$request = ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);

/**Container das rotas e configuracoes de rotas**/
$routerContainer = new RouterContainer();

$generator = $routerContainer->getGenerator();

/**Container de mapeamentos de rotas**/
$map = $routerContainer->getMap();

$view = new PhpRenderer(__DIR__.'/../templates/');

$entityManager = getEntityManager();

$map->get('home', '/', function (ServerRequestInterface $request, $response) use ($view, $entityManager) {
    $postsRepository = $entityManager->getRepository(Post::class);
    $categoryRepository = $entityManager->getRepository(Category::class);
    $categories = $categoryRepository->findAll();
    $data = $request->getQueryParams();
    if(isset($data['search']) and $data['search']!=""){
        $queryBuilder = $postsRepository->createQueryBuilder('p');
        $queryBuilder->join('p.categories', 'c')
            ->where($queryBuilder->expr()->eq('c.id', $data['search']));
        $posts = $queryBuilder->getQuery()->getResult();
    }else{
        $posts = $postsRepository->findAll();
    }
    return $view->render($response, 'home.phtml', [
        'posts' => $posts,
        'categories' => $categories
    ]);
});

/**Rotas de categories**/
include_once 'categories.php';

/**Rotas de posts**/
include_once 'posts.php'; 

/**Cobinador, verifica se a requisicao enviada combina com alguma rota configurada**/
$matcher = $routerContainer->getMatcher();

/**Recebe as informacoes se a combinacao tiver sucesso**/
$route = $matcher->match($request);

/**Analisa se a rota existe**/
if($route){
    foreach ($route->attributes as $key => $value){
        /**Aceesa os atributos com a requisicao passada**/
        $request = $request->withAttribute($key,$value);
    }

    /**Manda a response para ser executada**/
    $callable = $route->handler;

    /** @var Response  $response*/
    $response = $callable($request, new Response());

    if($response instanceof RedirectResponse){
        header("location:{$response->getHeader("location")[0]}");
    }elseif ($response instanceof Response){
        echo $response->getBody();
    }
}else{
    echo "Erro 404 - Página não enontrada.";
}
