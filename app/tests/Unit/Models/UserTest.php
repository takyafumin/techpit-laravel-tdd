<?php

namespace Tests\Unit\Models;

use App\Models\Lesson;
use App\Models\User;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * @param string $plan
     * @param int $reminingCount
     * @param int $reservationCount
     * @dataProvider dataCanReserve_正常
     */
    public function testCanReserve_正常(
        string $plan,
        int $remainingCount,
        int $reservationCount
    ) {
        // Setup

        /** @var User $user */
        $user = $this->partialMock(User::class, function (MockInterface $mock) use ($reservationCount) {
            $mock->shouldReceive('reservationCountThisMonth')->andReturn($reservationCount);
        });
        $user->plan = $plan;

        /** @var Lesson $lesson */
        $lesson = $this->mock(Lesson::class, function (MockInterface $mock) use ($remainingCount) {
            $mock->shouldReceive('remainingCount')->andReturn($remainingCount);
        });

        // Exec
        $user->canReserve($lesson);

        // Assert
        $this->assertTrue(true);
    }

    public function dataCanReserve_正常()
    {
        return [
            '予約可能: レギュラー, 空きあり, 月の上限未満' => [
                'plan'             => 'regular',
                'reminingCount'    => 1,
                'reservationCount' => 4,
                'canReserve'       => true,
            ],
            '予約可能: ゴールド, 空きあり' => [
                'plan'             => 'gold',
                'reminingCount'    => 1,
                'reservationCount' => 1,
                'canReserve'       => true,
            ],
        ];
    }

    /**
     * @param string $plan
     * @param int $remainingCount
     * @param int $reservationCount
     * @param string $errorMessage
     * @dataProvider dataCanReserve_エラー
     */
    public function testCanReserve_エラー(
        string $plan,
        int $remainingCount,
        int $reservationCount,
        string $errorMessage
    ) {
        // setup

        /** @var User $user */
        $user = $this->partialMock(User::class, function (MockInterface $mock) use ($reservationCount) {
            $mock->shouldReceive('reservationCountThisMonth')->andReturn($reservationCount);
        });
        $user->plan = $plan;

        /** @var Lesson $lesson */
        $lesson = $this->mock(Lesson::class, function (MockInterface $mock) use ($remainingCount) {
            $mock->shouldReceive('remainingCount')->andReturn($remainingCount);
        });

        $this->expectExceptionMessage($errorMessage);

        // Exec
        $user->canReserve($lesson);
    }

    public function dataCanReserve_エラー()
    {
        return [
            '予約不可: レギュラー, 空きあり, 月の上限以上' => [
                'plan'             => 'regular',
                'reminingCount'    => 1,
                'reservationCount' => 5,
                'canReserve'       => '今月の予約がプランの上限に達しています。',
            ],
            '予約不可: レギュラー, 空きなし' => [
                'plan'             => 'regular',
                'reminingCount'    => 0,
                'reservationCount' => 1,
                'canReserve'       => 'レッスンの予約可能上限に達しています。',
            ],
            '予約不可: ゴールド, 空きなし' => [
                'plan'             => 'gold',
                'reminingCount'    => 0,
                'reservationCount' => 1,
                'canReserve'       => 'レッスンの予約可能上限に達しています。',
            ],
        ];
    }
}
