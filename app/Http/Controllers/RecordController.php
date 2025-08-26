<?php

namespace App\Http\Controllers;

use App\Models\Record;
use Illuminate\Http\Request;

class RecordController extends Controller
{
    public function index()
    {
        $records = Record::latest()->paginate(15);
        return view('records.index', compact('records'));
    }

    public function create()
    {
        return view('records.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate(['title' => ['required','string','max:255']]);
        Record::create($data + ['user_id' => $request->user()->id]);
        return redirect()->route('records.index')->with('status', __('Created'));
    }

    public function edit(Record $record)
    {
        return view('records.edit', compact('record'));
    }

    public function update(Request $request, Record $record)
    {
        $data = $request->validate(['title' => ['required','string','max:255']]);
        $record->update($data);
        return redirect()->route('records.index')->with('status', __('Updated'));
    }

    public function destroy(Record $record)
    {
        $record->delete();
        return redirect()->route('records.index')->with('status', __('Deleted'));
    }
}
