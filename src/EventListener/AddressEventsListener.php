<?php

declare(strict_types=1);

namespace Sayco\SyliusMauticPlugin\EventListener;

use Mautic\Api\Contacts;
use Sayco\SyliusMauticPlugin\Http\Api\ContactsApiInterface;
use Sayco\SyliusMauticPlugin\Mapper\ContactDataMapperInterface;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Core\Model\AddressInterface as CoreAddressInterface;
use Sylius\Component\Customer\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class AddressEventsListener
{
    public function __construct(
        private ContactsApiInterface $contactsApi,
        private ContactDataMapperInterface $customerDataMapper,
    ) {
    }

    public function onSave(GenericEvent $event): void
    {
        $address = $event->getSubject();
        assert($address instanceof AddressInterface);
        assert($address instanceof CoreAddressInterface);
        $this->createOrUpdateContact($address);
    }

    private function createOrUpdateContact(AddressInterface|CoreAddressInterface $address): ?array
    {
        $customer = $address->getCustomer();
        if (false === $customer instanceof CustomerInterface) {
            return null;
        }

        $address_mapping = $this->customerDataMapper->mapFromAddress($address);
        $contact = $this->contactsApi->getContactByEmail($customer->getEmail());

        if (null === $contact) {
            $customer_mapping = $this->customerDataMapper->mapFromCustomer($customer);
            $mapping = array_merge($customer_mapping, $address_mapping);
            return $this->contactsApi->getApi()->create($mapping);
        }

        return $this->contactsApi->getApi()->edit($contact['id'], $address_mapping);
    }
}
