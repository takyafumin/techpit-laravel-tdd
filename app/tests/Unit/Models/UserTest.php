<?php

namespace Tests\Unit\Models;

use App\Models\User;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

class UserTest extends TestCase
{
    /**
     * @dataProvider dataCanReserve
     * @param string $plan
     * @param int $reminingCount
     * @param int $reservationCount
     * @param bool $canReserve
     */
    public function testCanReserve(
        string $plan,
        int $remainingCount,
        int $reservationCount,
        bool $canReserve,
    ) {
        // Setup
        $user = new User();
        $user->plan = $plan;

        // Exec, Assert
        $this->assertSame($canReserve, $user->canReserve($remainingCount, $reservationCount));

    }

    public function dataCanReserve()
    {
        return [
            '予約可能: レギュラー, 空きあり, 月の上限未満' => [
                'plan'             => 'regular',
                'reminingCount'    => 1,
                'reservationCount' => 4,
                'canReserve'       => true,
            ],
            '予約不可: レギュラー, 空きあり, 月の上限以上' => [
                'plan'             => 'regular',
                'reminingCount'    => 1,
                'reservationCount' => 5,
                'canReserve'       => false,
            ],
            '予約不可: レギュラー, 空きなし' => [
                'plan'             => 'regular',
                'reminingCount'    => 0,
                'reservationCount' => 1,
                'canReserve'       => false,
            ],
            '予約可能: ゴールド, 空きあり' => [
                'plan'             => 'gold',
                'reminingCount'    => 1,
                'reservationCount' => 1,
                'canReserve'       => true,
            ],
            '予約不可: ゴールド, 空きなし' => [
                'plan'             => 'gold',
                'reminingCount'    => 0,
                'reservationCount' => 1,
                'canReserve'       => false,
            ],
        ];
    }
}
