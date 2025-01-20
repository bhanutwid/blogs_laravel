<?php
namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class PostController extends Controller
{
    // Get all posts
    public function index()
    {
        $cachedPosts = Redis::get('posts');
        if ($cachedPosts) {
            print("Fetching all posts from Redis cache...\n");
            $posts = json_decode($cachedPosts);
        } else {
            print("Fetching all posts from database and caching...\n");
            $posts = Post::all();
            Redis::set('posts', json_encode($posts));
            Redis::expire('posts', 60); // Set expiration to 60 seconds
        }

        return response()->json($posts);
    }

    // Create a new post
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
        ]);

        $post = Post::create([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        print("New post created: {$post->id}\n");
        print("Clearing posts cache...\n");
        Redis::del('posts'); // Clear cached posts since data has changed

        return response()->json($post, 201);
    }

    // Get a single post by id
    public function show($id)
    {
        $cachedPost = Redis::get("post_{$id}");

        if ($cachedPost) {
            print("Fetching post {$id} from Redis cache...\n");
            $post = json_decode($cachedPost);
        } else {
            print("Fetching post {$id} from database and caching...\n");
            $post = Post::find($id);

            if ($post) {
                Redis::set("post_{$id}", json_encode($post));
                Redis::expire("post_{$id}", 60); // Cache for 60 seconds
            } else {
                print("Post {$id} not found in database.\n");
                return response()->json(['message' => 'Post not found'], 404);
            }
        }

        return response()->json($post);
    }

    // Update a post
    public function update(Request $request, $id)
    {
        $post = Post::find($id);

        if ($post) {
            $post->update([
                'title' => $request->title,
                'content' => $request->content,
            ]);

            print("Post {$id} updated.\n");
            print("Updating Redis cache for post {$id} and clearing posts cache...\n");
            Redis::set("post_{$id}", json_encode($post));
            Redis::expire("post_{$id}", 60);
            Redis::del('posts'); // Clear cached posts to force re-fetch

            return response()->json($post);
        }

        print("Post {$id} not found for update.\n");
        return response()->json(['message' => 'Post not found'], 404);
    }

    // Delete a post
    public function destroy($id)
    {
        $post = Post::find($id);

        if ($post) {
            $post->delete();
            print("Post {$id} deleted.\n");
            print("Removing Redis cache for post {$id} and clearing posts cache...\n");
            Redis::del("post_{$id}"); // Remove the individual post from Redis
            Redis::del('posts'); // Clear cached posts

            return response()->json(['message' => 'Post deleted successfully']);
        }

        print("Post {$id} not found for deletion.\n");
        return response()->json(['message' => 'Post not found'], 404);
    }
}
