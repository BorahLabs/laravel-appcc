<?php

namespace AppccDigital\LaravelAppcc\Testing;

class AppccFakeResponse
{
    public function __construct(
        public readonly array $data,
        public readonly int $status = 200,
    ) {}
}
