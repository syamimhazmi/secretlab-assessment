<?php

namespace Database\Seeders;

use App\Models\KeyValueStore;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class KeyValueStoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        KeyValueStore::query()->truncate();

        $now = time();

        KeyValueStore::query()
            ->create([
                'key' => 'user_config',
                'value' => json_encode(['theme' => 'light', 'notifications' => true]),
                'stored_at' => Carbon::createFromTimestamp($now - 3600), // 1 hour ago
            ]);

        KeyValueStore::query()
            ->create([
                'key' => 'user_config',
                'value' => json_encode(['theme' => 'dark', 'notifications' => true]),
                'stored_at' => Carbon::createFromTimestamp($now - 1800), // 30 minutes ago
            ]);

        KeyValueStore::query()
            ->create([
                'key' => 'user_config',
                'value' => json_encode(['theme' => 'dark', 'notifications' => false]),
                'stored_at' => Carbon::createFromTimestamp($now), // now
            ]);

        KeyValueStore::query()
            ->create([
                'key' => 'app_settings',
                'value' => json_encode(['version' => '1.0.0', 'debug' => true]),
                'stored_at' => Carbon::createFromTimestamp($now - 7200), // 2 hours ago
            ]);

        KeyValueStore::query()
            ->create([
                'key' => 'app_settings',
                'value' => json_encode(['version' => '1.1.0', 'debug' => false]),
                'stored_at' => Carbon::createFromTimestamp($now - 900), // 15 minutes ago
            ]);

        KeyValueStore::query()
            ->create([
                'key' => 'welcome_message',
                'value' => 'Hello World!',
                'stored_at' => Carbon::createFromTimestamp($now - 600), // 10 minutes ago
            ]);

        KeyValueStore::query()
            ->create([
                'key' => 'welcome_message',
                'value' => 'Welcome to our API!',
                'stored_at' => Carbon::createFromTimestamp($now), // now
            ]);

        KeyValueStore::query()
            ->create([
                'key' => 'api_version',
                'value' => 1.2,
                'stored_at' => Carbon::createFromTimestamp($now - 300), // 5 minutes ago
            ]);

        echo "KeyValueStore seeded with sample data!\n";
    }
}
