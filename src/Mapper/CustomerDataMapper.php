<?php

declare(strict_types=1);

namespace Sayco\SyliusMauticPlugin\Mapper;

use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Sylius\Component\Customer\Model\CustomerInterface;

final class CustomerDataMapper implements CustomerDataMapperInterface
{
    public function __construct(
        private AddressRepositoryInterface $addressRepository,
    )
    {
    }

    public function getData(CustomerInterface $customer): array
    {
        $customer_data = [
            'firstname' => $customer->getFirstName(),
            'lastname' => $customer->getLastName(),
            'email' => $customer->getEmail(),
            'gender' => $customer->getGender(),
            'date_of_birth' => $customer->getBirthday()->format('c'),
            'phone' => $customer->getPhoneNumber(),
            'optin' => (int) $customer->isSubscribedToNewsletter(),
        ];

        $addresses = $this->addressRepository->findByCustomer($customer);
        $address = reset($addresses);

        if (false === $address instanceof AddressInterface) {
            return $customer_data;
        }

        $address_data = [
            'address1' => $address->getStreet(),
            'city' => $address->getCity(),
            'zipcode' => $address->getPostcode(),
//            'country' => $address->getCountryCode(), - @todo: Mautic expects country name
            'state' => $address->getProvinceName(),
            'company' => $address->getCompany(),
        ];

        $address_data = array_filter($address_data);
        $customer_data = array_merge($customer_data, $address_data);

        return $customer_data;
    }
}
