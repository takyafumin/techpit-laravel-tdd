<?php

namespace Tests\Feature\Http\Controllers\Lesson;

use App\Models\Lesson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\Factories\Traits\CreatesUser;
use Tests\TestCase;

class ReserveControllerTest extends TestCase
{
    use RefreshDatabase;
    use CreatesUser;

    public function test__invoke()
    {
        // Arrange
        $lesson = Lesson::factory()->create();
        $user = $this->createUser();
        $this->actingAs($user);

        // Act
        $response = $this->post("/lessons/{$lesson->id}/reserve");

        // Assert
        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirect("/lessons/{$lesson->id}");
        $this->assertDatabaseHas('reservations', [
            'lesson_id' => $lesson->id,
            'user_id'   => $user->id,
        ]);
    }
}
