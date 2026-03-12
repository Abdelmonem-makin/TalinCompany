@extends('layouts.app')

@section('content')
<div class="container">
    <h1>New Stock Entry</h1>

    @include('partials.alerts')

    <form action="{{ route('stock.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="item_id" class="form-label">Item</label>
            <select name="item_id" id="item_id" class="form-control" required>
                <option value="">Select item</option>
                @foreach($items as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="change" class="form-label">Change (positive to add, negative to remove)</label>
            <input type="number" step="1" name="change" id="change" class="form-control" value="0" required>
        </div>

        <div class="mb-3">
            <label for="type" class="form-label">Type</label>
            <select name="type" id="type" class="form-control">
                <option value="manual">Manual</option>
                <option value="purchase">Purchase</option>
                <option value="sale">Sale</option>
                <option value="adjustment">Adjustment</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="reference_id" class="form-label">Reference ID</label>
            <input type="text" name="reference_id" id="reference_id" class="form-control">
        </div>

        <div class="mb-3">
            <label for="note" class="form-label">Note</label>
            <textarea name="note" id="note" class="form-control"></textarea>
        </div>

        <button class="btn btn-primary">Create</button>
    </form>
</div>
@endsection
