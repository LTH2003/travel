<?php

namespace App\Observers;

use App\Models\TourReview;

class TourReviewObserver
{

    public function created(TourReview $tourReview): void
    {
        
        $tourReview->tour->updateRating();
    }


    public function updated(TourReview $tourReview): void
    {
       
        $tourReview->tour->updateRating();
    }


    public function deleted(TourReview $tourReview): void
    {
        
        $tourReview->tour->updateRating();
    }

    public function restored(TourReview $tourReview): void
    {
        //
    }

    public function forceDeleted(TourReview $tourReview): void
    {
        
        $tourReview->tour->updateRating();
    }
}
