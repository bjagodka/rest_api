<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ProductStatus;
use App\Entity\User;
use App\Provider\ResponseProvider;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductStatusRepository;
use App\Validator\Components\ProductStatus\ProductStatusAddComponent;
use App\Validator\Components\ProductStatus\ProductStatusSidComponent;
use App\Validator\Components\User\UserSidComponent;
use App\Validator\Components\User\UserUpdateComponent;
use App\Validator\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/product-status', name: 'api_product-status_')]
class ProductStatusController extends AbstractMainController
{
    public function __construct(
        private ProductStatusRepository $productStatusRepository,
        private readonly Validator      $validator, private readonly ProductCategoryRepository $productCategoryRepository,
    )
    {}

    #[Route('/{sid}', name: 'get-single', methods: ['GET'])]
    public function getSingle(Request $request): JsonResponse
    {
        $sid = $request->get('sid');
        $checkSid = new ProductStatusSidComponent(['sid' => $sid]);
        $errors = $this->validator->valid($checkSid);

        if([] !== $errors) {
            return $this->json(new ResponseProvider(
                status: Response::HTTP_CONFLICT,
                message: 'Invalid request.',
                data: null,
                errors: $errors,
            ));
        }

        $productStatus = $this->productStatusRepository->getSingle($sid);
        if(null === $productStatus){
            return $this->json(new ResponseProvider(
                status: Response::HTTP_NOT_FOUND,
                message: 'Product status not found.'
            ));
        }

        return $this->json(new ResponseProvider(
            status: Response::HTTP_OK,
            message: 'Product status found.',
            data: ['product-status' => $productStatus],
        ));
    }

    #[Route('/', name: 'get-all', methods: ['GET'])]
    public function getAll(Request $request): JsonResponse
    {
        $productStatus = $this->productStatusRepository->getAll((int)$request->get('offset', 0), (int)$request->get('limit', 10));

        return $this->json(new ResponseProvider(
            status: Response::HTTP_OK,
            message: 'Product status found.',
            data: ['product-status' => $productStatus],
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

        $checkData = new ProductStatusAddComponent($data);
        $errors = $this->validator->valid($checkData);

        if([] !== $errors) {
            return $this->json(new ResponseProvider(
                status: Response::HTTP_CONFLICT,
                message: 'invalid request',
                data: null,
                errors: $errors,
            ));
        }

        $exist = $this->productStatusRepository->findOneBy(['name' => $data['name']]);
        if(true === $exist instanceof ProductStatus){
            return $this->json(new ResponseProvider(
                status: Response::HTTP_CONFLICT,
                message: 'Name exist in database',
                data: null,
                errors: ['Name exist in database']
            ));
        }

        $result = $this->productStatusRepository->add($data);
        if(true === $result instanceof \Throwable){
            return $this->json(new ResponseProvider(
                status: Response::HTTP_CONFLICT,
                message: 'Something went wrong with adding product status',
                data: null,
                errors: [$result->getMessage()],
            ));
        }

        return $this->json(new ResponseProvider(
            status: Response::HTTP_OK,
            message: 'Product Status added successfully'
        ));
    }

    #[Route('/{sid}', name: 'update', methods: ['PATCH'])]
    public function update(Request $request): JsonResponse
    {
        $sid = $request->get('sid');
        $checkSid = new ProductStatusSidComponent(['sid' => $sid]);
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

        $checkData = new ProductStatusAddComponent($data);
        $errors = $this->validator->valid($checkData);

        if([] !== $errors) {
            return $this->json(new ResponseProvider(
                status: Response::HTTP_CONFLICT,
                message: 'invalid request',
                data: null,
                errors: $errors,
            ));
        }

        $result = $this->productStatusRepository->update($sid, $data);
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
            message: 'Product status updated successfully.'
        ));
    }

    #[Route('/{sid}', name: 'delete', methods: ['DELETE'])]
    public function delete(Request $request): JsonResponse
    {
        $sid = $request->get('sid');
        $checkSid = new ProductStatusSidComponent(['sid' => $sid]);

        $errors = $this->validator->valid($checkSid);
        if([] !== $errors) {
            return $this->json(new ResponseProvider(
                status: Response::HTTP_CONFLICT,
                message: 'Invalid request.',
                data: null,
                errors: $errors,
            ));
        }

        $result = $this->productStatusRepository->delete($sid);
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
            message: 'Product status deleting successfully.'
        ));
    }
}
