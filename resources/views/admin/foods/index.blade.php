@extends('layout')
@section('title', 'Food Menu')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Food Menu</h4>
        <a href="{{ route('admin.foods.create') }}" class="btn btn-primary" style="background:#1a3263;border:none">Add Item</a>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.foods.index') }}" class="row g-2 align-items-end">
                <div class="col-sm-3">
                    <label class="form-label small mb-1">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Food name"
                        value="{{ request('search') }}">
                </div>
                <div class="col-sm-2">
                    <label class="form-label small mb-1">Category</label>
                    <select name="category" class="form-select form-select-sm">
                        <option value="">All Categories</option>
                        @foreach (['breakfast', 'lunch', 'dinner', 'snacks', 'beverages'] as $cat)
                            <option value="{{ $cat }}" @selected(request('category') === $cat)>
                                {{ ucfirst($cat) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2">
                    <label class="form-label small mb-1">Available</label>
                    <select name="available" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="yes" @selected(request('available') === 'yes')>Yes</option>
                        <option value="no" @selected(request('available') === 'no')>No</option>
                    </select>
                </div>
                <div class="col-sm-2 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary w-100"
                        style="background:#1a3263;border:none">Filter</button>
                    @if (request()->hasAny(['search', 'category', 'available']))
                        <a href="{{ route('admin.foods.index') }}" class="btn btn-sm btn-outline-secondary w-100">Clear</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Available</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($foods as $food)
                        <tr>
                            <td>{{ $food->name }}</td>
                            <td>{{ ucfirst($food->category) }}</td>
                            <td>Rs. {{ number_format($food->price, 2) }}</td>
                            <td><span
                                    class="badge bg-{{ $food->available ? 'success' : 'danger' }}">{{ $food->available ? 'Yes' : 'No' }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.foods.edit', $food) }}"
                                    class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('admin.foods.destroy', $food) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Delete?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No food items found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if ($foods->hasPages())
                <div class="p-3">{{ $foods->links('pagination::bootstrap-5') }}</div>
            @endif
        </div>
    </div>
@endsection
