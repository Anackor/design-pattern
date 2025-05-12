<?php

namespace App\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class UserDataDTO
{
    #[Assert\Type("string")]
    private string $name;

    #[Assert\Type("string")]
    private string $email;

    #[Assert\Type("string")]
    public string $password;

    public function __construct(string $name, string $email, string $password)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getEmail() : string
    {
        return $this->email;
    }
}
