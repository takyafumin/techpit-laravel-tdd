<?php

namespace Tests\Feature\Http\Controllers\Api\Lesson;

use App\Models\Lesson;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\Factories\Traits\CreatesUser;
use Tests\TestCase;

class ReserveControllerTest extends TestCase
{
    use RefreshDatabase;
    use CreatesUser;

    public function test__invoke_正常系()
    {
        // Arrange
        $lesson = Lesson::factory()->create();
        $user = $this->createUser();
        $this->actingAs($user, 'sanctum');

        // Act
        $response = $this->postJson("/api/lessons/{$lesson->id}/reserve");

        // Assert
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson([
            'lesson_id' => $lesson->id,
            'user_id'   => $user->id,
        ]);
        $this->assertDatabaseHas('reservations', [
            'lesson_id' => $lesson->id,
            'user_id'   => $user->id,
        ]);
    }

    public function test__invoke_異常系()
    {
        // Arrange
        /** @var Lesson $lesson */
        $lesson = Lesson::factory()->create(['capacity' => 1]);
        $lesson->reservations()->save(Reservation::factory()->make());
        $user = $this->createUser();
        $this->actingAs($user, 'sanctum');

        // Act
        $response = $this->postJson("/api/lessons/{$lesson->id}/reserve");

        // Assert
        $response->assertStatus(Response::HTTP_CONFLICT);
        $response->assertJsonStructure(['error']);
        $error = $response->json('error');
        $this->assertStringContainsString('予約できません。', $error);
        $this->assertDatabaseMissing('reservations', [
            'lesson_id' => $lesson->id,
            'user_id'   => $user->id,
        ]);
    }
}
