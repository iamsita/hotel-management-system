@extends('layout')
@section('title', 'Food Menu')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>Food Menu</h4>
    <a href="{{ route('admin.foods.create') }}" class="btn btn-primary" style="background:#1a3263;border:none">Add Item</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr><th>Name</th><th>Category</th><th>Price</th><th>Available</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @foreach($foods as $food)
                <tr>
                    <td>{{ $food->name }}</td>
                    <td>{{ ucfirst($food->category) }}</td>
                    <td>${{ number_format($food->price, 2) }}</td>
                    <td><span class="badge bg-{{ $food->available ? 'success' : 'danger' }}">{{ $food->available ? 'Yes' : 'No' }}</span></td>
                    <td>
                        <a href="{{ route('admin.foods.edit', $food) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('admin.foods.destroy', $food) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
