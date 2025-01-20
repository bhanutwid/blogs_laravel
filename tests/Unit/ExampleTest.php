<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;


class ExampleTest extends TestCase
{
    use RefreshDatabase;
    public function test_that_true_is_true(): void
    {
        $this->assertTrue(true);
    }
   
    public function test_it_can_retrieve_all_posts_from_the_database()
    {
        Post::factory()->count(5)->create();
        $posts = Post::all();
        $this->assertCount(5, $posts);
    }


    public function test_it_can_find_a_post_by_id()
    {
        $post = Post::factory()->create();
        $foundPost = Post::find($post->id);
        $this->assertNotNull($foundPost);
        $this->assertEquals($post->id, $foundPost->id);
    }

    public function test_it_returns_validation_errors_when_creating_a_post_with_missing_fields()
    {    
        $response = $this->postJson('/api/posts', []);        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title', 'content']);
    }

    public function test_it_can_update_a_post()
    {
        $post = Post::factory()->create();
        $post->update([
            'title' => 'Updated Title',
            'content' => 'Updated Content',
        ]);
        $this->assertEquals('Updated Title', $post->title);
        $this->assertEquals('Updated Content', $post->content);
    }

}
