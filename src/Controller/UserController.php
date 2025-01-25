<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Provider\ResponseProvider;
use App\Validator\Components\User\UserAddComponent;
use App\Validator\Components\User\UserSidComponent;
use App\Validator\Components\User\UserUpdateComponent;
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

    #[Route('/{sid}', name: 'get-single', methods: ['GET'])]
    public function getSingle(Request $request): JsonResponse
    {
        $sid = $request->get('sid');
        $checkSid = new UserSidComponent(['sid' => $sid]);

        $errors = $this->validator->valid($checkSid);

        if([] !== $errors) {
            return $this->json(new ResponseProvider(
                status: Response::HTTP_CONFLICT,
                message: 'Invalid request.',
                data: null,
                errors: $errors,
            ));
        }

        $user = $this->userRepository->getSingle($sid);
        if(null === $user instanceof User){
            return $this->json(new ResponseProvider(
                status: Response::HTTP_NOT_FOUND,
                message: 'User not found.'
            ));
        }

        return $this->json(new ResponseProvider(
            status: Response::HTTP_OK,
            message: 'User found.',
            data: [$user],
        ));
    }

    #[Route('/', name: 'get-all', methods: ['GET'])]
    public function getAll(Request $request): JsonResponse
    {
        $users = $this->userRepository->getAll((int)$request->get('offset', 0), (int)$request->get('limit', 10));

        return $this->json(new ResponseProvider(
            status: Response::HTTP_OK,
            message: 'User found.',
            data: ['users' => $users],
        ));
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
            return $this->json(new ResponseProvider(
                status: Response::HTTP_CONFLICT,
                message: 'Something went wrong with saving data',
                data: null,
                errors: [$result],
            ));
        }

        return $this->json(new ResponseProvider(
            status: Response::HTTP_OK,
            message: 'User added successfully'
        ));
    }

    #[Route('/{sid}', name: 'update', methods: ['PATCH'])]
    public function updatePart(Request $request): JsonResponse
    {
        $sid = $request->get('sid');
        $checkSid = new UserSidComponent(['sid' => $sid]);

        $errors = $this->validator->valid($checkSid);
        if([] !== $errors) {
            return $this->json(new ResponseProvider(
                status: Response::HTTP_CONFLICT,
                message: 'Invalid request.',
                data: null,
                errors: $errors,
            ));
        }

        $data = $this->decodeRequest($request);
        if(true === $data instanceof \Throwable){
            return $this->json(new ResponseProvider(
                status: Response::HTTP_CONFLICT,
                message: 'invalid request',
                data: null,
                errors: [$data->getMessage()],
            ));
        }

        $checkData = new UserUpdateComponent($data);
        $errors = $this->validator->valid($checkData);

        if([] !== $errors) {
            return $this->json(new ResponseProvider(
                status: Response::HTTP_CONFLICT,
                message: 'invalid request',
                data: null,
                errors: $errors,
            ));
        }

        $user = $this->userRepository->findOneBy(['sid' => $sid]);
        if(null === $user instanceof User){
            return $this->json(new ResponseProvider(
                status: Response::HTTP_NOT_FOUND,
                message: 'User not found.'
            ));
        }

        $result = $this->userRepository->update($user, $data);
        if(true === $result instanceof \Throwable){
            return $this->json(new ResponseProvider(
                status: Response::HTTP_CONFLICT,
                message: 'Something went wrong with updating data',
                data: null,
                errors: [$result->getMessage()],
            ));
        }

        return $this->json(new ResponseProvider(
            status: Response::HTTP_OK,
            message: 'User updated successfully.'
        ));
    }

    #[Route('/{sid}', name: 'delete', methods: ['DELETE'])]
    public function remove(Request $request): JsonResponse
    {
        $sid = $request->get('sid');
        $checkSid = new UserSidComponent(['sid' => $sid]);

        $errors = $this->validator->valid($checkSid);
        if([] !== $errors) {
            return $this->json(new ResponseProvider(
                status: Response::HTTP_CONFLICT,
                message: 'Invalid request.',
                data: null,
                errors: $errors,
            ));
        }

        $result = $this->userRepository->delete($sid);
        if(true === $result instanceof \Throwable){
            return $this->json(new ResponseProvider(
                status: Response::HTTP_CONFLICT,
                message: 'Something went wrong with deleting data',
                data: null,
                errors: [$result->getMessage()],
            ));
        }

        return $this->json(new ResponseProvider(
            status: Response::HTTP_OK,
            message: 'User deleting successfully.'
        ));
    }
}
