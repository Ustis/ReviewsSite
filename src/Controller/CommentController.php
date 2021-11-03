<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Product;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\MyCommentType;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/comment')]
class CommentController extends AbstractController
{
    #[Route('/', name: 'comment_index', methods: ['GET'])]
    public function index(CommentRepository $commentRepository): Response
    {
        return $this->render('comment/index.html.twig', [
            'comments' => $commentRepository->findAll(),
        ]);
    }

    #[Route('/{productId}/new', name: 'comment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, int $productId = 1): Response
    {
        $comment = new Comment();
        $form = $this->createForm(MyCommentType::class, $comment);

        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($productId);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $entityManager = $this->getDoctrine()->getManager();

            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy(['email' => $this->getUser()->getUsername()]);

            $comment->setCreator($user);
            $comment->setRelatedToProduct($product);

            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('comment/new.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'comment_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($product);

        $comments = $this->getDoctrine()
            ->getRepository(Comment::class)
            ->findBy(['relatedToProduct' => $product->getId()]);

        $commentsWithUsers = array();

        foreach ($comments as $comment) {
            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy(['id' => $comment->getCreator()]);
            $elem = new \stdClass();
            $elem->creator = $user->getEmail();
            $elem->creatorImage = $user->getImage();
            $elem->text = $comment->getText();
            $elem->addedDate = $comment->getAddedDate();
            array_push($commentsWithUsers, $elem);
        }

        if ($comments == null)
            return $this->render('comment/show.html.twig', [
                'commentsWithUsers' => $commentsWithUsers,
                'productId' => $product->getId(),
                'exist' => false
            ]);
        return $this->render('comment/show.html.twig', [
            'commentsWithUsers' => $commentsWithUsers,
                'productId' => $product->getId(),
            'exist' => true
        ]);
    }

    #[Route('/{id}/edit', name: 'comment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comment $comment): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('comment_index');
        }

        return $this->render('comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'comment_delete', methods: ['POST'])]
    public function delete(Request $request, Comment $comment): Response
    {
        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('comment_index');
    }
}
