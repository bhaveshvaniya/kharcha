@extends('layouts.app')
@section('title', 'New Goal')
@section('page-title', 'Create Savings Goal')

@section('content')
<div style="max-width:580px;margin:0 auto;">
    <div class="panel">
        <div class="panel-header"><div class="panel-title">🎯 New Savings Goal</div></div>
        <div style="padding:32px;">
            <form method="POST" action="{{ route('goals.store') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Goal Name <span class="required">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="e.g. Emergency Fund, Vacation, New Car" required>
                    @error('name')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Target Amount (₹) <span class="required">*</span></label>
                        <input type="number" name="target_amount" value="{{ old('target_amount') }}" class="form-control" min="1" step="0.01" placeholder="0.00" required>
                        @error('target_amount')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Already Saved (₹)</label>
                        <input type="number" name="saved_amount" value="{{ old('saved_amount', 0) }}" class="form-control" min="0" step="0.01" placeholder="0.00">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Emoji Icon</label>
                        <input type="text" name="emoji" value="{{ old('emoji', '🎯') }}" class="form-control" maxlength="4" placeholder="🎯">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Deadline (optional)</label>
                        <input type="date" name="deadline" value="{{ old('deadline') }}" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Description (optional)</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="What is this goal for?">{{ old('description') }}</textarea>
                </div>
                <div class="actions-row with-top-lg">
                    <button type="submit" class="btn btn-primary">Create Goal</button>
                    <a href="{{ route('goals.index') }}" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

