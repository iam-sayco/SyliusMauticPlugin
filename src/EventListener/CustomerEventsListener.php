<?php

declare(strict_types=1);

namespace Sayco\SyliusMauticPlugin\EventListener;

use Mautic\Api\Contacts;
use Sayco\SyliusMauticPlugin\Http\Api\ContactsApiInterface;
use Sayco\SyliusMauticPlugin\Mapper\ContactDataMapperInterface;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Sylius\Component\Customer\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class CustomerEventsListener
{
    public function __construct(
        private ContactsApiInterface $contactsApi,
        private ContactDataMapperInterface $contactDataMapper,
        private AddressRepositoryInterface $addressRepository,
    ) {
    }

    public function onCreate(GenericEvent $event): void
    {
        $customer = $event->getSubject();
        assert($customer instanceof CustomerInterface);
        $this->createOrUpdateContact($customer, true);
    }

    public function onUpdate(GenericEvent $event): void
    {
        $customer = $event->getSubject();
        assert($customer instanceof CustomerInterface);
        $this->createOrUpdateContact($customer);
    }

    public function onDelete(GenericEvent $event): void
    {
        $customer = $event->getSubject();
        assert($customer instanceof CustomerInterface);

        $contact = $this->contactsApi->getContactByEmail($customer->getEmail());
        if (null === $contact) {
            return;
        }

        $this->contactsApi->getApi()->delete($contact['id']);
    }

    private function createOrUpdateContact(CustomerInterface $customer, bool $new = false): array
    {
        $mappping = $this->contactDataMapper->mapFromCustomer($customer);
        $contact = $this->contactsApi->getContactByEmail($customer->getEmail());
        $contact_missing = null === $contact;

        if ($new || $contact_missing) {
            $this->addAddressMapping($mappping, $customer);
        }

        if ($contact_missing) {
            return $this->contactsApi->getApi()->create($mappping);
        }

        return $this->contactsApi->getApi()->edit($contact['id'], $mappping);
    }

    private function addAddressMapping(array &$mapping, CustomerInterface $customer): void
    {
        $addresses = $this->addressRepository->findByCustomer($customer);
        $address = reset($addresses);

        if (false === $address instanceof AddressInterface) {
            return;
        }

        $address_mapping = $this->contactDataMapper->mapFromAddress($address);
        $mappping = array_merge($mappping, $address_mapping);
    }
}
