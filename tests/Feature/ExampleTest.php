<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ExampleTest extends TestCase
{

    use DatabaseMigrations;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_book_api()
    {  
        $response = $this->json('get', 'api/book');

        $response->assertStatus(200);
    }

    public function test_put_book_api()
    {  
        $response = $this->json('put', 'api/book');

        $response->assertStatus(405);
    }

    public function test_author_api()
    {  
        $response = $this->json('get', 'api/author');

        $response->assertStatus(200);
    }

    public function test_title_api()
    {  
        $response = $this->json('get', 'api/title');

        $response->assertStatus(200);
    }

    public function test_delete_api()
    {
        $response = $this->json('delete', 'api/book');

        $response->assertStatus(405);
    }

    public function test_create_a_book()
    {
        $book = factory('App\Book')->make()->toArray();

        $this->post('api/book', $book)->assertStatus(201)->assertJson($book);

        $this->assertDatabaseHas('books', $book);
    }

}
