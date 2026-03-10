<?php

namespace AppccDigital\LaravelAppcc\Commands;

use AppccDigital\LaravelAppcc\AppccManager;
use Illuminate\Console\Command;

class TestConnectionCommand extends Command
{
    protected $signature = 'appcc:test-connection';

    protected $description = 'Test the connection to APPCC Digital';

    public function handle(AppccManager $manager): int
    {
        $url = config('appcc.url');

        if (empty($url) || empty(config('appcc.token'))) {
            $this->error('APPCC_URL and APPCC_TOKEN must be configured.');

            return self::FAILURE;
        }

        $this->info('Testing connection to APPCC Digital...');

        $result = $manager->client()->get('connection-test');

        if ($result->successful()) {
            $data = $result->toArray()['data'] ?? [];
            $this->info('✓ Connected to APPCC Digital');
            $this->line("  Tenant: ".($data['tenant_name'] ?? 'Unknown'));
            $this->line("  Plan: ".($data['plan'] ?? 'Unknown'));
            $this->line("  URL: {$url}");

            return self::SUCCESS;
        }

        $this->error('✗ Connection failed');
        $this->line('  Error: '.($result->message() ?? 'Unknown error'));
        $this->line('  Status: '.$result->status());

        return self::FAILURE;
    }
}
