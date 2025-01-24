<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Provider\ResponseProvider;
use App\Validator\Components\User\UserAddComponent;
use App\Validator\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/user', name: 'api_user_')]
class UserController extends AbstractMainController
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
        $data = $this->decodeRequest($request);
        if(true === $data instanceof \Throwable){
            return $this->json(new ResponseProvider(
                status: Response::HTTP_CONFLICT,
                message: 'invalid request',
                data: null,
                errors: [$data->getMessage()],
            ));
        }

        $checkData = new UserAddComponent($data);
        $errors = $this->validator->valid($checkData);

        if([] !== $errors) {
            return $this->json(new ResponseProvider(
                status: Response::HTTP_CONFLICT,
                message: 'invalid request',
                data: null,
                errors: $errors,
            ));
        }

        $user = $this->userRepository->findOneBy(['email' => $data['email']]);
        if(true === $user instanceof User){
            //return $this->json(data: 'user exist', status: Response::HTTP_CONFLICT);
            return $this->json(new ResponseProvider(
                status: Response::HTTP_CONFLICT,
                message: 'User exists',
                data: null,
                errors:['User exist in database'],
            ));
        }

        $result = $this->userRepository->add($data);
        if(null !== $result){
            //return $this->json(data: $result, status: Response::HTTP_CONFLICT);
            return $this->json(new ResponseProvider(
                status: Response::HTTP_CONFLICT,
                message: 'Something went wrong with saving data',
                data: null,
                errors: [$result],
            ));
        }

        //return $this->json('user added', Response::HTTP_OK);
        return $this->json(new ResponseProvider(
            status: Response::HTTP_OK,
            message: 'User added successfully'
        ));

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
