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
        $user = $this->authenticateUser();

        $response = $this->post(route('note.store'), [            
            'note' => 'This is a test note.',            
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('notes', ['note' => 'This is a test note.']);
    }

    public function test_user_can_update_note()
    {
        $user = $this->authenticateUser();
        $note = Note::factory()->create(['user_id' => $user->id]);

        $response = $this->patch(route('note.update', $note), [            
            'note' => 'This note has been updated.'
        ]);

        $note->refresh();
        
        $response->assertRedirect();
        $this->assertEquals('This note has been updated.', $note->note);
    }

    public function test_user_can_delete_note()
    {        
        $user = $this->authenticateUser();
        $note = Note::factory()->create(['user_id' => $user->id]);

        $response = $this->delete(route('note.destroy', $note));        

        $response->assertRedirect();
        $this->assertModelMissing($note);
    }

    public function test_user_can_view_note()
    {
        $user = $this->authenticateUser();
        $note = Note::factory()->create(['user_id' => $user->id]);

        $response = $this->get(route('note.show', $note));

        $response->assertStatus(200);
        $response->assertSee($note->note); 
    }
}
