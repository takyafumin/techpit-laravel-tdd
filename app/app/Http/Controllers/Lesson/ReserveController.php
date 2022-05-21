<?php

namespace App\Http\Controllers\Lesson;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;

class ReserveController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Lesson $lesson)
    {
        $user = Auth::user();
        Reservation::create(['lesson_id' => $lesson->id, 'user_id' => $user->id]);

        // 予約
        return redirect()->route('lessons.show', ['lesson' => $lesson]);
    }
}
