<?php
declare(strict_types=1);

namespace MySportsApp\Services;

class PaystackClient
{
    private string $baseUrl = 'https://api.paystack.co';
    private string $secret;

    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    private function request(string $method, string $path, array $params = []): array
    {
        $url = $this->baseUrl . $path;

        $ch = curl_init();
        $headers = [
            'Authorization: Bearer ' . $this->secret,
            'Content-Type: application/json',
            'Accept: application/json',
        ];

        if ($method === 'GET' && !empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_HTTPHEADER     => $headers,
        ]);

        if ($method !== 'GET' && !empty($params)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        }

        $response = curl_exec($ch);
        $code     = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response === false) {
            $err = curl_error($ch);
            curl_close($ch);
            throw new \RuntimeException("Paystack request failed: $err");
        }

        curl_close($ch);

        $decoded = json_decode($response, true);
        if (!is_array($decoded)) {
            throw new \RuntimeException("Paystack returned invalid JSON: $response");
        }

        if ($code >= 400 || ($decoded['status'] ?? true) !== true) {
            $message = $decoded['message'] ?? 'Unknown error';
            throw new \RuntimeException("Paystack API error ($code): $message");
        }

        return $decoded;
    }

    public function listSettlements(int $page = 1, int $perPage = 50): array
    {
        return $this->request('GET', '/settlement', [
            'page'    => $page,
            'perPage' => $perPage,
        ]);
    }

    public function settlementTransactions(int $settlementId, int $page = 1, int $perPage = 200): array
    {
        return $this->request('GET', "/settlement/{$settlementId}/transactions", [
            'page'    => $page,
            'perPage' => $perPage,
        ]);
    }
}
