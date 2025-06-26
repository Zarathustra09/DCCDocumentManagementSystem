@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Folders</h5>
                        @can('create folders')
                            <a href="{{ route('folders.create') }}" class="btn btn-primary">
                                <i class="bx bx-folder-plus"></i> Create New Folder
                            </a>
                        @endcan
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if($folders->isEmpty())
                            <div class="text-center py-5">
                                <i class="bx bx-folder-open bx-lg text-muted mb-3"></i>
                                <p class="text-muted">You don't have any folders yet.</p>
                                @can('create folders')
                                    <a href="{{ route('folders.create') }}" class="btn btn-sm btn-primary">
                                        <i class="bx bx-folder-plus"></i> Create your first folder
                                    </a>
                                @endcan
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Subfolders</th>
                                        <th>Documents</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($folders as $folder)
                                        <tr>
                                            <td>
                                                <a href="{{ route('folders.show', $folder) }}">
                                                    <i class="bx bxs-folder text-warning"></i> {{ $folder->name }}
                                                </a>
                                            </td>
                                            <td>{{ Str::limit($folder->description, 30) }}</td>
                                            <td>{{ $folder->children->count() }}</td>
                                            <td>{{ $folder->documents->count() }}</td>
                                            <td>{{ $folder->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('folders.show', $folder) }}" class="btn btn-sm btn-info">
                                                        <i class="bx bx-show"></i>
                                                    </a>
                                                    @can('edit folders')
                                                        <a href="{{ route('folders.edit', $folder) }}" class="btn btn-sm btn-primary">
                                                            <i class="bx bx-edit"></i>
                                                        </a>
                                                    @endcan
                                                    @can('delete folders')
                                                        <form action="{{ route('folders.destroy', $folder) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this folder and all its contents?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">
                                                                <i class="bx bx-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
