<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
    public function index()
    {
        $goals = Goal::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get();
        return view('goals.index', compact('goals'));
    }

    public function create()
    {
        return view('goals.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'target_amount' => ['required', 'numeric', 'min:1'],
            'saved_amount'  => ['nullable', 'numeric', 'min:0'],
            'emoji'         => ['nullable', 'string', 'max:10'],
            'deadline'      => ['nullable', 'date'],
            'description'   => ['nullable', 'string', 'max:500'],
        ]);

        $validated['user_id']      = Auth::id();
        $validated['saved_amount'] = $validated['saved_amount'] ?? 0;
        $validated['emoji']        = $validated['emoji'] ?? '🎯';

        Goal::create($validated);

        return redirect()->route('goals.index')->with('success', 'Goal created!');
    }

    public function edit(Goal $goal)
    {
        $this->authorize('update', $goal);
        return view('goals.edit', compact('goal'));
    }

    public function update(Request $request, Goal $goal)
    {
        $this->authorize('update', $goal);

        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'target_amount' => ['required', 'numeric', 'min:1'],
            'saved_amount'  => ['nullable', 'numeric', 'min:0'],
            'emoji'         => ['nullable', 'string', 'max:10'],
            'deadline'      => ['nullable', 'date'],
            'description'   => ['nullable', 'string', 'max:500'],
        ]);

        $goal->update($validated);

        return redirect()->route('goals.index')->with('success', 'Goal updated!');
    }

    public function destroy(Goal $goal)
    {
        $this->authorize('delete', $goal);
        $goal->delete();
        return redirect()->route('goals.index')->with('success', 'Goal deleted.');
    }

    public function contribute(Request $request, Goal $goal)
    {
        $this->authorize('update', $goal);

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $goal->saved_amount = min($goal->target_amount, $goal->saved_amount + $validated['amount']);
        $goal->save();

        return redirect()->route('goals.index')
            ->with('success', "₹" . number_format($validated['amount'], 2) . " added to \"{$goal->name}\"!");
    }
}
