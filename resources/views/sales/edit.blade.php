@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Sale #{{ $sale->id }}</h1>

    @include('partials.alerts')

    <form action="{{ route('sales.update', $sale) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="customer_id" class="form-label">Customer</label>
            <select name="customer_id" id="customer_id" class="form-control">
                <option value="">Select customer</option>
                @foreach($customers as $id => $name)
                    <option value="{{ $id }}" {{ $sale->customer_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" name="date" id="date" class="form-control" value="{{ old('date', $sale->date->format('Y-m-d')) }}">
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-control">
                <option value="draft" {{ $sale->status == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="confirmed" {{ $sale->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="cancelled" {{ $sale->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update Sale</button>
    </form>
</div>
@endsection
