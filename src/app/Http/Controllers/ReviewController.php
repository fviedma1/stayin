<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;

class ReviewController extends Controller
{
    public function create($token)
    {
        $review = Review::where('token', $token)
            ->where('used', false)
            ->firstOrFail();

        return view('review.form', compact('review'));
    }

    public function store(Request $request, Review $review)
    {
        $request->validate([
            'stars' => 'required|integer|between:1,5',
            'comment' => 'required|string|max:500'
        ]);

        if ($review->used) {
            abort(403, 'Este formulario ya fue utilizado');
        }

        $review->update([
            'stars' => $request->stars,
            'comment' => $request->comment,
            'used' => true
        ]);

        return redirect()->route('review.thankyou');
    }
}
