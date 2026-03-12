@extends('layouts.app')

@section('content')
<h1>Item: {{ $item->name }}</h1>
<ul class="list-unstyled">
    <li><strong>SKU:</strong> {{ $item->sku }}</li>
    <li><strong>Price:</strong> {{ $item->price }}</li>
    <li><strong>Stock:</strong> {{ $item->stock }}</li>
    <li><strong>Supplier:</strong> {{ optional($item->supplier)->name }}</li>
</ul>
<a href="{{ route('items.index') }}" class="btn btn-secondary">Back</a>
@endsection
