<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticateUser()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        return $user;
    }

    public function test_user_can_create_note()
    {
        $this->authenticateUser();

        $response = $this->post(route('note.store'), [
            'title' => 'Test Note',
            'content' => 'This is a test note.'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('notes', ['title' => 'Test Note']);
    }

    public function test_user_can_update_note()
    {
        $user = $this->authenticateUser();
        $note = Note::factory()->create(['user_id' => $user->id]);

        $response = $this->put(route('note.update', $note), [
            'title' => 'Updated Note',
            'content' => 'This note has been updated.'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('notes', ['title' => 'Updated Note']);
    }

    public function test_user_can_delete_note()
    {
        $user = $this->authenticateUser();
        $note = Note::factory()->create(['user_id' => $user->id]);

        $response = $this->delete(route('note.destroy', $note));

        $response->assertRedirect();
        $this->assertDatabaseMissing('notes', ['id' => $note->id]);
    }

    public function test_user_can_view_note()
    {
        $user = $this->authenticateUser();
        $note = Note::factory()->create(['user_id' => $user->id]);

        $response = $this->get(route('note.show', $note));

        $response->assertStatus(200);
        $response->assertSee($note->title);
    }
}
