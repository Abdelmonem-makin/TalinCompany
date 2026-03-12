@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Stock Entry #{{ $stock->id }}</h1>

    @include('partials.alerts')

    <form action="{{ route('stock.update', $stock) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="item_id" class="form-label">Item</label>
            <select name="item_id" id="item_id" class="form-control" required>
                <option value="">Select item</option>
                @foreach($items as $id => $name)
                    <option value="{{ $id }}" {{ $stock->item_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="change" class="form-label">Change (positive to add, negative to remove)</label>
            <input type="number" step="1" name="change" id="change" class="form-control" value="{{ old('change', $stock->change) }}" required>
        </div>

        <div class="mb-3">
            <label for="type" class="form-label">Type</label>
            <select name="type" id="type" class="form-control">
                <option value="manual" {{ $stock->type == 'manual' ? 'selected' : '' }}>Manual</option>
                <option value="purchase" {{ $stock->type == 'purchase' ? 'selected' : '' }}>Purchase</option>
                <option value="sale" {{ $stock->type == 'sale' ? 'selected' : '' }}>Sale</option>
                <option value="adjustment" {{ $stock->type == 'adjustment' ? 'selected' : '' }}>Adjustment</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="reference_id" class="form-label">Reference ID</label>
            <input type="text" name="reference_id" id="reference_id" class="form-control" value="{{ old('reference_id', $stock->reference_id) }}">
        </div>

        <div class="mb-3">
            <label for="note" class="form-label">Note</label>
            <textarea name="note" id="note" class="form-control">{{ old('note', $stock->note) }}</textarea>
        </div>

        <button class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
