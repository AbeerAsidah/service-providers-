<?php
namespace App\Services\Review;

use App\Models\Review;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Resources\ReviewResource;

class ReviewService
{
    public function createReview(Request $request, $serviceId)
    {
        $service = Service::findOrFail($serviceId);
        $review = $service->reviews()->create([
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return new ReviewResource($review);
    }

    public function updateReview(Request $request, Review $review)
    {
        $this->authorizeAction($review);
        $review->update($request->only(['rating', 'comment']));

        return new ReviewResource($review);
    }

    public function deleteReview(Review $review)
    {
        $this->authorizeAction($review);
        $review->delete();
    }

    public function getReviewsByService($serviceId)
    {
        $reviews = Review::where('service_id', $serviceId)->with('user')->latest()->get();
        return ReviewResource::collection($reviews);
    }

    public function getAverageRating($serviceId)
    {
        return Review::where('service_id', $serviceId)->avg('rating') ?? 0;
    }

    private function authorizeAction(Review $review)
    {
        if ($review->user_id !== auth()->id()) {
            abort(403, __('messages.unauthorized'));
        }
    }
}
