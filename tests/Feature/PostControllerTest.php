<?php

namespace Tests\Feature;
use App\Models\Post;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostControllerTest extends TestCase
{   
    use RefreshDatabase;
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
    
    public function test_it_can_get_all_posts()
    {
        Post::factory()->count(3)->create();
        $posts = Post::all();
        $this->assertCount(3, $posts);
    }

  
    public function test_it_can_create_a_new_post()
    {    
        $data = [
            'title' => 'Test Title',
            'content' => 'Test Content',
        ];   
        $response = $this->postJson('/api/posts', $data);    
        $response->assertStatus(201);
        $response->assertJsonFragment($data);
        $this->assertDatabaseHas('posts', $data);
    }

 
    public function test_it_returns_validation_errors_when_creating_a_post_with_missing_fields()
    {    
        $response = $this->postJson('/api/posts', []);        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title', 'content']);
    }

  
    public function test_it_can_get_a_single_post()
    {  
        $post = Post::factory()->create();       
        $response = $this->getJson("/api/posts/{$post->id}");
        $response->assertStatus(200);
        $response->assertJson([
            'id' => $post->id,
            'title' => $post->title,
            'content' => $post->content,
        ]);
    }

   
    public function test_it_returns_404_for_a_nonexistent_post()
    {       
        $response = $this->getJson('/api/posts/999');       
        $response->assertStatus(404);
        $response->assertJson(['message' => 'Post not found']);
    }

   
    public function test_it_can_update_a_post()
    {       
        $post = Post::factory()->create();
        $data = [
            'title' => 'Updated Title',
            'content' => 'Updated Content',
        ];      
        $response = $this->putJson("/api/posts/{$post->id}", $data);      
        $response->assertStatus(200);
        $response->assertJsonFragment($data);
        $this->assertDatabaseHas('posts', $data);
    }

  
    public function test_it_can_delete_a_post()
    {       
        $post = Post::factory()->create();       
        $response = $this->deleteJson("/api/posts/{$post->id}");       
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Post deleted successfully']);
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }
}
