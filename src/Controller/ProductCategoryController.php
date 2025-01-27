<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ProductStatus;
use App\Provider\ResponseProvider;
use App\Repository\ProductCategoryRepository;
use App\Validator\Components\ProductCategory\ProductCategoryAddComponent;
use App\Validator\Components\ProductCategory\ProductCategorySidComponent;
use App\Validator\Components\ProductStatus\ProductStatusAddComponent;
use App\Validator\Components\ProductStatus\ProductStatusSidComponent;
use App\Validator\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/product-category', name: 'api_product-category_')]
class ProductCategoryController extends AbstractMainController
{
    public function __construct(
        private ProductCategoryRepository $productCategoryRepository,
        private readonly Validator $validator,
    )
    {}

    #[Route('/{sid}', name: 'get-single', methods: ['GET'])]
    public function getSingle(Request $request): JsonResponse
    {
        $sid = $request->get('sid');
        $checkSid = new ProductCategorySidComponent(['sid' => $sid]);
        $errors = $this->validator->valid($checkSid);

        if([] !== $errors) {
            return $this->json(new ResponseProvider(
                status: Response::HTTP_CONFLICT,
                message: 'Invalid request.',
                data: null,
                errors: $errors,
            ));
        }

        $productCategory = $this->productCategoryRepository->getSingle($sid);
        if(null === $productCategory){
            return $this->json(new ResponseProvider(
                status: Response::HTTP_NOT_FOUND,
                message: 'Product category not found.'
            ));
        }

        return $this->json(new ResponseProvider(
            status: Response::HTTP_OK,
            message: 'Product category found.',
            data: ['product-category' => $productCategory],
        ));
    }

    #[Route('/', name: 'get-all', methods: ['GET'])]
    public function getAll(Request $request): JsonResponse
    {
        $productCategory = $this->productCategoryRepository->getAll((int)$request->get('offset', 0), (int)$request->get('limit', 10));

        return $this->json(new ResponseProvider(
            status: Response::HTTP_OK,
            message: 'Product category found.',
            data: ['product-status' => $productCategory],
        ));
    }

    #[Route('/{sid}', name: 'update', methods: ['PATCH'])]
    public function update(Request $request): JsonResponse
    {
        $sid = $request->get('sid');
        $checkSid = new ProductCategorySidComponent(['sid' => $sid]);
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

        $checkData = new ProductCategoryAddComponent($data);
        $errors = $this->validator->valid($checkData);

        if([] !== $errors) {
            return $this->json(new ResponseProvider(
                status: Response::HTTP_CONFLICT,
                message: 'Invalid request.',
                data: null,
                errors: $errors,
            ));
        }

        $result = $this->productCategoryRepository->update($sid, $data);
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
            message: 'Product category updated successfully.'
        ));
    }

    #[Route('', name: 'add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $data = $this->decodeRequest($request);
        if(true === $data instanceof \Throwable){
            return $this->json(new ResponseProvider(
                status: Response::HTTP_CONFLICT,
                message: 'Invalid request.',
                data: null,
                errors: [$data->getMessage()],
            ));
        }

        $checkData = new ProductCategoryAddComponent($data);
        $errors = $this->validator->valid($checkData);

        if([] !== $errors) {
            return $this->json(new ResponseProvider(
                status: Response::HTTP_CONFLICT,
                message: 'invalid request',
                data: null,
                errors: $errors,
            ));
        }

        $exist = $this->productCategoryRepository->findOneBy(['name' => $data['name']]);
        if(true === $exist instanceof ProductCategory){
            return $this->json(new ResponseProvider(
                status: Response::HTTP_CONFLICT,
                message: 'Name exist in database',
                data: null,
                errors: ['Name exist in database']
            ));
        }

        $result = $this->productCategoryRepository->add($data);
        if(true === $result instanceof \Throwable){
            return $this->json(new ResponseProvider(
                status: Response::HTTP_CONFLICT,
                message: 'Something went wrong with adding product category.',
                data: null,
                errors: [$result->getMessage()],
            ));
        }

        return $this->json(new ResponseProvider(
            status: Response::HTTP_OK,
            message: 'Product category added successfully'
        ));
    }

    #[Route('/{sid}', name: 'delete', methods: ['DELETE'])]
    public function delete(Request $request): JsonResponse
    {
        $sid = $request->get('sid');
        $checkSid = new ProductCategorySidComponent(['sid' => $sid]);

        $errors = $this->validator->valid($checkSid);
        if([] !== $errors) {
            return $this->json(new ResponseProvider(
                status: Response::HTTP_CONFLICT,
                message: 'Invalid request.',
                data: null,
                errors: $errors,
            ));
        }

        $result = $this->productCategoryRepository->delete($sid);
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
            message: 'Product category deleting successfully.'
        ));
    }
}
