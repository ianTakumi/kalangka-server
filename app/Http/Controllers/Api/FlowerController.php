<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use App\Models\Flower;
use Illuminate\Http\Request;

class FlowerController extends Controller
{
    
    // Store flower (from React Native)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|string|uuid',
            'tree_id' => 'required|string|exists:trees,id',
            'quantity' => 'required|integer|min:1',
            'wrapped_at' => 'nullable|date',
            'image_url' => 'required|url',
        ]);
        
        $flower = Flower::create($validated);
        
        return response()->json([
            'message' => 'Flower created successfully',
            'data' => $flower
        ], 201);
    }
    
    public function show(string $id)
    {
        $flower = Flower::find($id);
        
        if (!$flower) {
            return response()->json([
                'message' => 'Flower not found'
            ], 404);
        }
        
        return response()->json($flower);
    }

    // Delete flower PERMANENTLY
    public function destroy(string $id)
    {
        $flower = Flower::findOrFail($id);
        $flower->delete(); // Permanent delete
        
        return response()->json([
            'message' => 'Flower permanently deleted'
        ]);
    }
    
    // Update flower
    public function update(Request $request, string $id)
    {
        $flower = Flower::findOrFail($id);
        
        $validated = $request->validate([
            'quantity' => 'sometimes|integer|min:1',
            'wrapped_at' => 'sometimes|nullable|date',
            'image_url' => 'sometimes|url',
        ]);
        
        $flower->update($validated);
        
        return response()->json($flower);
    }
}