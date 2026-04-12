<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article; 
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       $articles = Article::orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $articles,
            'message' => 'Articles retrieved successfully'
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string', 
            'topic' => 'required|string|max:255',
            'slug' => 'required|unique:articles',
            'featured_image' => 'nullable|url',
            'is_published' => 'boolean',

        ]);

        $validated['id'] = Str::uuid();
        $validated['published_at'] = $request->is_published ? now() : null;
        $article = Article::create($validated);

        return response()->json([
            'success' => true,
            'data' => $article,
            'message' => 'Article created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $article,
            'message' => 'Article retrieved successfully'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found'
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'topic' => 'required|string|max:255',
            'slug' => 'required|unique:articles,slug,' . $article->id,
            'featured_image' => 'nullable|url',
            'is_published' => 'boolean',
        ]);

        $validated['published_at'] = $request->is_published ? now() : null;
        $article->update($validated);

        return response()->json([
            'success' => true,
            'data' => $article,
            'message' => 'Article updated successfully'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found'
            ], 404);
        }

        $article->delete();

        return response()->json([
            'success' => true,
            'message' => 'Article deleted successfully'
        ], 200);
    }
}
