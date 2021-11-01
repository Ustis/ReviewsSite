<?php


namespace App\Controller;

use App\Form\ChangeInfoType;
use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChangeProfileInfoController extends AbstractController
{
    #[Route('/changeProfile', name: 'changeProfile')]
    public function index(): Response
    {
        $user = $this->getUser();
        $newUserInfo = new ChangeInfoType();
        $form = $this->createForm(ProductType::class, $newUserInfo);
        $form->handleRequest();

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

//            if($user->getInfo() == $newUserInfo->);

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('product_index');
        }
//
//        return $this->render('product/new.html.twig', [
//            'product' => $product,
//            'form' => $form->createView(),
//        ]);



        $user = $this->getUser()->getUsername();

        return $this->render('about/index.html.twig', [
            'userInfo' => $this->getUser(),
        ]);
    }
}