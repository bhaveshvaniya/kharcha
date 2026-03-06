@extends('layouts.app')
@section('title', 'Add Transaction')
@section('page-title', 'Add Transaction')

@section('content')
<div style="max-width:640px;margin:0 auto;">
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">New Transaction</div>
        </div>
        <div style="padding:32px;">
            <form method="POST" action="{{ route('transactions.store') }}">
                @csrf

                <!-- Type Toggle -->
                <div class="form-group" style="margin-bottom:20px;">
                    <label class="form-label">Transaction Type</label>
                    <div class="type-toggle" id="typeToggle">
                        <button type="button" class="type-btn type-income {{ old('type', 'expense') == 'income' ? 'active' : '' }}" onclick="setType('income')">
                            ▲ Income
                        </button>
                        <button type="button" class="type-btn type-expense {{ old('type', 'expense') == 'expense' ? 'active' : '' }}" onclick="setType('expense')">
                            ▼ Expense
                        </button>
                    </div>
                    <input type="hidden" name="type" id="typeInput" value="{{ old('type', 'expense') }}">
                    @error('type')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Amount (₹) <span class="required">*</span></label>
                        <input type="number" name="amount" value="{{ old('amount') }}" class="form-control @error('amount') is-invalid @enderror" placeholder="0.00" min="0.01" step="0.01" required>
                        @error('amount')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Date <span class="required">*</span></label>
                        <input type="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" class="form-control @error('date') is-invalid @enderror" required>
                        @error('date')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Description <span class="required">*</span></label>
                    <input type="text" name="description" value="{{ old('description') }}" class="form-control @error('description') is-invalid @enderror" placeholder="What was this for?" required>
                    @error('description')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Category <span class="required">*</span></label>
                    <select name="category" class="form-control @error('category') is-invalid @enderror" required>
                        <option value="">Select a category...</option>
                        @foreach($categories as $cat => $icon)
                            <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $icon }} {{ $cat }}</option>
                        @endforeach
                    </select>
                    @error('category')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Note <span class="muted">(optional)</span></label>
                    <textarea name="note" class="form-control" rows="2" placeholder="Any additional notes...">{{ old('note') }}</textarea>
                </div>

                <div class="actions-row with-top-lg">
                    <button type="submit" class="btn btn-primary">Save Transaction</button>
                    <a href="{{ route('transactions.index') }}" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function setType(type) {
    document.getElementById('typeInput').value = type;
    document.querySelectorAll('.type-btn').forEach(b => b.classList.remove('active'));
    document.querySelector('.type-' + type).classList.add('active');
}
</script>
@endpush

