<?php
declare(strict_types=1);

namespace MySportsApp\Services;

/**
 * Stub service for Xero OAuth + API.
 * Fill in with your tenant-specific logic.
 */
class XeroService
{
    public function getAuthorizationUrl(): string
    {
        // TODO: Build OAuth URL using client_id, redirect_uri, scopes
        return '#';
    }

    public function handleCallback(array $query): void
    {
        // TODO: Exchange code for tokens, store in xero_connections
    }

    public function createSettlementInvoice(array $settlement, array $lines): void
    {
        // TODO: Use stored tokens to create invoice in Xero
    }
}
