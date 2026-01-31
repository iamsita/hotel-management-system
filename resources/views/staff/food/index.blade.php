<!DOCTYPE html>
<html>

<head>
    <title>Food Menu Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Food Menu Management</h2>
            <a href="{{ route('food.create') }}" class="btn btn-primary">Add New Food Item</a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Available</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($foods as $food)
                        <tr>
                            <td>{{ $food->id }}</td>
                            <td>{{ $food->name }}</td>
                            <td><span class="badge bg-info">{{ ucfirst($food->category) }}</span></td>
                            <td>${{ number_format($food->price, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $food->available ? 'success' : 'danger' }}">
                                    {{ $food->available ? 'Available' : 'Unavailable' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('food.edit', $food) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('food.destroy', $food) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No food items found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $foods->links() }}
    </div>
</body>

</html>
