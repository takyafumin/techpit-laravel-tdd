<?php

namespace Tests\Unit\Models;

use App\Models\VacancyLevel;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

class VacancyLevelTest extends TestCase
{
    /**
     * 空き状況のマークをテストします
     *
     * @param int $remainingCount 空き件数
     * @param string $expectedMark 期待値
     * @dataProvider dataMark
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function testMark(int $remainingCount, string $expectedMark)
    {
        $level = new VacancyLevel($remainingCount);
        $this->assertSame($expectedMark, $level->mark());
    }

    /**
     * data for testMark
     *
     * @return (int|string)[][] テストデータ
     *  - テストケース名
     *  - 空き件数
     *  - 期待値
     */
    public function dataMark()
    {
        return [
            '空き無し' => [
                'remainingCount' => 0,
                'expectedMark'   => '×',
            ],
            '残り僅か' => [
                'remainingCount' => 4,
                'expectedMark'   => '△',
            ],
            '空き十分' => [
                'remainingCount' => 5,
                'expectedMark'   => '◎',
            ],
        ];
    }

    /**
     * 空き状況のslugをテストします
     *
     * @param int $remainingCount 空き件数
     * @param string $expectedSlug 期待値
     * @dataProvider dataSlug
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function testSlug(int $remainingCount, string $expectedSlug)
    {
        $level = new VacancyLevel($remainingCount);
        $this->assertSame($expectedSlug, $level->slug());
    }

    /**
     * data for testSlug
     *
     * @return (int|string)[][] テストデータ
     *  - テストケース名
     *  - 空き件数
     *  - 期待値
     */
    public function dataSlug()
    {
        return [
            '空き無し' => [
                'remainingCount' => 0,
                'expectedSlug'   => 'empty',
            ],
            '残り僅か' => [
                'remainingCount' => 4,
                'expectedSlug'   => 'few',
            ],
            '空き十分' => [
                'remainingCount' => 5,
                'expectedSlug'   => 'enough',
            ],
        ];
    }
}

