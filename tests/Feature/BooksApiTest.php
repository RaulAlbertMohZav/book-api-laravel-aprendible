<?php

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BooksApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_get_all_books(): void
    {
        $books = Book::factory()->count(4)->create();

        $url = route('books.index');

        $response = $this->getJson($url)->assertOk();

        $response->assertJsonFragment([
            'title' => $books[0]->title
        ]);
        $response->assertJsonFragment([
            'title' => $books[1]->title
        ]);
    }

    /** @test */
    public function can_get_one_book(): void
    {
        $book = Book::factory()->create();

        $url = route('books.show', $book);

        $this->getJson($url)
            ->assertOk()
            ->assertJsonFragment([
                'title' => $book->title
            ]);
    }

    /** @test */
    public function can_create_books(): void
    {
        $bookTitle = 'My new book';

        $url = route('books.store');

        $this->postJson($url, [])
            ->assertJsonValidationErrorFor('title');

        $this->postJson($url, [
            'title' => $bookTitle
        ])->assertCreated()
            ->assertJsonFragment([
                'title' => $bookTitle
            ]);
        $this->assertDatabaseHas('books', [
            'title' => $bookTitle
        ]);
    }

    /** @test */
    public function can_update_books(): void
    {
        $book = Book::factory()->create();

        $url = route('books.update', $book);

        $this->patchJson($url, [])->assertJsonValidationErrorFor('title');

        $this->patchJson($url, [
            'title' => 'Edited Book'
        ])->assertOk()
            ->assertJsonFragment([
                'title' => 'Edited Book'
            ]);

        $this->assertDatabaseHas('books', [
            'title' => 'Edited Book'
        ]);
    }

    /** @test */
    public function can_delete_books(): void
    {
        $book = Book::factory()->create();

        $url = route('books.destroy', $book);

        $this->deleteJson($url)->assertNoContent();

        $this->assertDatabaseMissing('books', [
            'title' => $book->title
        ]);
        $this->assertDatabaseCount('books', 0);
    }
}
