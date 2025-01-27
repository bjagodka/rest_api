<?php

declare(strict_types=1);

namespace App\Validator\Components\ProductCategory;

use App\Validator\Components\ComponentInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ProductCategorySidComponent implements ComponentInterface
{
    #[Assert\Collection(
        fields:[
            "sid" => [
                new Assert\NotBlank(message: "Sid can not be blank."),
                new Assert\Uuid(message: "Please enter a valid sid."),
            ],
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