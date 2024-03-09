<?php

declare(strict_types=1);

namespace Sayco\SyliusMauticPlugin\Mapper;

use Sylius\Component\Customer\Model\CustomerInterface;

interface CustomerDataMapperInterface
{
    public function getData(CustomerInterface $customer): array;
}
