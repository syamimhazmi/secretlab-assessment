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
                    '*' => ['key', 'value', 'stored_at']
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

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'value' => 'value1',
            ]);
    }

    public function test_can_get_all_records(): void
    {
        KeyValueStore::storeKeyValue('mykey', 'value1', 1640000000);

        KeyValueStore::storeKeyValue('mykey', 'value2', 1640000100);

        $response = $this->getJson('/api/v1/object/get_all_records');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(2)
            ->assertJsonStructure([
                '*' => ['key', 'value', 'stored_at']
            ]);
    }

    public function test_returns_404_for_nonexistent_key(): void
    {
        $response = $this->getJson('/api/v1/object/nonexistent');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_handles_invalid_timestamp(): void
    {
        KeyValueStore::storeKeyValue('mykey', 'value1', 1640000000);

        $response = $this->getJson('/api/v1/object/mykey?timestamp=-1');

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_returns_400_for_empty_request_body(): void
    {
        $response = $this->postJson('/api/v1/object', []);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_can_store_json_objects(): void
    {
        $data = [
            'user' => [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'age' => 30
            ]
        ];

        $response = $this->postJson('/api/v1/object', $data);

        $response->assertStatus(Response::HTTP_CREATED);

        $retrieveResponse = $this->getJson('/api/v1/object/user');

        $retrieveResponse->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment([
                'key' => 'user',
                'value' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'age' => 30
                ]
            ]);
    }

    public function test_versioning_works_correctly(): void
    {
        $timestamp1 = time();
        $timestamp2 = $timestamp1 + 300; // add 5 more minutes

        KeyValueStore::storeKeyValue('versioned_key', 'version1', $timestamp1);

        KeyValueStore::storeKeyValue('versioned_key', 'version2', $timestamp2);

        $response = $this->getJson('/api/v1/object/versioned_key');
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'value' => 'version2'
            ]);

        $response = $this->getJson("/api/v1/object/versioned_key?timestamp=$timestamp1");
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(['value' => 'version1']);

        $betweenTimestamp = $timestamp1 + 100;
        $response = $this->getJson("/api/v1/object/versioned_key?timestamp=$betweenTimestamp");
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(['value' => 'version1']);
    }
}
