<?php

namespace App\Infrastructure\Security;

use App\Domain\Report\ReportAccessCheckerInterface;
use App\Shared\Security\UserContext;

class AccessChecker implements ReportAccessCheckerInterface
{
    private UserContext $userContext;

    public function __construct(UserContext $userContext)
    {
        $this->userContext = $userContext;
    }

    public function canViewFinancialReports(): bool
    {
        return in_array('ROLE_USER', $this->userContext->getRoles(), true);
    }
}
