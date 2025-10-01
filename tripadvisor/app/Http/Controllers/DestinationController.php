<?php

namespace App\Http\Controllers;

use App\Http\Resources\DestinationResource;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DestinationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $sortBy = $request->query('sort_by', 'name');
        $sortDir = strtolower($request->query('sort_direction', 'asc')) === 'desc' ? 'desc' : 'asc';

        $allowedSort = ['name', 'country', 'region', 'created_at', 'places_count'];

        if (! in_array($sortBy, $allowedSort, true)) {
            return response()->json([
                'message' => "Invalid sort_by. Allowed: " . implode(',', $allowedSort),
            ], 422);
        }

        $query = Destination::query();

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('country', 'like', "%{$q}%")
                    ->orWhere('region', 'like', "%{$q}%");
            });
        }

        if ($sortBy === 'places_count') {
            $query->withCount('places')->orderBy('places_count', $sortDir);
        } else {
            $query->orderBy($sortBy, $sortDir);
        }

        $destinations = $query->get();

        if ($destinations->isEmpty()) {
            return response()->json(['message' => 'No destinations found!'], 404);            return response()->json(['message' => 'No destinations found!'], 404);
        }

        return response()->json([
            'count' => $destinations->count(),
            'destinations' => DestinationResource::collection($destinations),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'region' => ['sometimes', 'nullable', 'string', 'max:255'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', 'unique:destinations,slug'],
            'description' => ['sometimes', 'nullable', 'string'],
        ]);

        $slug = $validated['slug'] ?? Str::slug(($validated['name'] ?? '') . '-' . ($validated['country'] ?? ''));

        $base = $slug;
        $i = 1;
        while (Destination::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        $validated['slug'] = $slug;

        $destination = Destination::create($validated);

        return response()->json([
            'message' => 'Destination created successfully',
            'destination' => new DestinationResource($destination),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Destination $destination)
    {
        $destination->loadCount('places');

        return response()->json([
            'destination' => new DestinationResource($destination),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Destination $destination)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Destination $destination)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'country' => ['sometimes', 'string', 'max:255'],
            'region' => ['sometimes', 'nullable', 'string', 'max:255'],
            'slug' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
                Rule::unique('destinations', 'slug')->ignore($destination->id)
            ],
            'description' => ['sometimes', 'nullable', 'string'],
        ]);

        if (!array_key_exists('slug', $validated) && (array_key_exists('name', $validated) || array_key_exists('country', $validated))) {
            $newName = $validated['name'] ?? $destination->name;
            $newCountry = $validated['country'] ?? $destination->country;
            $slug = Str::slug($newName . '-' . $newCountry);

            if ($slug !== $destination->slug) {
                $base = $slug;
                $i = 1;
                while (Destination::where('slug', $slug)->where('id', '!=', $destination->id)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $validated['slug'] = $slug;
            }
        }

        if (empty($validated)) {
            return response()->json([
                'message' => 'Nothing to update',
                'destination' => new DestinationResource($destination),
            ]);
        }

        $destination->update($validated);

        return response()->json([
            'message' => 'Destination updated successfully',
            'destination' => new DestinationResource($destination),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Destination $destination)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $destination->delete();
        return response()->json(['message' => 'Destination deleted successfully']);
    }
}