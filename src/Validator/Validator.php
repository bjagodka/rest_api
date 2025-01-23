<?php

declare(strict_types=1);

namespace App\Validator;

use App\Validator\Components\ComponentInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Validator
{
    public function __construct(
        private readonly ValidatorInterface $validator,
    )
    {
    }

    public function valid(ComponentInterface $component): array
    {
        $result = $this->validator->validate($component);

        return array_map(fn($error) => $error->getMessage(),
            iterator_to_array($result)
        );

    }

}