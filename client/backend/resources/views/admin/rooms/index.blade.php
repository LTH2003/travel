@extends('admin.layouts.app')

@section('title', 'Rooms Management')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.hotels.index') }}">Hotels</a></li>
                    <li class="breadcrumb-item active">{{ $hotel->name }}</li>
                </ol>
            </nav>
            <h1 class="h2 mb-0">
                <i class="bi bi-door-closed me-2"></i> Rooms Management
            </h1>
            <small class="text-muted d-block mt-1">{{ $hotel->name }} • {{ $hotel->location }}</small>
        </div>
        <a href="{{ route('admin.hotels.rooms.create', $hotel) }}" class="btn btn-success">
            <i class="bi bi-plus-circle me-2"></i> Add New Room
        </a>
    </div>

    <!-- Success Message -->
    @if($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            <strong>Success!</strong> {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Hotel Info Card -->
    <div class="row mb-4">
        <div class="col-lg-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase fw-bold mb-2">
                        <i class="bi bi-building text-primary me-2"></i> Hotel
                    </h6>
                    <h5 class="card-title mb-0">{{ $hotel->name }}</h5>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase fw-bold mb-2">
                        <i class="bi bi-door-closed text-success me-2"></i> Total Rooms
                    </h6>
                    <h5 class="card-title mb-0">{{ $rooms->total() }}</h5>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase fw-bold mb-2">
                        <i class="bi bi-star text-warning me-2"></i> Rating
                    </h6>
                    <h5 class="card-title mb-0">{{ $hotel->rating }} / 5.0</h5>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase fw-bold mb-2">
                        <i class="bi bi-geo-alt text-danger me-2"></i> Location
                    </h6>
                    <small class="text-muted">{{ Str::limit($hotel->location, 30) }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Rooms Table -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-light border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-list-ul me-2"></i> Room List
                </h5>
                <span class="badge bg-primary">{{ $rooms->count() }} showing</span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 15%;">
                            <i class="bi bi-door-closed me-2"></i> Name
                        </th>
                        <th style="width: 12%;">
                            <i class="bi bi-cash-coin me-2"></i> Price
                        </th>
                        <th style="width: 10%;">
                            <i class="bi bi-people me-2"></i> Capacity
                        </th>
                        <th style="width: 12%;">
                            <i class="bi bi-bed me-2"></i> Bed Type
                        </th>
                        <th style="width: 25%;">
                            <i class="bi bi-star me-2"></i> Amenities
                        </th>
                        <th style="width: 10%;">
                            <i class="bi bi-rulers me-2"></i> Area
                        </th>
                        <th style="width: 16%;" class="text-center">
                            <i class="bi bi-gear me-2"></i> Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rooms as $room)
                        <tr>
                            <td>
                                <strong>{{ $room->name }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-success">
                                    {{ number_format($room->price, 0, ',', '.') }} VNĐ
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $room->capacity }} <i class="bi bi-person-fill"></i>
                                </span>
                            </td>
                            <td>
                                @if($room->bed_type)
                                    <span class="badge bg-secondary">{{ $room->bed_type }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $amenities = $room->amenities;
                                    // Handle both array and JSON string formats
                                    if (is_string($amenities)) {
                                        $amenities = json_decode($amenities, true) ?? [];
                                    }
                                    if (!is_array($amenities)) {
                                        $amenities = [];
                                    }
                                @endphp
                                @if(count($amenities) > 0)
                                    <div class="d-flex gap-1 flex-wrap">
                                        @foreach(array_slice($amenities, 0, 3) as $amenity)
                                            <span class="badge bg-light text-dark border border-secondary" title="{{ $amenity }}">
                                                {{ Str::limit($amenity, 12) }}
                                            </span>
                                        @endforeach
                                        @if(count($amenities) > 3)
                                            <span class="badge bg-light text-dark border border-secondary" title="and {{ count($amenities) - 3 }} more">
                                                +{{ count($amenities) - 3 }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted small">No amenities</span>
                                @endif
                            </td>
                            <td>
                                @if($room->area)
                                    <strong>{{ number_format($room->area, 1) }} m²</strong>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.hotels.rooms.edit', [$hotel, $room]) }}" 
                                       class="btn btn-outline-warning" 
                                       title="Edit Room">
                                        <i class="bi bi-pencil me-1"></i> Edit
                                    </a>
                                    <button type="button" 
                                            class="btn btn-outline-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteModal{{ $room->id }}"
                                            title="Delete Room">
                                        <i class="bi bi-trash me-1"></i> Delete
                                    </button>
                                </div>

                                <!-- Delete Confirmation Modal -->
                                <div class="modal fade" id="deleteModal{{ $room->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-sm">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger bg-opacity-10 border-danger">
                                                <h6 class="modal-title">
                                                    <i class="bi bi-exclamation-triangle text-danger me-2"></i>
                                                    Delete Room
                                                </h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete <strong>{{ $room->name }}</strong>?
                                                This action cannot be undone.
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('admin.hotels.rooms.destroy', [$hotel, $room]) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="bi bi-trash me-1"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="text-center py-5">
                                    <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc; display: block; margin-bottom: 1rem;"></i>
                                    <h6 class="text-muted">No rooms added yet</h6>
                                    <p class="text-muted mb-3">Start by adding your first room to this hotel</p>
                                    <a href="{{ route('admin.hotels.rooms.create', $hotel) }}" class="btn btn-success btn-sm">
                                        <i class="bi bi-plus-circle me-2"></i> Add First Room
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endempty
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($rooms->hasPages())
            <div class="card-footer bg-light border-top">
                {{ $rooms->links() }}
            </div>
        @endif
    </div>
</div>

<style>
    .table-hover tbody tr:hover {
        background-color: rgba(102, 126, 234, 0.05);
        transition: background-color 0.2s;
    }

    .breadcrumb {
        background-color: transparent;
        padding: 0;
    }

    .badge {
        font-weight: 500;
        padding: 0.45rem 0.65rem;
    }

    .btn-group-sm .btn {
        padding: 0.35rem 0.6rem;
        font-size: 0.875rem;
    }

    .modal-header {
        border-bottom: 2px solid;
    }

    code {
        background-color: #f5f5f5;
        padding: 2px 6px;
        border-radius: 3px;
        color: #d63384;
    }
</style>
@endsection
