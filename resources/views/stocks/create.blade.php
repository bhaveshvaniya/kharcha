@extends('layouts.app')
@section('title', 'Add Stock')
@section('page-title', 'Add New Stock')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-plus-circle me-2"></i>Stock Details</div>
            <div class="card-body">
                <form method="POST" action="{{ route('stocks.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Stock Symbol <span class="text-danger">*</span></label>
                            <input type="text" name="symbol" class="form-control @error('symbol') is-invalid @enderror"
                                value="{{ old('symbol') }}" placeholder="e.g. RELIANCE" required style="text-transform:uppercase;">
                            @error('symbol')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Company Name <span class="text-danger">*</span></label>
                            <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror"
                                value="{{ old('company_name') }}" placeholder="e.g. Reliance Industries Ltd" required>
                            @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Exchange <span class="text-danger">*</span></label>
                            <select name="exchange" class="form-select">
                                <option value="NSE" {{ old('exchange') == 'NSE' ? 'selected' : '' }}>NSE</option>
                                <option value="BSE" {{ old('exchange') == 'BSE' ? 'selected' : '' }}>BSE</option>
                                <option value="NYSE">NYSE</option>
                                <option value="NASDAQ">NASDAQ</option>
                                <option value="OTHER">Other</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Sector</label>
                            <select name="sector" class="form-select">
                                <option value="">Select Sector</option>
                                @foreach(['IT', 'Banking', 'Pharma', 'Energy', 'Auto', 'FMCG', 'Metal', 'Real Estate', 'Telecom', 'Infrastructure', 'Finance', 'Other'] as $sec)
                                    <option value="{{ $sec }}" {{ old('sector') == $sec ? 'selected' : '' }}>{{ $sec }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Previous Close (₹) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="previous_close" class="form-control @error('previous_close') is-invalid @enderror"
                                    value="{{ old('previous_close', 0) }}" step="0.01" min="0" required>
                            </div>
                            @error('previous_close')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Current Price (₹) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="current_price" class="form-control @error('current_price') is-invalid @enderror"
                                    value="{{ old('current_price', 0) }}" step="0.01" min="0" required>
                            </div>
                            @error('current_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i> Add Stock</button>
                        <a href="{{ route('stocks.index') }}" class="btn btn-light px-4">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
