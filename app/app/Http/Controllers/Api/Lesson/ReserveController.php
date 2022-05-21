<?php

namespace App\Http\Controllers\Api\Lesson;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Response;
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
        /** @var User $user */
        $user = Auth::user();
        try {
            $user->canReserve($lesson);
        } catch (\Throwable $e) {
            return response()->json(['error' => '予約できません。:' . $e->getMessage()], Response::HTTP_CONFLICT);
        }
        $reservation = Reservation::create(['lesson_id' => $lesson->id, 'user_id' => $user->id]);

        return response()->json($reservation, Response::HTTP_CREATED);
    }
}
