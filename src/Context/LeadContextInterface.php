<?php

declare(strict_types=1);

namespace Sayco\SyliusMauticPlugin\Context;

interface LeadContextInterface
{
    public function getLeadId(): ?int;
}
