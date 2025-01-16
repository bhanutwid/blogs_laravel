<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // Get all posts
    public function index()
    {
        return response()->json(Post::all());
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

        return response()->json($post, 201);
    }

    // Get a single post by id
    public function show($id)
    {
        $post = Post::find($id);

        if ($post) {
            return response()->json($post);
        }

        return response()->json(['message' => 'Post not found'], 404);
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

            return response()->json($post);
        }

        return response()->json(['message' => 'Post not found'], 404);
    }

    // Delete a post
    public function destroy($id)
    {
        $post = Post::find($id);

        if ($post) {
            $post->delete();
            return response()->json(['message' => 'Post deleted successfully']);
        }

        return response()->json(['message' => 'Post not found'], 404);
    }
}
