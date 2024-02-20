@extends('layouts/contentNavbarLayout')

@section('title', 'Create Note')

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Create Note</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('updateNote', ['id' => $note->id]) }}" method="POST">
                @csrf
                    @method('PUT')
                <div class="mb-3">
                    <label for="noteText" class="form-label">Note Text</label>
                    <textarea class="form-control" id="noteText" name="text" rows="3" required>{{ old('text') }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="dueDate" class="form-label">Due Date</label>
                    <input type="date" class="form-control" id="dueDate" name="date" value="{{ old('date') }}" required>
                </div>
                <button type="submit" class="btn btn-primary">Save Note</button>
            </form>
        </div>
    </div>
@endsection
