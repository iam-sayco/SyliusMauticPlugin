<?php

declare(strict_types=1);

namespace Sayco\SyliusMauticPlugin\EventListener;

use Mautic\Api\Contacts;
use Sayco\SyliusMauticPlugin\Http\Api\ContactsApiInterface;
use Sayco\SyliusMauticPlugin\Mapper\CustomerDataMapperInterface;
use Sylius\Component\Customer\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class CustomerEventsListener
{
    public function __construct(
        private ContactsApiInterface $contactsApi,
        private CustomerDataMapperInterface $customerDataMapper,
    )
    {
    }

    public function onCreate(GenericEvent $event): void
    {
        $customer = $event->getSubject();
        assert($customer instanceof CustomerInterface);
        $this->createOrUpdateContact($customer);
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

    private function createOrUpdateContact(CustomerInterface $customer): void
    {
        $customer_data = $this->customerDataMapper->getData($customer);
        $contact = $this->contactsApi->getContactByEmail($customer->getEmail());

        if (null === $contact) {
            $this->contactsApi->getApi()->create($customer_data);
            return;
        }

        $response = $this->contactsApi->getApi()->edit($contact['id'], $customer_data);
    }
}
