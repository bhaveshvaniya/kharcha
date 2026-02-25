@extends('layouts.app')
@section('title', 'Edit Stock')
@section('page-title', 'Edit Stock – ' . $stock->symbol)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-pencil me-2"></i>Edit Stock – <strong>{{ $stock->symbol }}</strong></div>
            <div class="card-body">
                <form method="POST" action="{{ route('stocks.update', $stock) }}">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Stock Symbol</label>
                            <input type="text" class="form-control bg-light" value="{{ $stock->symbol }}" disabled>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Company Name <span class="text-danger">*</span></label>
                            <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror"
                                value="{{ old('company_name', $stock->company_name) }}" required>
                            @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Exchange</label>
                            <select name="exchange" class="form-select">
                                @foreach(['NSE','BSE','NYSE','NASDAQ','OTHER'] as $ex)
                                    <option value="{{ $ex }}" {{ $stock->exchange == $ex ? 'selected' : '' }}>{{ $ex }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Sector</label>
                            <select name="sector" class="form-select">
                                <option value="">Select Sector</option>
                                @foreach(['IT', 'Banking', 'Pharma', 'Energy', 'Auto', 'FMCG', 'Metal', 'Real Estate', 'Telecom', 'Infrastructure', 'Finance', 'Other'] as $sec)
                                    <option value="{{ $sec }}" {{ $stock->sector == $sec ? 'selected' : '' }}>{{ $sec }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Previous Close (₹)</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="previous_close" class="form-control" value="{{ $stock->previous_close }}" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Current Price (₹)</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="current_price" class="form-control" value="{{ $stock->current_price }}" step="0.01" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i> Update Stock</button>
                        <a href="{{ route('stocks.index') }}" class="btn btn-light px-4">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
