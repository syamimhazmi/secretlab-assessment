<?php

namespace Tests\Feature;

use App\Models\KeyValueStore;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class KeyValueStoreTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_can_store_key_value_pair_with_redirect_format(): void
    {
        $response = $this->postJson('/api/v1/object', [
            'mykey' => 'value1'
        ]);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => ['key', 'value', 'stored_at', 'stored_at_utc']
                ]
            ]);

        $this->assertDatabaseHas('key_value_stores', [
            'key' => 'mykey',
            'value' => json_encode('value1')
        ]);
    }

    public function test_can_retrieve_latest_value(): void
    {
        KeyValueStore::storeKeyValue('mykey', 'value1', 1640000000);

        KeyValueStore::storeKeyValue('mykey', 'value2', 1640000300);

        $response = $this->getJson('/api/v1/object/mykey');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'value' => 'value2',
            ]);
    }

    public function test_can_retrieve_value_at_timestamp(): void
    {
        KeyValueStore::storeKeyValue('mykey', 'value1', 1640000000);

        KeyValueStore::storeKeyValue('mykey', 'value2', 1640000300);

        $response = $this->getJson('/api/v1/object/mykey?timestamp=1640000200');

        $response->assertStatus(200)
            ->assertJson([
                'value' => 'value1',
            ]);
    }
}
