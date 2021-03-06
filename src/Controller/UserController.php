<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditProfileType;
use App\Form\EditPasswordType;
use App\Form\ProfileImageType;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    #[Route('/profil', name: 'app_user_profile')]
    public function profile(User $user = null, Request $request, ObjectManager $manager): Response
    {
        $user = $this->getUser();
        $formProfileImage =  $this->createForm(ProfileImageType::class, $user);

        $formProfileImage->handleRequest($request);

        if ($formProfileImage->isSubmitted() && $formProfileImage->isValid()) {
            if ($user->getId()) {
                $user->setUpdatedAt(new \DateTime());
            }
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Image de profil mise à jour avec succès !'
            );
            return $this->redirectToRoute('app_user_profile');
        }
        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'formProfileImage' => $formProfileImage->createView()
        ]);
    }

    #[Route('/profil/modifier', name: 'app_user_update')]
    public function editProfile(Request $request, ObjectManager $manager): Response
    {
        $user = $this->getUser();
        $formEditUser =  $this->createForm(EditProfileType::class, $user);
        
        $formEditUser->handleRequest($request);

        if ($formEditUser->isSubmitted() && $formEditUser->isValid()) {
            if ($user->getId()) {
                $user->setUpdatedAt(new \DateTime());
            }
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Vos modifications ont bien été enregistrées !'
            );

            return $this->redirectToRoute('app_user_profile');
        }
        return $this->render('user/editProfile.html.twig', [
            'formEditUser' => $formEditUser->createView(),
        ]);
    }

    #[Route('/profil/modifier/mdp', name: 'app_password_update')]
    public function editPasswordProfile(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = $this->getUser();
        $formEditPasswordUser =  $this->createForm(EditPasswordType::class, $user);
        
        $formEditPasswordUser->handleRequest($request);

        if ($formEditPasswordUser->isSubmitted() && $formEditPasswordUser->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $formEditPasswordUser->get('password')->getData()
                )
            );

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre mot de passe a bien été modifié !'
            );

            return $this->redirectToRoute('app_user_profile');
        }
        return $this->render('user/editPassword.html.twig', [
            'formEditPasswordUser' => $formEditPasswordUser->createView(),
        ]);
    }
}
