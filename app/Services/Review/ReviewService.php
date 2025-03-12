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
        // $this->updateUserRating($service->user_id);

        return new ReviewResource($review);
    }

    public function updateReview(Request $request, Review $review)
    {
        $this->authorizeAction($review);
        $review->update($request->only(['rating', 'comment']));
        // $this->updateUserRating($review->service->user_id);

        return new ReviewResource($review);
    }

    public function deleteReview(Review $review)
    {
        $this->authorizeAction($review);
        $review->delete();
        // $this->updateUserRating($review->service->user_id);

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

    public function getUserAverageRating($userId)
    {
        $services = Service::where('service_provider_id', $userId)->get();

        $totalRating = 0;
        $serviceCount = 0;

        foreach ($services as $service) {
            $totalRating += $this->getAverageRating($service->id);
            $serviceCount++;
        }

        return $serviceCount > 0 ? $totalRating / $serviceCount : 0;
    }

    // private function updateUserRating($userId)
    // {
    //     $user = User::findOrFail($userId);
        
    //     $userRating = $this->getUserAverageRating($userId);

    //     $user->update([
    //         'average_rating' => $userRating,
    //     ]);
    // }
    private function authorizeAction(Review $review)
    {
        if ($review->user_id !== auth()->id()) {
            abort(403, __('messages.unauthorized'));
        }
    }
}
