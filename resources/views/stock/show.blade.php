@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Stock Entry #{{ $stock->id }}</h1>

    @include('partials.alerts')

    <div class="card">
        <div class="card-body">
            <p><strong>Item:</strong> {{ optional($stock->item)->name }}</p>
            <p><strong>Change:</strong> {{ $stock->change }}</p>
            <p><strong>Type:</strong> {{ $stock->type }}</p>
            <p><strong>Reference:</strong> {{ $stock->reference_id }}</p>
            <p><strong>Note:</strong> {{ $stock->note }}</p>
            <p><strong>Date:</strong> {{ $stock->created_at->format('Y-m-d H:i') }}</p>
        </div>
    </div>

    <a href="{{ route('stock.index') }}" class="btn btn-secondary mt-3">Back</a>
</div>
@endsection
