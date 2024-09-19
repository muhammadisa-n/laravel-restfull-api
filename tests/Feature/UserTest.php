<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotEquals;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;

class UserTest extends TestCase
{
  /**
   * A basic feature test example.
   */
  public function test_registerSuccess()
  {
    $this->post('/api/users', [
      "username" => 'muhammadisa226',
      "password" => 'rahasia',
      "name" => 'Muhammad Isa'
    ])->assertStatus(201)->assertJson([
      "data" => [
        'username' => 'muhammadisa226',
        'name' => 'Muhammad Isa'
      ]
    ]);
  }
  public function test_registerFailed()
  {
    $this->post('/api/users', [
      "username" => '',
      "password" => '',
      "name" => ''
    ])->assertStatus(400)->assertJson([
      "errors" => 'The username field is required.'
    ]);
  }
  public function test_registerUsernameAlreadyExits()
  {
    $this->test_registerSuccess();
    $this->post('/api/users', [
      "username" => 'muhammadisa226',
      "password" => 'rahasia',
      "name" => 'Muhammad Isa'
    ])->assertStatus(409)->assertJson([
      "errors" => 'Username Already Registered'
    ]);
  }
  public function test_loginSuccess()
  {
    $this->seed([UserSeeder::class]);
    $this->post('/api/users/login', [
      "username" => 'test',
      "password" => 'test',
    ])->assertStatus(200)->assertJson([
      "data" => [
        'username' => 'test',
        'name' => 'test'
      ]
    ]);

    $user = User::where('username', 'test')->first();
    assertNotNull($user->token);
  }


  public function test_loginFailedUsernameNotfound()
  {
    $this->seed([UserSeeder::class]);
    $this->post('/api/users/login', [
      "username" => 'tests',
      "password" => 'test',
    ])->assertStatus(401)->assertJson([
      "errors" => 'Username Or Password Is Wrong'
    ]);
    // $user = User::where('username', 'test')->first();
    // assertNull($user->token);
  }
  public function test_loginFailedPasswordWrong()
  {
    $this->seed([UserSeeder::class]);
    $this->post('/api/users/login', [
      "username" => 'test',
      "password" => 'tes',
    ])->assertStatus(401)->assertJson([
      "errors" => 'Username Or Password Is Wrong'
    ]);
  }

  public function test_getSuccess()
  {
    $this->seed([UserSeeder::class]);
    $this->get('/api/users/current', ['Authorization' => 'test'])->assertStatus(200)->assertJson(['data' => [
      "username" => 'test',
      "name" => 'test'
    ]]);
  }
  public function test_getUnathorized()
  {
    $this->seed([UserSeeder::class]);
    $this->get('/api/users/current')->assertStatus(401)->assertJson([
      'errors' => 'Unauthorized'
    ]);
  }
  public function test_getInvalidToken()
  {
    $this->seed([UserSeeder::class]);
    $this->get('/api/users/current', ['Authorization' => 'testing'])->assertStatus(401)->assertJson([
      'errors' => 'Unauthorized'
    ]);
  }
  public function test_UpdateName()
  {
    $this->seed([UserSeeder::class]);
    $this->patch('/api/users/current', [
      "name" => 'test update'
    ], ['Authorization' => 'test'])->assertStatus(200)->assertJson(['data' => [
      "username" => 'test',
      "name" => 'test update'
    ]]);
  }
  public function test_UpdatePassword()
  {
    $this->seed([UserSeeder::class]);
    $this->patch('/api/users/current', [
      "password" => 'test update'
    ], ['Authorization' => 'test'])->assertStatus(200)->assertJson(['data' => [
      "username" => 'test',
      "name" => 'test'
    ]]);
  }
  public function test_logout()
  {
    $this->seed([UserSeeder::class]);
    $this->delete(uri: '/api/users/logout', headers: ['Authorization' => 'test'])->assertStatus(200)->assertJson(['data' => true]);
  }
}
