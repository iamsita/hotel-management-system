@extends('layout')
@section('title', 'Edit Food Item')
@section('content')
<h4 class="mb-4">Edit: {{ $food->name }}</h4>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.foods.update', $food) }}">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $food->name }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select" required>
                        @foreach(['breakfast','lunch','dinner','snacks','beverages'] as $cat)
                            <option value="{{ $cat }}" {{ $food->category === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Price ($)</label>
                    <input type="number" name="price" class="form-control" value="{{ $food->price }}" step="0.01" min="0" required>
                </div>
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" name="available" class="form-check-input" {{ $food->available ? 'checked' : '' }}>
                <label class="form-check-label">Available</label>
            </div>
            <button type="submit" class="btn btn-primary" style="background:#1a3263;border:none">Update Item</button>
            <a href="{{ route('admin.foods.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
