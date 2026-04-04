<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     * GET /api/contacts - Returns ALL contacts without filtering
     */
    public function index(Request $request)
    {
        // Get all contacts, no filtering, no pagination
        $contacts = Contact::orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $contacts,
            'message' => 'Contacts retrieved successfully'
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     * POST /api/contacts
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $validated['id'] = (string) Str::uuid();
        $validated['status'] = Contact::STATUS_NEW;

        $contact = Contact::create($validated);

        return response()->json([
            'success' => true,
            'data' => $contact,
            'message' => 'Contact message sent successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     * GET /api/contacts/{id}
     */
    public function show(string $id)
    {
        $contact = Contact::find($id);

        if (!$contact) {
            return response()->json([
                'success' => false,
                'message' => 'Contact not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $contact,
            'message' => 'Contact retrieved successfully'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     * PUT/PATCH /api/contacts/{id}
     */
    public function update(Request $request, string $id)
    {
        $contact = Contact::find($id);

        if (!$contact) {
            return response()->json([
                'success' => false,
                'message' => 'Contact not found'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'sometimes|string|max:255',
            'message' => 'sometimes|string',
            'status' => ['sometimes', Rule::in([Contact::STATUS_NEW, Contact::STATUS_READ, Contact::STATUS_RESOLVED])],
        ]);

        $contact->update($validated);

        return response()->json([
            'success' => true,
            'data' => $contact,
            'message' => 'Contact updated successfully'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /api/contacts/{id}
     */
    public function destroy(string $id)
    {
        $contact = Contact::find($id);

        if (!$contact) {
            return response()->json([
                'success' => false,
                'message' => 'Contact not found'
            ], 404);
        }

        $contact->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contact deleted successfully'
        ], 200);
    }
}