<?php

namespace App\Controller;

use App\dto\ImageDto;
use App\dto\PasswordDto;
use App\dto\ProfileInfoDto;
use App\Entity\Product;
use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\ProductType;
use PhpParser\Node\Scalar\MagicConst\File;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/profile')]
class ProfileController extends AbstractController
{
    #[Route('/', name: 'profile')]
    public function index(): Response
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['email' => $this->getUser()->getUsername()]);

        return $this->render('profile/profile.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/changePassword', name: 'profile_change_password', methods: ['GET', 'POST'])]
    public function changePassword(Request $request, UserPasswordEncoderInterface $passwordEncoder, NotifierInterface $notifier): Response
    {
        $password = new PasswordDto();
        $form = $this->createFormBuilder($password)
            ->add('password', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Введите пароль',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Пароль должен содержать минимум {{ limit }} символов',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
                'label' => 'Пароль'
            ])
            ->add('repeatPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Повторите пароль',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Пароль должен содержать минимум {{ limit }} символов',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
                'label' => 'Повтор пароля'
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy(['email' => $this->getUser()->getUsername()]);

            if ($form->get('password') != $form->get('repeatPassword'))
                $notifier->send(new Notification('Пароли не совпадают', ['browser']));

            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
        }

        return $this->render('profile/change.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/changeInfo', name: 'profile_change_info', methods: ['GET', 'POST'])]
    public function changeProfileInfo(Request $request): Response
    {
        $info = new ProfileInfoDto();
        $form = $this->createFormBuilder($info)
            ->add('info', TextareaType::class, [
                'label' => 'Информация о пользователе'
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy(['email' => $this->getUser()->getUsername()]);

            $user->setInfo($form->get('info')->getData());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
        }

        return $this->render('profile/change.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/changePhoto', name: 'profile_change_photo', methods: ['GET', 'POST'])]
    public function changePhoto(Request $request, SluggerInterface $slugger): Response
    {
        $imageDto = new ImageDto();
        $form = $this->createFormBuilder($imageDto)
            ->add('image', FileType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $entityManager = $this->getDoctrine()->getManager();

            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy(['email' => $this->getUser()->getUsername()]);

            $image = $form->get('image')->getData();

            if($image)
            {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('file_save_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
            }
            $user->setImage($newFilename);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
        }

        return $this->render('profile/change.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}