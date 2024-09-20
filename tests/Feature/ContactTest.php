<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Database\Seeders\SearchContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ContactTest extends TestCase
{
  /**
   * A basic feature test example.
   */
  public function test_createSuccess()
  {
    $this->seed([UserSeeder::class]);
    $this->post('/api/contacts', [
      'first_name' => 'Muhammad',
      'last_name' => 'Isa Nuruddin',
      'email' => 'muhammadisa226@gmail',
      'phone' => '081234567890',
    ], [
      'Authorization' => 'test'
    ])->assertStatus(201)->assertJson([
      'data' => [
        'first_name' => 'Muhammad',
        'last_name' => 'Isa Nuruddin',
        'email' => 'muhammadisa226@gmail',
        'phone' => '081234567890',
      ]
    ]);
  }
  public function test_createFailed()
  {
    $this->seed([UserSeeder::class]);
    $this->post('/api/contacts', [
      'first_name' => '',
      'last_name' => 'Isa Nuruddin',
      'email' => 'muhammadisa226@gmail',
      'phone' => '081234567890',
    ], [
      'Authorization' => 'test'
    ])->assertStatus(400)->assertJson([
      'errors' => 'The first name field is required.',
    ]);
  }
  public function test_getSuccess()
  {
    $this->seed([UserSeeder::class, ContactSeeder::class]);
    $contact = Contact::query()->limit(1)->first();
    $this->get('/api/contacts/' . $contact->id, ['Authorization' => 'test'])->assertStatus(200)->assertJson([
      'data' => [
        'first_name' => 'test',
        'last_name' => 'test',
        'email' => 'test@gmail.com',
        'phone' => '111111111111',
      ]
    ]);
  }
  public function test_getNotfound()
  {
    $this->seed([UserSeeder::class, ContactSeeder::class]);
    $contact = Contact::query()->limit(1)->first();
    $this->get('/api/contacts/' . $contact->id + 1, ['Authorization' => 'test'])->assertStatus(404)->assertJson([
      'errors' => 'Contact Not Found'
    ]);
  }
  public function test_getOtherUserContact()
  {
    $this->seed([UserSeeder::class, ContactSeeder::class]);
    $contact = Contact::query()->limit(1)->first();
    $this->get('/api/contacts/' . $contact->id, ['Authorization' => 'test2'])->assertStatus(404)->assertJson([
      'errors' => 'Contact Not Found',
    ]);
  }
  public function test_UpdateSuccess()
  {
    $this->seed([UserSeeder::class, ContactSeeder::class]);
    $contact = Contact::query()->limit(1)->first();
    $this->put('/api/contacts/' . $contact->id, [
      'first_name' => 'test update',
      'last_name' => 'test update',
      'email' => 'testupdate@gmail.com',
      'phone' => 'testupdate',
    ], ['Authorization' => 'test'])->assertStatus(200)->assertJson([
      'data' => [
        'first_name' => 'test update',
        'last_name' => 'test update',
        'email' => 'testupdate@gmail.com',
        'phone' => 'testupdate',
      ],
    ]);
  }
  public function test_UpdateFailedValidation()
  {
    $this->seed([UserSeeder::class, ContactSeeder::class]);
    $contact = Contact::query()->limit(1)->first();
    $this->put('/api/contacts/' . $contact->id, [
      'first_name' => '',
      'last_name' => 'test update',
      'email' => 'testupdate@gmail.com',
      'phone' => 'testupdate',
    ], ['Authorization' => 'test'])->assertStatus(400)->assertJson([
      'errors' => 'The first name field is required.',
    ]);
  }
  public function test_deleteSuccess()
  {
    $this->seed([UserSeeder::class, ContactSeeder::class]);
    $contact = Contact::query()->limit(1)->first();
    $this->delete('/api/contacts/' . $contact->id, [], ['Authorization' => 'test'])->assertStatus(200)->assertJson([
      'data' => true,
    ]);
  }
  public function test_deleteNotFound()
  {
    $this->seed([UserSeeder::class, ContactSeeder::class]);
    $contact = Contact::query()->limit(1)->first();
    $this->delete('/api/contacts/' . $contact->id + 1, [], ['Authorization' => 'test'])->assertStatus(404)->assertJson([
      'errors' => 'Contact Not Found',
    ]);
  }
  public function test_SearchByFirstName()
  {
    $this->seed([UserSeeder::class, SearchContactSeeder::class]);
    $response  =  $this->get('/api/contacts?name=first', ['Authorization' => 'test'])->assertStatus(200)->json();
    Log::info(json_encode($response, JSON_PRETTY_PRINT));
    self::assertEquals(10, count($response['data']));
    self::assertEquals(20, $response['meta']['total']);
  }
  public function test_SearchByLastName()
  {
    $this->seed([UserSeeder::class, SearchContactSeeder::class]);
    $response  =  $this->get('/api/contacts?name=last', ['Authorization' => 'test'])->assertStatus(200)->json();
    Log::info(json_encode($response, JSON_PRETTY_PRINT));
    self::assertEquals(10, count($response['data']));
    self::assertEquals(20, $response['meta']['total']);
  }
  public function test_SearchByPhone()
  {
    $this->seed([UserSeeder::class, SearchContactSeeder::class]);
    $response  =  $this->get('/api/contacts?phone=11111', ['Authorization' => 'test'])->assertStatus(200)->json();
    Log::info(json_encode($response, JSON_PRETTY_PRINT));
    self::assertEquals(10, count($response['data']));
    self::assertEquals(20, $response['meta']['total']);
  }
  public function test_SearchNotFound()
  {
    $this->seed([UserSeeder::class, SearchContactSeeder::class]);
    $response  =  $this->get('/api/contacts?phone=tidakada', ['Authorization' => 'test'])->assertStatus(200)->json();
    Log::info(json_encode($response, JSON_PRETTY_PRINT));
    self::assertEquals(0, count($response['data']));
    self::assertEquals(0, $response['meta']['total']);
  }
  public function test_SearchWithPage()
  {
    $this->seed([UserSeeder::class, SearchContactSeeder::class]);
    $response  =  $this->get('/api/contacts?size=5&page=2', ['Authorization' => 'test'])->assertStatus(200)->json();
    Log::info(json_encode($response, JSON_PRETTY_PRINT));
    self::assertEquals(5, count($response['data']));
    self::assertEquals(2, $response['meta']['current_page']);
    self::assertEquals(20, $response['meta']['total']);
  }
}
