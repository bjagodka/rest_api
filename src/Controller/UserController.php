<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Validator\Components\User\UserAddComponent;
use App\Validator\Validator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/user', name: 'api_user_')]
class UserController extends AbstractController
{
    public function __construct(
        //private readonly EntityManagerInterface $em,
        private UserRepository $userRepository,
        private readonly Validator $validator,
    )
    {

    }
    #[Route('/user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig');
    }

    public function getSingle()
    {
        //TODO get single use
    }

    public function getAll()
    {
        //TODO get all users
    }

    #[Route('', name: 'name', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $data = $request->toArray();

        $checkData = new UserAddComponent($data);
        $errors = $this->validator->valid($checkData);

        if([] !== $errors) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userRepository->findOneBy(['email' => $data['email']]);
        if(true === $user instanceof User){
            return $this->json(data: 'user exist', status: Response::HTTP_CONFLICT);
        }

        $result = $this->userRepository->add($data);

        if(null !== $result){
            return $this->json(data: $result, status: Response::HTTP_CONFLICT);
        }

        return $this->json('user added', Response::HTTP_OK);
    }

    public function updateAll()
    {
        // TODO update all parts of user or add
    }

    public function updatePart()
    {
        // TODO update part of user
    }

    public function remove()
    {
        //TODO
    }
}