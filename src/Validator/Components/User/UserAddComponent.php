<?php

declare(strict_types=1);

namespace App\Validator\Components\User;

use App\Validator\Components\ComponentInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UserAddComponent implements ComponentInterface
{
    #[Assert\Collection(
        fields:[
            "email" => [
                new Assert\NotBlank(message: "Email can not be blank."),
                new Assert\Email(message: "Please enter a valid email address."),
            ],
            "password" => new Assert\NotBlank(message: "Password can not be blank."),
            "role" => new Assert\NotBlank(message: "Role can not be blank."),
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