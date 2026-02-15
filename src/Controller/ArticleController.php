<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

final class ArticleController extends AbstractController
{
#[Route('/admin/article', name: 'article_list', methods: ['GET'])]
public function list(ArticleRepository $articleRepository): Response
{
    $user = $this->getUser(); // utilisateur connecté

    // Récupérer uniquement les articles de cet utilisateur
    $articles = $articleRepository->findByUser($user);

    return $this->render('pages/admin/article/list.html.twig', [
        'articles' => $articles,
    ]);
}



    #[Route('admin/article/new', name: 'article_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $article->setUser($this->getUser());
            $article->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pages/admin/article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('article/{slug}', name: 'article_show', methods: ['GET'])]
    public function show(#[MapEntity(mapping: ['slug' => 'slug'])] Article $article): Response
    {
        return $this->render('pages/article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('admin/article/{id}/edit', name: 'article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');


        if ($article->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas modifier cet article.');
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pages/admin/article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('admin/article/{id}', name: 'article_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');


        if ($article->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer cet article.');
            return $this->redirectToRoute('app_home');
        }

        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->request->get('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }


}
