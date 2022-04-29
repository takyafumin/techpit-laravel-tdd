<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Vacancy Level Model
 *
 * @package App\Models
 *
 * @property int $remainingCount 空き数
 */
class VacancyLevel extends Model
{
    /**
     * constructor
     *
     * @param int $remainingCount 空き数
     */
    public function __construct(
        private int $remainingCount
    ) {
    }

    /**
     * 空き状況のマークを返却する
     *
     * @return string 空き状況マーク
     */
    public function mark(): string
    {
        if ($this->remainingCount === 0) {
            return '×';
        }

        if ($this->remainingCount < 5) {
            return '△';
        }

        return '◎';
    }

    /**
     * 空き状況のslugを返却する
     *
     * @return string 空き状況slug
     */
    public function slug(): string
    {
        if ($this->remainingCount === 0) {
            return 'empty';
        }

        if ($this->remainingCount < 5) {
            return 'few';
        }

        return 'enough';
    }

    public function __toString()
    {
        return $this->mark();
    }
}
