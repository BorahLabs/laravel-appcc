<?php

namespace AppccDigital\LaravelAppcc\Http;

use AppccDigital\LaravelAppcc\Results\AppccResult;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class AppccClient
{
    public function __construct(
        private readonly ?string $url,
        private readonly ?string $token,
        private readonly int $timeout = 30,
    ) {}

    public function post(string $endpoint, array $data, mixed $photo = null): AppccResult
    {
        $response = $this->sendRequest('post', $endpoint, $data, $photo);

        return new AppccResult($response);
    }

    public function patch(string $endpoint, array $data): AppccResult
    {
        $response = $this->sendRequest('patch', $endpoint, $data);

        return new AppccResult($response);
    }

    public function get(string $endpoint): AppccResult
    {
        $response = $this->makeRequest()->get($this->buildUrl($endpoint));

        return new AppccResult($response);
    }

    private function sendRequest(string $method, string $endpoint, array $data, mixed $photo = null): Response
    {
        $request = $this->makeRequest();
        $url = $this->buildUrl($endpoint);

        if ($photo !== null) {
            return $this->sendMultipartRequest($request, $url, $data, $photo);
        }

        return $request->$method($url, $data);
    }

    private function sendMultipartRequest(PendingRequest $request, string $url, array $data, mixed $photo): Response
    {
        $request = $request->asMultipart();

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $index => $item) {
                    if (is_array($item)) {
                        foreach ($item as $subKey => $subValue) {
                            $request = $request->attach("{$key}[{$index}][{$subKey}]", (string) $subValue);
                        }
                    } else {
                        $request = $request->attach("{$key}[{$index}]", (string) $item);
                    }
                }
            } elseif ($value !== null) {
                $request = $request->attach($key, (string) $value);
            }
        }

        if (is_string($photo) && file_exists($photo)) {
            $request = $request->attach('photo', fopen($photo, 'r'), basename($photo));
        } elseif (is_object($photo) && method_exists($photo, 'getPathname')) {
            $request = $request->attach('photo', fopen($photo->getPathname(), 'r'), $photo->getClientOriginalName());
        } elseif (is_resource($photo)) {
            $request = $request->attach('photo', $photo, 'photo.jpg');
        }

        return $request->post($url);
    }

    private function makeRequest(): PendingRequest
    {
        return Http::baseUrl($this->buildBaseUrl())
            ->withToken($this->token)
            ->timeout($this->timeout)
            ->acceptJson();
    }

    private function buildBaseUrl(): string
    {
        return rtrim($this->url ?? '', '/').'/api/v1';
    }

    private function buildUrl(string $endpoint): string
    {
        return ltrim($endpoint, '/');
    }
}
