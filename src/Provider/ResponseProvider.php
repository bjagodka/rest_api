<?php

declare(strict_types=1);

namespace App\Provider;

use http\Client\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ResponseProvider
{
    public function __construct(
        public int $status = \Symfony\Component\HttpFoundation\Response::HTTP_OK,
        public ?string $message = null,
        public ?array $data = null,
        public ?array $errors = null,
    )
    {
    }

    public function createResponse(): array
    {
        return [
            'status' => $this->status,
            'message' => $this->message,
            'data' => $this->data,
            'errors' => $this->errors,
        ];
    }
}