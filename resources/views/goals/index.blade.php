@extends('layouts.app')
@section('title', 'Savings Goals')
@section('page-title', 'Savings Goals')

@section('topbar-actions')
    <a href="{{ route('goals.create') }}" class="btn btn-outline btn-sm">+ New Goal</a>
@endsection

@section('content')

@if($goals->isEmpty())
    <div class="panel">
        <div class="empty-state large">
            <div class="empty-icon">🎯</div>
            <h3>No Savings Goals Yet</h3>
            <p>Set a savings goal to stay motivated and track your progress.</p>
            <a href="{{ route('goals.create') }}" class="btn btn-primary" style="margin-top:16px;">Create First Goal</a>
        </div>
    </div>
@else
    <div class="goals-grid">
        @foreach($goals as $goal)
            <div class="goal-card {{ $goal->is_completed ? 'completed' : '' }}">
                <div class="goal-header">
                    <div class="goal-emoji">{{ $goal->emoji }}</div>
                    <div class="goal-actions">
                        <a href="{{ route('goals.edit', $goal) }}" class="btn btn-outline btn-xs">Edit</a>
                        <form method="POST" action="{{ route('goals.destroy', $goal) }}" style="display:inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Delete goal?')">Del</button>
                        </form>
                    </div>
                </div>
                <div class="goal-name">{{ $goal->name }}</div>
                @if($goal->description)
                    <div class="goal-desc">{{ $goal->description }}</div>
                @endif
                <div class="goal-amounts">
                    <span>₹{{ number_format($goal->saved_amount, 2) }}</span>
                    <span class="muted">of ₹{{ number_format($goal->target_amount, 2) }}</span>
                </div>
                <div class="goal-bar">
                    <div class="goal-fill" style="width:{{ $goal->progress_percentage }}%"></div>
                </div>
                <div class="goal-meta">
                    <span class="goal-pct {{ $goal->is_completed ? 'complete' : '' }}">{{ $goal->progress_percentage }}%</span>
                    @if($goal->is_completed)
                        <span class="badge badge-income">✅ Completed!</span>
                    @else
                        <span class="muted small">₹{{ number_format($goal->remaining, 2) }} remaining</span>
                    @endif
                </div>

                @if($goal->deadline)
                    <div class="goal-deadline">📅 Deadline: {{ $goal->deadline->format('d M Y') }}</div>
                @endif

                @if(!$goal->is_completed)
                    <form method="POST" action="{{ route('goals.contribute', $goal) }}" class="contribute-form">
                        @csrf
                        <input type="number" name="amount" placeholder="Add amount..." class="form-control" min="1" step="0.01" required>
                        <button type="submit" class="btn btn-primary btn-sm">Add</button>
                    </form>
                @endif
            </div>
        @endforeach
    </div>
@endif

@endsection
