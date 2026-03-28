@extends('layout')
@section('title', 'Add Food Item')
@section('content')
<h4 class="mb-4">Add Food Item</h4>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.foods.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select" required>
                        @foreach(['breakfast','lunch','dinner','snacks','beverages'] as $cat)
                            <option value="{{ $cat }}">{{ ucfirst($cat) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Price (Rs.)</label>
                    <input type="number" name="price" class="form-control" value="{{ old('price') }}" step="0.01" min="0" required>
                </div>
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" name="available" class="form-check-input" checked>
                <label class="form-check-label">Available</label>
            </div>
            <button type="submit" class="btn btn-primary" style="background:#1a3263;border:none">Add Item</button>
            <a href="{{ route('admin.foods.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
