<?php


namespace App\Console\Commands;

use App\Models\ApiKey;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateApiKey extends Command
{
    protected $signature = 'api:key:generate {name} {--expires=} {--description=}';
    protected $description = 'Generate a new API key';

    public function handle()
    {
        $key = ApiKey::create([
            'key' => Str::random(64),
            'name' => $this->argument('name'),
            'description' => $this->option('description'),
            'expires_at' => $this->option('expires') ? now()->addDays((int) $this->option('expires')) : null
        ]);

        $this->info("API key generated successfully!");
        $this->table(
            ['Name', 'Key', 'Expires'],
            [[
                $key->name,
                $key->key,
                $key->expires_at ? $key->expires_at->format('Y-m-d') : 'Never'
            ]]
        );
    }
}