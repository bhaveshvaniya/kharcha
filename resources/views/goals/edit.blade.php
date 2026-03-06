@extends('layouts.app')
@section('title', 'Edit Goal')
@section('page-title', 'Edit Savings Goal')

@section('content')
<div style="max-width:580px;margin:0 auto;">
    <div class="panel">
        <div class="panel-header"><div class="panel-title">✏️ Edit Goal</div></div>
        <div style="padding:32px;">
            <form method="POST" action="{{ route('goals.update', $goal) }}">
                @csrf @method('PUT')
                <div class="form-group">
                    <label class="form-label">Goal Name</label>
                    <input type="text" name="name" value="{{ old('name', $goal->name) }}" class="form-control" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Target Amount (₹)</label>
                        <input type="number" name="target_amount" value="{{ old('target_amount', $goal->target_amount) }}" class="form-control" min="1" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Saved Amount (₹)</label>
                        <input type="number" name="saved_amount" value="{{ old('saved_amount', $goal->saved_amount) }}" class="form-control" min="0" step="0.01">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Emoji</label>
                        <input type="text" name="emoji" value="{{ old('emoji', $goal->emoji) }}" class="form-control" maxlength="4">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Deadline</label>
                        <input type="date" name="deadline" value="{{ old('deadline', $goal->deadline?->format('Y-m-d')) }}" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="2">{{ old('description', $goal->description) }}</textarea>
                </div>
                <div class="actions-row with-top-lg">
                    <button type="submit" class="btn btn-primary">Update Goal</button>
                    <a href="{{ route('goals.index') }}" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

