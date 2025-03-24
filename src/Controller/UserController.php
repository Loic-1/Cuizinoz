<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserPictureType;
use App\Form\UserType;
use App\Repository\CommentRepository;
use App\Repository\CompilationRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends AbstractController
{
    // Renvoie une liste des utilisateurs [INUTILE]
    #[Route('/user', name: 'app_user')]
    public function listUsers(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('user/index.html.twig', [
            'users' => $users
        ]);
    }

    // Renvoie l'utilisateur spécifié, ceux qui le suivent et ceux qu'il suit
    #[Route('/detail/{user}', name: 'detail_user')]
    public function detailUser(User $user = null, UserRepository $userRepository, CompilationRepository $compilationRepository, RecipeRepository $recipeRepository, CommentRepository $commentRepository): Response
    {
        if ($user) {

            $followers = $user->getFollowers();
            $followees = $user->getFollowees();
            $users = $userRepository->findAll();
            // limit = 3
            $compilations = $compilationRepository->findLastCompilationsByUserId($user->getId(), 3);
            $recipes = $recipeRepository->findBestRecipesByUserId($user->getId(), 3);
            $comments = $commentRepository->findLastCommentsByUserId($user->getId(), 3);

            $metaDescription = "Vous aimez cuisiner ? Accédez au profil de " . $user->getPseudo() . ", découvrez ses infos, et retrouvez ses créations de recettes, collections et derniers commentaires !";

            return $this->render('user/detailUser.html.twig', [
                'user' => $user,
                'followers' => $followers,
                'followees' => $followees,
                'users' => $users,
                'compilations' => $compilations,
                'recipes' => $recipes,
                'comments' => $comments,
                'metaDescription' => $metaDescription,
            ]);
        } else {
            return $this->redirectToRoute('app_home');
        }
    }

    // Renvoie l'utilisateur spécifié, ceux qui le suivent et ceux qu'il suit
    #[Route('/detailOwn', name: 'detailOwn_user')]
    public function detailOwnUser(UserRepository $userRepository, CompilationRepository $compilationRepository, RecipeRepository $recipeRepository, CommentRepository $commentRepository): Response
    {
        if (!$this->getUser()) {
            return new JsonResponse(["error" => "Vous etre pas utilisateur"], 401);
        }

        $user = $this->getUser();

        $followers = $user->getFollowers();
        $followees = $user->getFollowees();

        $compilations = $compilationRepository->findLastCompilationsByUserId($user->getId(), 3);
        $recipes = $recipeRepository->findBestRecipesByUserId($user->getId(), 3);
        $comments = $commentRepository->findLastCommentsByUserId($user->getId(), 3);

        $metaDescription = "Vous aimez cuisiner ? Accédez à votre page personnelle, modifiez vos infos, et retrouvez vos créations de recettes, collections et derniers commentaires !";

        return $this->render('user/detailOwnUser.html.twig', [
            'user' => $user,
            'followers' => $followers,
            'followees' => $followees,
            'compilations' => $compilations,
            'recipes' => $recipes,
            'comments' => $comments,
            'metaDescription' => $metaDescription,
        ]);
    }

    // Permet au follower de suivre le followee (tous deux des users)
    #[Route('/follow/{follower}/{followee}', name: 'follow_user')]
    public function followUser(User $follower = null, User $followee = null, EntityManagerInterface $entityManager): Response
    {

        // Empêche un user de se suivre lui-même
        if ($follower && $followee && ($follower != $followee)) {

            $follower->addFollowee($followee);
            $followee->addFollower($follower);

            $entityManager->persist($follower);
            $entityManager->persist($followee);
            $entityManager->flush();

            return $this->redirectToRoute('detail_user', [
                'user' => $follower->getId()
            ]);
        } else {

            return $this->redirectToRoute('app_home');
        }
    }

    // Permet au follower d'arrêter de suivre le followee
    #[Route('/unfollow/{follower}/{followee}', name: 'unfollow_user')]
    public function unfollowUser(User $follower = null, User $followee = null, EntityManagerInterface $entityManager): Response
    {

        if ($follower && $followee && ($follower != $followee)) {

            $follower->removeFollowee($followee);
            $followee->removeFollower($follower);

            $entityManager->persist($follower);
            $entityManager->persist($followee);
            $entityManager->flush();

            return $this->redirectToRoute('detail_user', [
                'user' => $follower->getId()
            ]);
        } else {

            return $this->redirectToRoute('app_home');
        }
    }

    // Permet de modifier les attributs du user spécifié
    #[Route('/edit/{user}', name: 'edit_user')]
    public function editUser(EntityManagerInterface $entityManager, Request $request): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('detailOwn_user');
        }

        $metaDescription = "Vous aimez cuisiner ? Modifiez vos infos personnelles et personnalisez votre profil !";

        return $this->render('user/editUser.html.twig', [
            'user' => $user->getId(),
            'editUserForm' => $form,
            'metaDescription' => $metaDescription,
        ]);
    }

    // NOT USED YET
    #[Route('/editProfilePicture/{user}', name: 'edit_picture_user')]
    public function editPictureUser(User $user = null, EntityManagerInterface $entityManager, Request $request): Response
    {

        if ($user) {

            $form = $this->createForm(UserPictureType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $user = $form->getData();

                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirectToRoute('detail_user', [
                    'user' => $user->getId()
                ]);
            }
            return $this->render('user/editPictureUser.html.twig', [
                'editPictureUserForm' => $form,
                'user' => $user
            ]);
        } else {

            return $this->redirectToRoute('app_home');
        }
    }

    #[Route('/deleteProfile', name: "delete_user")]
    public function deleteUser(EntityManagerInterface $entityManager)
    {
        if (!$this->getUser()) {
            return new JsonResponse(["error" => "Vous etre pas utilisateur"], 401);
        }

        $user = $this->getUser();

        $user->setPseudo("Anonyme");
        $user->setBiography("Anonyme");
        $user->setProfilePicture(null);
        $user->setEmail(hash('sha256', $user->getEmail()) . uniqid());

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_logout');
    }
}
