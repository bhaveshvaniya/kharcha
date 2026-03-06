@extends('layouts.app')
@section('title', 'Edit Transaction')
@section('page-title', 'Edit Transaction')

@section('content')
<div style="max-width:640px;margin:0 auto;">
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">Edit Transaction</div>
        </div>
        <div style="padding:32px;">
            <form method="POST" action="{{ route('transactions.update', $transaction) }}">
                @csrf @method('PUT')

                <!-- Type Toggle -->
                <div class="form-group" style="margin-bottom:20px;">
                    <label class="form-label">Transaction Type</label>
                    <div class="type-toggle">
                        <button type="button" class="type-btn type-income {{ old('type', $transaction->type) == 'income' ? 'active' : '' }}" onclick="setType('income')">▲ Income</button>
                        <button type="button" class="type-btn type-expense {{ old('type', $transaction->type) == 'expense' ? 'active' : '' }}" onclick="setType('expense')">▼ Expense</button>
                    </div>
                    <input type="hidden" name="type" id="typeInput" value="{{ old('type', $transaction->type) }}">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Amount (₹)</label>
                        <input type="number" name="amount" value="{{ old('amount', $transaction->amount) }}" class="form-control" step="0.01" min="0.01" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Date</label>
                        <input type="date" name="date" value="{{ old('date', $transaction->date->format('Y-m-d')) }}" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" value="{{ old('description', $transaction->description) }}" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-control" required>
                        @foreach($categories as $cat => $icon)
                            <option value="{{ $cat }}" {{ old('category', $transaction->category) == $cat ? 'selected' : '' }}>{{ $icon }} {{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Note</label>
                    <textarea name="note" class="form-control" rows="2">{{ old('note', $transaction->note) }}</textarea>
                </div>

                <div class="actions-row with-top-lg">
                    <button type="submit" class="btn btn-primary">Update Transaction</button>
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

