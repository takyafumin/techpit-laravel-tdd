<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    use HasFactory;

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * 空き状況Modelを返却する
     *
     * @return VacancyLevel 空き状況Model
     */
    public function getVacancyLevelAttribute(): VacancyLevel
    {
        return new VacancyLevel($this->remainingCount());
    }

    /**
     * 空き数を返却する
     *
     * @return int 空き数
     */
    public function remainingCount(): int
    {
        return $this->capacity - $this->reservations()->count();
    }
}
