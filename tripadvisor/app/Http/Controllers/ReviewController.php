<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReviewResource;
use App\Models\Place;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Place $place)
    {
        $validated = $request->validate([
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page'  => ['sometimes', 'integer', 'min:1'],
        ]);
        $perPage = (int)($validated['per_page'] ?? 15);

        $reviews = Review::query()
            ->where('place_id', $place->id)
            ->with(['user'])
            ->latest()
            ->paginate($perPage)
            ->appends($request->query());

        return ReviewResource::collection($reviews);
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
        $user = Auth::user();
        if (!$user || $user->role !== 'user') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'place_id' => ['required', 'integer', 'exists:places,id'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'body' => ['sometimes', 'nullable', 'string'],
        ]);

        $exists = Review::where('user_id', $user->id)
            ->where('place_id', $validated['place_id'])
            ->exists();
        if ($exists) {
            return response()->json(['message' => 'You have already reviewed this place.'], 422);
        }

        $review = Review::create([
            'user_id' => $user->id,
            'place_id' => $validated['place_id'],
            'rating' => $validated['rating'],
            'title' => $validated['title'] ?? null,
            'body' => $validated['body'] ?? null,
        ]);

        $this->recalculatePlaceAggregates($validated['place_id']);

        return response()->json([
            'message' => 'Review created successfully',
            'review'  => new ReviewResource($review->load('user', 'place')),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Review $review)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Review $review)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'user') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ((int)$review->user_id !== (int)$user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $placeId = $review->place_id;
        $review->delete();

        $this->recalculatePlaceAggregates($placeId);

        return response()->json(['message' => 'Review deleted successfully']);
    }

    private function recalculatePlaceAggregates(int $placeId): void
    {
        $agg = Review::where('place_id', $placeId)
            ->selectRaw('COUNT(*) as cnt, COALESCE(AVG(rating),0) as avg_rating')
            ->first();

        $place = Place::find($placeId);
        if ($place) {
            $place->reviews_count = (int) ($agg->cnt ?? 0);
            $place->rating_avg    = round((float) ($agg->avg_rating ?? 0), 2);
            $place->save();
        }
    }
}