<?php
use App\Entity\Category;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\RedirectResponse;

/**Listar**/
$map->get('categories.list','/categories', function ($request, $response) use ($view, $entityManager){
    $repository = $entityManager->getRepository(Category::class);
    $categories = $repository->findAll();
    return $view->render($response,'categories/list.phtml',[
        'categories' => $categories
    ]);
});

/**Inserir**/
$map->get('categories.create','/categories/create', function ($request, $response) use ($view, $entityManager){
    return $view->render($response,'categories/create.phtml');
});

$map->post('categories.store','/categories/store', function (ServerRequestInterface $request, $response) use ($view, $entityManager, $generator){
    /**Recebe os dados da requisicao**/
    $data = $request->getParsedBody();
    $category = new Category();
    $category->setName($data["name"]);
    /**Transforma a entidade em objeto gerenciavel no doctrine**/
    $entityManager->persist($category);

    $entityManager->flush();

    /**Gera o endereco para a rota informanda**/
    $uri = $generator->generate('categories.list');


    return new RedirectResponse($uri);
});

/**Editar**/
$map->get('categories.edit','/categories/{id}/edit', function (ServerRequestInterface $request, $response) use ($view, $entityManager){
    $id = $request->getAttribute('id');
    $repository = $entityManager->getRepository(Category::class);
    $category = $repository->find($id);
    return $view->render($response,'categories/edit.phtml',[
        'category' => $category
    ]);
});

$map->post('categories.update','/categories/{id}/update', function (ServerRequestInterface $request, $response) use ($view, $entityManager, $generator){
    $id = $request->getAttribute('id');
    $repository = $entityManager->getRepository(Category::class);
    $category = $repository->find($id);
    /**Recebe os dados da requisicao**/
    $data = $request->getParsedBody();
    $category->setName($data["name"]);

    $entityManager->flush();

    /**Gera o endereco para a rota informanda**/
    $uri = $generator->generate('categories.list');


    return new RedirectResponse($uri);
});

/**Remover**/
$map->get('categories.remove','/categories/{id}/remove', function (ServerRequestInterface $request, $response) use ($view, $entityManager, $generator){
    $id = $request->getAttribute('id');
    $repository = $entityManager->getRepository(Category::class);
    $category = $repository->find($id);

    $entityManager->remove($category);

    $entityManager->flush();

    /**Gera o endereco para a rota informanda**/
    $uri = $generator->generate('categories.list');


    return new RedirectResponse($uri);
});
