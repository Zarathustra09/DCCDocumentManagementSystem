@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Base Folder</h1>

<form action="{{ route('base-folder.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Base Folder Name</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Create</button>
        <a href="{{ route('folders.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
