<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Contact;
use Database\Seeders\AddressSeeder;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddressTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_createSuccess(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $this->post('/api/contacts/' . $contact->id . '/addresses', [
            'street' =>  'test',
            'city' =>  'test',
            'province' => 'test',
            'country' =>  'test',
            'postal_code' =>  '213123',
        ], ['Authorization' => 'test'])->assertStatus(201)->assertJson([
            "data" => [
                'street' =>  'test',
                'city' =>  'test',
                'province' => 'test',
                'country' =>  'test',
                'postal_code' =>  '213123',
            ],
        ]);
    }
    public function test_createFailed(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $this->post('/api/contacts/' . $contact->id . '/addresses', [
            'street' =>  'test',
            'city' =>  'test',
            'province' => 'test',
            'country' =>  '',
            'postal_code' =>  '213123',
        ], ['Authorization' => 'test'])->assertStatus(400)->assertJson([
            'errors' => 'The country field is required.',
        ]);
    }
    public function test_createContactNotFound(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $this->post('/api/contacts/' . $contact->id + 1 . '/addresses', [
            'street' =>  'test',
            'city' =>  'test',
            'province' => 'test',
            'country' =>  'test',
            'postal_code' =>  '213123',
        ], ['Authorization' => 'test'])->assertStatus(404)->assertJson([
            'errors' => 'Contact Not Found',
        ]);
    }
    public function test_getSuccess(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);

        $address = Address::query()->limit(1)->first();
        $this->get('/api/contacts/' . $address->contact_id  . '/addresses/' . $address->id, ['Authorization' => 'test'])->assertStatus(200)->assertJson([
            "data" => [
                'street' =>  'test',
                'city' =>  'test',
                'province' => 'test',
                'country' =>  'test',
                'postal_code' =>  '111111',
            ],
        ]);
    }
    public function test_getNotfound(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);

        $address = Address::query()->limit(1)->first();
        $this->get('/api/contacts/' . $address->contact_id  . '/addresses/' . $address->id + 1, ['Authorization' => 'test'])->assertStatus(404)->assertJson([
            "errors" => 'Address Not Found'
        ]);
    }
    public function test_updateSuccess(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();
        $this->put('/api/contacts/' . $address->contact_id  . '/addresses/' . $address->id, [
            'street' =>  'update',
            'city' =>  'update',
            'province' => 'update',
            'country' =>  'update',
            'postal_code' =>  '222222',
        ], ['Authorization' => 'test'])->assertStatus(200)->assertJson([
            "data" => [
                'street' =>  'update',
                'city' =>  'update',
                'province' => 'update',
                'country' =>  'update',
                'postal_code' =>  '222222',
            ],
        ]);
    }
    public function test_updateFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();
        $this->put('/api/contacts/' . $address->contact_id  . '/addresses/' . $address->id, [
            'street' =>  'update',
            'city' =>  'update',
            'province' => 'update',
            'country' =>  '',
            'postal_code' =>  '222222',
        ], ['Authorization' => 'test'])->assertStatus(400)->assertJson([
            'errors' => 'The country field is required.',
        ]);
    }
    public function test_updateNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();
        $this->put('/api/contacts/' . $address->contact_id  . '/addresses/' . $address->id + 1, [
            'street' =>  'update',
            'city' =>  'update',
            'province' => 'update',
            'country' =>  'update',
            'postal_code' =>  '222222',
        ], ['Authorization' => 'test'])->assertStatus(404)->assertJson([
            'errors' => 'Address Not Found',
        ]);
    }
    public function test_deleteSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();
        $this->delete('/api/contacts/' . $address->contact_id  . '/addresses/' . $address->id, [], ['Authorization' => 'test'])->assertStatus(200)->assertJson([
            'data' => true,
        ]);
    }
    public function test_deleteNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();
        $this->delete('/api/contacts/' . $address->contact_id  . '/addresses/' . $address->id + 1, [], ['Authorization' => 'test'])->assertStatus(404)->assertJson([
            'errors' => "Address Not Found",
        ]);
    }
    public function test_listSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $this->get('/api/contacts/' . $contact->id  . '/addresses', ['Authorization' => 'test'])->assertStatus(200)->assertJson([
            "data" => [
                [
                    'street' =>  'test',
                    'city' =>  'test',
                    'province' => 'test',
                    'country' =>  'test',
                    'postal_code' =>  '111111',
                ]

            ],
        ]);
    }
    public function test_listContactNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $this->get('/api/contacts/' . $contact->id + 1 . '/addresses', ['Authorization' => 'test'])->assertStatus(404)->assertJson([
            "errors" => "Contact Not Found",
        ]);
    }
}
