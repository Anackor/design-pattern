<?php

namespace App\Shared\Security;

class UserContext
{
    private array $roles;

    public function __construct(array $roles)
    {
        $this->roles = $roles;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }
}
