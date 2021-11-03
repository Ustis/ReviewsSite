<?php

namespace App\Controller;

use App\dto\ImageDto;
use App\dto\PasswordDto;
use App\dto\ProfileInfoDto;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use App\Service\FileUpLoader;

#[Route('/profile')]
class ProfileController extends AbstractController
{
    #[Route('/', name: 'profile')]
    public function index(NotifierInterface $notifier): Response
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
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Введите пароль',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Пароль должен содержать минимум {{ limit }} символов',
                        'max' => 4096,
                    ]),
                ],
                'label' => 'Пароль'
            ])
            ->add('repeatPassword', PasswordType::class, [
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Повторите пароль',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Пароль должен содержать минимум {{ limit }} символов',
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

            if ($form->get('password')->getData() !== $form->get('repeatPassword')->getData())
                $notifier->send(new Notification('Пароли не совпадают', ['browser']));
            else{
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
            }
        }

        return $this->render('profile/change.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/changeInfo', name: 'profile_change_info', methods: ['GET', 'POST'])]
    public function changeProfileInfo(Request $request, NotifierInterface $notifier): Response
    {
        $info = new ProfileInfoDto();
        $form = $this->createFormBuilder($info)
            ->add('info', TextareaType::class, [
                'label' => 'Информация о пользователе'
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
    public function changePhoto(Request $request, NotifierInterface $notifier, FileUploader $fileUploader): Response
    {
        $imageDto = new ImageDto();
        $form = $this->createFormBuilder($imageDto)
            ->add('image', FileType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy(['email' => $this->getUser()->getUsername()]);

            $image = $form->get('image')->getData();

            if($image)
            {
                try {
                    $fileName = $fileUploader->upload($image);

                    $user->setImage($fileName);

                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($user);
                    $entityManager->flush();
                }
                catch (FileException $e){
                    $notifier->send(new Notification('Произошла ошибка при сохранении файла.', ['browser']));
                }
            }
        }

        return $this->render('profile/change.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}