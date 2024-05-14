<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNoteRequest;
use App\Models\Note;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notes = Note::query()
            ->where('user_id', request()->user()->id)
            ->orderBy("created_at","desc")
            ->paginate();
        return view("note.index", compact("notes"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("note.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNoteRequest $request)
    {
        $data = $request->validated();
        
        //hardcoded first test user
        $data['user_id'] = $request->user()->id;
        $note = Note::create($data);

        return to_route('note.show', $note)->with('message','Note was created');
    }

    /**
     * Display the specified resource.
     */
    public function show(Note $note)
    {
        if (request()->user()->id !== $note->user_id) {
            abort(403);
        }
        return view('note.show', compact("note"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Note $note)
    {
        return view('note.edit', compact("note"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreNoteRequest $request, Note $note)
    {
        $data = $request->validated();
             
        $note->update($data);

        return to_route('note.show', $note)->with('message','Note was updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note)
    {
        return to_route('note.index')->with('message','Note was deleted');

    }
}
