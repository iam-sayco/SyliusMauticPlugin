<?php

declare(strict_types=1);

namespace Sayco\SyliusMauticPlugin\Context;

use Sayco\SyliusMauticPlugin\Http\Api\LeadDevicesApiInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class LeadContext implements LeadContextInterface
{
    public const SESSION_KEY = 'mautic_lead_id';

    public function __construct(
        private LeadDevicesApiInterface $leadDevicesApi,
        private CustomerContextInterface $customerContext,
        private RequestStack $requestStack,
        private SessionInterface $session
    ) {
    }

    public function getLeadId(): ?int
    {
        if (null === $this->customerContext->getCustomer()) {
            return null;
        }

        $session_lead_id = $this->getLeadIdFromSession();
        if (null !== $session_lead_id) {
            return $session_lead_id;
        }

        $tracking_id = $this->getTrackingId();
        if (null === $tracking_id) {
            return null;
        }

        $id = $this->leadDevicesApi->getLeadIdByTrackingId($tracking_id);
        if (null === $id) {
            return null;
        }

        $this->setLeadId($id);

        return $id;
    }

    private function getTrackingId(): ?string
    {
        return $this->requestStack
            ->getCurrentRequest()
            ->cookies
            ->getAlnum('mtc_sid') ?: null;
    }

    private function getLeadIdFromSession(): ?int
    {
        $id = $this->session->get(self::SESSION_KEY);
        if (null === $id) {
            return null;
        }

        return (int) $id;
    }

    public function setLeadId(int $lead_id): void
    {
        $this->session->set(self::SESSION_KEY, $lead_id);
    }
}
