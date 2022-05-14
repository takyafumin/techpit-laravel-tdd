<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Lesson;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use LogicException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use Tests\TestCase;

class LessonControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param int $capacity 空き数
     * @param int $reservationCount 予約数
     * @param string $expectedVacancyLevelMark 期待値
     * @dataProvider dataShow
     */
    public function testShow(
        int $capacity,
        int $reservationCount,
        string $expectedVacancyLevelMark
    ) {
        // setup
        $lesson = Lesson::factory()->create([
            'name'     => '楽しいヨガレッスン',
            'capacity' => $capacity,
        ]);
        for ($i = 0; $i < $reservationCount; $i++) {
            $user = User::factory()->create();
            $lesson->reservations()->save(Reservation::factory()->make(['user_id' => $user]));
        }

        // exec
        $response = $this->get("/lessons/{$lesson->id}");

        // assert
        $response->assertStatus(Response::HTTP_OK);
        $response->assertSee("$lesson->name");
        $response->assertSee($expectedVacancyLevelMark);
    }

    public function dataShow()
    {
        return [
            '空き十分' => [
                'capacity'                 => 6,
                'reservationCount'         => 1,
                'expectedVacancyLevelMark' => '◎',
            ],
            '空きわずか' => [
                'capacity'                 => 6,
                'reservationCount'         => 2,
                'expectedVacancyLevelMark' => '△',
            ],
            '空きなし' => [
                'capacity'                 => 6,
                'reservationCount'         => 6,
                'expectedVacancyLevelMark' => '×',
            ],
        ];
    }
}
