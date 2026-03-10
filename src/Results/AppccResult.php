<?php

namespace AppccDigital\LaravelAppcc\Results;

use Illuminate\Http\Client\Response;

/**
 * @template T
 */
class AppccResult
{
    private readonly Response $response;

    private mixed $typedData = null;

    /** @var class-string<T>|null */
    private ?string $dtoClass = null;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @param class-string<T> $dtoClass
     *
     * @return static
     */
    public function withDto(string $dtoClass): static
    {
        $this->dtoClass = $dtoClass;

        return $this;
    }

    public function successful(): bool
    {
        return $this->response->successful();
    }

    public function failed(): bool
    {
        return $this->response->failed();
    }

    public function status(): int
    {
        return $this->response->status();
    }

    /**
     * @return T|null
     */
    public function data(): mixed
    {
        if ($this->failed()) {
            return null;
        }

        if ($this->typedData !== null) {
            return $this->typedData;
        }

        $responseData = $this->response->json('data', []);

        if ($this->dtoClass !== null && method_exists($this->dtoClass, 'fromArray')) {
            $this->typedData = $this->dtoClass::fromArray($responseData);
        } else {
            $this->typedData = $responseData;
        }

        return $this->typedData;
    }

    /**
     * @return array<string, array<string>>
     */
    public function errors(): array
    {
        if ($this->status() === 422) {
            return $this->response->json('errors', []);
        }

        return [];
    }

    public function message(): ?string
    {
        return $this->response->json('message');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->response->json() ?? [];
    }
}
