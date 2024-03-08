<?php

declare(strict_types=1);

namespace IamSayco\SyliusMauticPlugin\EventListener;

use Mautic\Api\Contacts;
use Sayco\SyliusMauticPlugin\Http\MauticClient;
use Sylius\Component\Customer\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class CustomerEventsListener
{
    public function __construct(
        private MauticClient $mauticClient,
    )
    {
    }

    public function onCreate(GenericEvent $event): void
    {
        $contactsApi = $this->getContactApi();
        $contactsApi->getList($event->getSubject());
        $this->mauticClient->getApi('contacts')->create($event->getSubject());
    }

    public function onUpdate(GenericEvent $event): void
    {
        $customer = $event->getSubject();
        assert($customer instanceof CustomerInterface);

        $api = $this->getContactApi();
        $list = $api->getList($customer->getEmail(), limit: 1, minimal: true);

        $entry = [
            'firstname' => $customer->getFirstName(),
            'lastname' => $customer->getLastName(),
            'email' => $customer->getEmail(),
            'gender' => $customer->getGender(),
            'date_of_birth' => $customer->getBirthday()->format('c'),
            'phone' => $customer->getPhoneNumber(),
        ];

        if (empty($list['contacts'])) {
            $api->create($entry);
            return;
        }

        $contact = reset($list['contacts']);
        $api->edit($contact['id'], $entry);
    }

    public function onDelete(GenericEvent $event): void
    {
        $customer = $event->getSubject();
        assert($customer instanceof CustomerInterface);
        $api = $this->getContactApi();
        $list = $api->getList($customer->getEmail());

        if (false === empty($list['contacts'])) {
            $contact = reset($list['contacts']);
            $api->delete($contact['id']);
        }
    }

    private function getContactApi(): Contacts
    {
        return $this->mauticClient->getApi('contacts');
    }
}
