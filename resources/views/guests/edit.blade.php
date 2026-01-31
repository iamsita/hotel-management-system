@extends('layout')

@section('title', 'Edit Guest')

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4"><i class="fas fa-user"></i> Edit Guest</h1>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('guests.update', $user) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="first_name" class="form-label">First Name *</label>
                                        <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                            id="first_name" name="first_name" required value="{{ $user->first_name }}">
                                        @error('first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="last_name" class="form-label">Last Name *</label>
                                        <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                            id="last_name" name="last_name" required value="{{ $user->last_name }}">
                                        @error('last_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email *</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            id="email" name="email" required value="{{ $user->email }}">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone</label>
                                        <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                            id="phone" name="phone" value="{{ $user->phone ?? '' }}">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="id_type" class="form-label">ID Type</label>
                                        <select class="form-select @error('id_type') is-invalid @enderror" id="id_type"
                                            name="id_type">
                                            <option value="">Select Type</option>
                                            <option value="passport" {{ $user->id_type === 'passport' ? 'selected' : '' }}>
                                                Passport</option>
                                            <option value="national_id"
                                                {{ $user->id_type === 'national_id' ? 'selected' : '' }}>National ID
                                            </option>
                                            <option value="driving_license"
                                                {{ $user->id_type === 'driving_license' ? 'selected' : '' }}>Driving
                                                License</option>
                                        </select>
                                        @error('id_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="id_number" class="form-label">ID Number</label>
                                        <input type="text" class="form-control @error('id_number') is-invalid @enderror"
                                            id="id_number" name="id_number" value="{{ $user->id_number ?? '' }}">
                                        @error('id_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror"
                                    id="address" name="address" value="{{ $user->address ?? '' }}">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="city" class="form-label">City</label>
                                        <input type="text" class="form-control @error('city') is-invalid @enderror"
                                            id="city" name="city" value="{{ $user->city ?? '' }}">
                                        @error('city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="country" class="form-label">Country</label>
                                        <input type="text" class="form-control @error('country') is-invalid @enderror"
                                            id="country" name="country" value="{{ $user->country ?? '' }}">
                                        @error('country')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="guest_type" class="form-label">Guest Type *</label>
                                <select class="form-select @error('guest_type') is-invalid @enderror" id="guest_type"
                                    name="guest_type" required>
                                    <option value="individual" {{ $user->guest_type === 'individual' ? 'selected' : '' }}>
                                        Individual</option>
                                    <option value="corporate" {{ $user->guest_type === 'corporate' ? 'selected' : '' }}>
                                        Corporate</option>
                                </select>
                                @error('guest_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Guest
                                </button>
                                <a href="{{ route('guests.show', $user) }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
