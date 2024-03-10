<?php

declare(strict_types=1);

namespace Sayco\SyliusMauticPlugin\Mapper;

use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Sylius\Component\Customer\Model\CustomerInterface;
use Symfony\Component\Intl\Countries;

final class ContactDataMapper implements ContactDataMapperInterface
{

    public function mapFromCustomer(CustomerInterface $customer): array
    {
        $mapping = [
            'firstname' => $customer->getFirstName(),
            'lastname' => $customer->getLastName(),
            'email' => $customer->getEmail(),
            'gender' => $customer->getGender(),
            'date_of_birth' => $customer->getBirthday()->format('c'),
            'phone' => $customer->getPhoneNumber(),
            'optin' => (int) $customer->isSubscribedToNewsletter(),
        ];

        return array_filter($mapping);
    }

    public function mapFromAddress(AddressInterface $address): array
    {
        $mapping = [
            'address1' => $address->getStreet(),
            'city' => $address->getCity(),
            'zipcode' => $address->getPostcode(),
            'country' => Countries::getName($address->getCountryCode()),
            'state' => $address->getProvinceName(),
            'company' => $address->getCompany(),
        ];

        return array_filter($mapping);
    }
}
