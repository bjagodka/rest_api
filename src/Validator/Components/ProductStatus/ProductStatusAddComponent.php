<?php

declare(strict_types=1);

namespace App\Validator\Components\ProductStatus;

use App\Validator\Components\ComponentInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ProductStatusAddComponent implements ComponentInterface
{
    #[Assert\Collection(
        fields:[
            "name" => new Assert\NotBlank(message: "Password can not be blank."),
        ],
        allowMissingFields: false,
        missingFieldsMessage: 'Missing {{ field }} field.'
    )]
    public readonly array $fields;

    public function __construct(
        array $fields
    )
    {
        $this->fields = $fields;
    }
}