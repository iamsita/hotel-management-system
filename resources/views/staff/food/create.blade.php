<!DOCTYPE html>
<html>

<head>
    <title>Add Food Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h2>Add New Food Item</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('food.store') }}" method="POST" class="card p-4">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Food Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                    id="name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-select @error('category') is-invalid @enderror" name="category" id="category"
                    required>
                    <option value="">-- Select Category --</option>
                    <option value="breakfast" {{ old('category') == 'breakfast' ? 'selected' : '' }}>Breakfast</option>
                    <option value="lunch" {{ old('category') == 'lunch' ? 'selected' : '' }}>Lunch</option>
                    <option value="dinner" {{ old('category') == 'dinner' ? 'selected' : '' }}>Dinner</option>
                    <option value="beverages" {{ old('category') == 'beverages' ? 'selected' : '' }}>Beverages</option>
                    <option value="desserts" {{ old('category') == 'desserts' ? 'selected' : '' }}>Desserts</option>
                </select>
                @error('category')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" class="form-control @error('price') is-invalid @enderror" name="price"
                        id="price" step="0.01" value="{{ old('price') }}" required>
                </div>
                @error('price')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" name="description" id="description"
                    rows="3">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="image_url" class="form-label">Image URL</label>
                <input type="url" class="form-control @error('image_url') is-invalid @enderror" name="image_url"
                    id="image_url" value="{{ old('image_url') }}">
                @error('image_url')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="available" class="form-label">Availability</label>
                <select class="form-select @error('available') is-invalid @enderror" name="available" id="available">
                    <option value="1" {{ old('available', 1) == 1 ? 'selected' : '' }}>Available</option>
                    <option value="0" {{ old('available') == 0 ? 'selected' : '' }}>Unavailable</option>
                </select>
                @error('available')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Add Food Item</button>
                <a href="{{ route('food.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</body>

</html>
