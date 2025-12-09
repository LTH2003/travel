<?php

namespace App\Observers;

use App\Models\TourReview;

class TourReviewObserver
{
    /**
     * Handle the TourReview "created" event.
     */
    public function created(TourReview $tourReview): void
    {
        // Update tour rating when review is created
        $tourReview->tour->updateRating();
    }

    /**
     * Handle the TourReview "updated" event.
     */
    public function updated(TourReview $tourReview): void
    {
        // Update tour rating when review is updated
        $tourReview->tour->updateRating();
    }

    /**
     * Handle the TourReview "deleted" event.
     */
    public function deleted(TourReview $tourReview): void
    {
        // Update tour rating when review is deleted
        $tourReview->tour->updateRating();
    }

    /**
     * Handle the TourReview "restored" event.
     */
    public function restored(TourReview $tourReview): void
    {
        //
    }

    /**
     * Handle the TourReview "force deleted" event.
     */
    public function forceDeleted(TourReview $tourReview): void
    {
        // Update tour rating when review is force deleted
        $tourReview->tour->updateRating();
    }
}
