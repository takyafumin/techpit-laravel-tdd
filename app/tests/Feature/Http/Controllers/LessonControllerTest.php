<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Lesson;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class LessonControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param int $capacity 空き数
     * @param int $reservationCount 予約数
     * @param string $expectedVacancyLevelMark 期待値
     * @param string $button ボタンHTML文字列
     * @dataProvider dataShow
     */
    public function testShow(
        int $capacity,
        int $reservationCount,
        string $expectedVacancyLevelMark,
        string $button
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
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->get("/lessons/{$lesson->id}");

        // assert
        $response->assertStatus(Response::HTTP_OK);
        $response->assertSee("$lesson->name");
        $response->assertSee("空き状況: {$expectedVacancyLevelMark}");
        $response->assertSee($button, false);
    }

    public function dataShow()
    {
        $button = '<button class="btn btn-primary">このレッスンを予約する</button>';
        $span   = '<span class="btn btn-primary disabled">予約できません</span>';

        return [
            '空き十分' => [
                'capacity'                 => 6,
                'reservationCount'         => 1,
                'expectedVacancyLevelMark' => '◎',
                'button'                   => $button,
            ],
            '空きわずか' => [
                'capacity'                 => 6,
                'reservationCount'         => 2,
                'expectedVacancyLevelMark' => '△',
                'button'                   => $button,
            ],
            '空きなし' => [
                'capacity'                 => 6,
                'reservationCount'         => 6,
                'expectedVacancyLevelMark' => '×',
                'button'                   => $span,
            ],
        ];
    }
}
