<?php

declare(strict_types=1);

namespace Sayco\SyliusMauticPlugin\Mapper;

use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Customer\Model\CustomerInterface;

interface ContactDataMapperInterface
{
    public function mapFromCustomer(CustomerInterface $customer): array;

    public function mapFromAddress(AddressInterface $address): array;
}
