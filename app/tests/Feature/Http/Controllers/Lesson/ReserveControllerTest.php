<?php

namespace Tests\Feature\Http\Controllers\Lesson;

use App\Models\Lesson;
use App\Models\Reservation;
use App\Notifications\ReservationCompleted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Notification;
use Tests\Factories\Traits\CreatesUser;
use Tests\TestCase;

class ReserveControllerTest extends TestCase
{
    use RefreshDatabase;
    use CreatesUser;

    public function test__invoke()
    {
        // Arrange
        Notification::fake();
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
        Notification::assertSentTo(
            $user,
            ReservationCompleted::class,
            function (ReservationCompleted $notification) use ($lesson) {
                return $notification->lesson->id === $lesson->id;
            }
        );
    }

    public function test__invoke_異常系()
    {
        // Arrange
        Notification::fake();
        /** @var Lesson $lesson */
        $lesson = Lesson::factory()->create(['capacity' => 1]);
        $anotherUser = $this->createUser();
        $lesson->reservations()->save(Reservation::factory()->make(['user_id' => $anotherUser->id]));

        $user = $this->createUser();
        $this->actingAs($user);

        // Act
        $response = $this->from("/lessons/{$lesson->id}")
            ->post("/lessons/{$lesson->id}/reserve");

        // Assert
        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirect("/lessons/{$lesson->id}");

        $response->assertSessionHasErrors();
        $error = session('errors')->first();
        $this->assertStringContainsString('予約できません', $error);

        $this->assertDatabaseMissing('reservations', [
            'lesson_id' => $lesson->id,
            'user_id'   => $user->id,
        ]);
        Notification::assertNotSentTo($user, ReservationCompleted::class);
    }
}
