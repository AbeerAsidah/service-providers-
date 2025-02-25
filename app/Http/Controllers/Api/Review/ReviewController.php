<?php
namespace App\Http\Controllers\Api\Review;

use App\Http\Controllers\Controller;
use App\Services\Review\ReviewService;
use App\Models\Review;
use App\Http\Requests\Api\Review\StoreReviewRequest;
use App\Http\Requests\Api\Review\UpdateReviewRequest;
use App\Http\Requests\Api\Review\DeleteReviewRequest;

class ReviewController extends Controller
{
    protected $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    public function store(StoreReviewRequest $request, $serviceId)
    {
        try {
            $review = $this->reviewService->createReview($request, $serviceId);
            return success(['review' => $review], 201, ['message' => __('messages.review_added')]);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 400);
        }
    }

    public function update(UpdateReviewRequest $request, Review $review)
    {
        try {
            $updatedReview = $this->reviewService->updateReview($request, $review);
            return success(['review' => $updatedReview], 200, ['message' => __('messages.review_updated')]);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 400);
        }
    }

    public function destroy(DeleteReviewRequest $request, Review $review)
    {
        try {
            $this->reviewService->deleteReview($review);
            return success( ['message' => __('messages.review_deleted')]);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 400);
        }
    }

    public function getReviewsByService($serviceId)
    {
        try {
            $reviews = $this->reviewService->getReviewsByService($serviceId);
            return success(['reviews' => $reviews], 200, ['message' => __('messages.reviews_fetched')]);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 400);
        }
    }

    public function getAverageRating($serviceId)
    {
        try {
            $averageRating = $this->reviewService->getAverageRating($serviceId);
            return success(['average_rating' => $averageRating], 200, ['message' => __('messages.average_rating_fetched')]);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 400);
        }
    }
}
