<?php

namespace App\Http\Controllers;

use App\Http\Resources\PlaceResource;
use App\Models\Destination;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class PlaceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'type' => ['sometimes', Rule::in(Place::TYPES)],
            'price_level' => ['sometimes', 'nullable', 'integer', 'between:0,4'],
            'destination_id' => ['sometimes', 'integer', 'exists:destinations,id'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ]);

        $perPage = (int)($validated['per_page'] ?? 15);

        $query = Place::query()
            ->with(['destination'])
            ->withCount('reviews');

        if (array_key_exists('type', $validated)) {
            $query->where('type', $validated['type']);
        }

        if (array_key_exists('price_level', $validated)) {

            $pl = $validated['price_level'];
            if ($pl === null) {
                $query->whereNull('price_level');
            } else {
                $query->where('price_level', $pl);
            }
        }

        if (array_key_exists('destination_id', $validated)) {
            $query->where('destination_id', $validated['destination_id']);
        }

        $query->orderBy('created_at', 'desc');

        $places = $query->paginate($perPage)->appends($request->query());

        return PlaceResource::collection($places);
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
        if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'moderator'], true)) {
            return response()->json(['message' => 'Unauthorized'], 403);  
        }

        $validated = $request->validate([
            'destination_id' => ['required', 'integer', 'exists:destinations,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(Place::TYPES)],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', 'unique:places,slug'],
            'address' => ['sometimes', 'nullable', 'string', 'max:255'],
            'latitude' => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
            'price_level' => ['sometimes', 'nullable', 'integer', 'between:0,4'],
        ]);

        $baseSlug = $validated['slug'] ?? Str::slug(($validated['name'] ?? '') . '-' . (optional(Destination::find($validated['destination_id']))?->slug ?? ''));
        $slug = $baseSlug;
        $i = 1;
        while ($slug && Place::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $i++;
        }
        $validated['slug'] = $slug ?: Str::slug($validated['name'] . '-' . Str::random(6));

        $place = Place::create($validated);

        return response()->json([      
            'message' => 'Place created successfully',
            'place' => new PlaceResource($place->load('destination')->loadCount('reviews')),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Place $place)
    {
        $place->load(['destination'])->loadCount('reviews');

        return response()->json([      
            'place' => new PlaceResource($place),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Place $place)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Place $place)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'moderator'], true)) {
            return response()->json(['message' => 'Unauthorized'], 403);         
        }

        $validated = $request->validate([
            'destination_id' => ['sometimes', 'integer', 'exists:destinations,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', Rule::in(Place::TYPES)],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('places', 'slug')->ignore($place->id)],
            'address' => ['sometimes', 'nullable', 'string', 'max:255'],
            'latitude' => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
            'price_level' => ['sometimes', 'nullable', 'integer', 'between:0,4'],
        ]);

        if (!array_key_exists('slug', $validated) && (array_key_exists('name', $validated) || array_key_exists('destination_id', $validated))) {
            $newName = $validated['name'] ?? $place->name;
            $destId  = $validated['destination_id'] ?? $place->destination_id;
            $dest    = Destination::find($destId);
            $suggest = Str::slug($newName . '-' . ($dest?->slug ?? ''));
            if ($suggest !== $place->slug) {
                $base = $suggest;
                $i = 1;
                $slug = $suggest;
                while (Place::where('slug', $slug)->where('id', '!=', $place->id)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $validated['slug'] = $slug;
            }
        }

        if (empty($validated)) {
            return response()->json([       
                'message' => 'Nothing to update',
                'place' => new PlaceResource($place->load('destination')->loadCount('reviews')),
            ]);
        }

        $place->update($validated);

        return response()->json([     
            'message' => 'Place updated successfully',
            'place'   => new PlaceResource($place->load('destination')->loadCount('reviews')),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Place $place)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'moderator'], true)) {
            return response()->json(['message' => 'Unauthorized'], 403);      
        }

        $place->delete();

        return response()->json(['message' => 'Place deleted successfully']);       
    }
}