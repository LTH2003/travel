@extends('admin.layouts.app')

@section('title', 'Edit Room')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('admin.hotels.rooms.index', $hotel) }}" class="text-blue-500 hover:underline">&larr; Back to Rooms</a>
    </div>

    <h1 class="text-3xl font-bold text-gray-900 mb-6">Edit Room</h1>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.hotels.rooms.update', [$hotel, $room]) }}" method="POST" class="bg-white shadow rounded-lg p-6">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="name" class="block text-gray-700 font-semibold mb-2">Room Name *</label>
            <input type="text" name="name" id="name" value="{{ old('name', $room->name) }}" class="w-full border rounded px-3 py-2 focus:outline-none focus:border-blue-500" required>
        </div>

        <div class="mb-4">
            <label for="price" class="block text-gray-700 font-semibold mb-2">Price per Night *</label>
            <input type="number" name="price" id="price" value="{{ old('price', $room->price) }}" step="0.01" class="w-full border rounded px-3 py-2 focus:outline-none focus:border-blue-500" required>
        </div>

        <div class="mb-4">
            <label for="capacity" class="block text-gray-700 font-semibold mb-2">Capacity (Number of Guests) *</label>
            <input type="number" name="capacity" id="capacity" value="{{ old('capacity', $room->capacity) }}" min="1" class="w-full border rounded px-3 py-2 focus:outline-none focus:border-blue-500" required>
        </div>

        <div class="mb-4">
            <label for="bed_type" class="block text-gray-700 font-semibold mb-2">Bed Type</label>
            <select name="bed_type" id="bed_type" class="w-full border rounded px-3 py-2 focus:outline-none focus:border-blue-500">
                <option value="">Select a bed type</option>
                <option value="Single" {{ old('bed_type', $room->bed_type) === 'Single' ? 'selected' : '' }}>Single</option>
                <option value="Double" {{ old('bed_type', $room->bed_type) === 'Double' ? 'selected' : '' }}>Double</option>
                <option value="Queen" {{ old('bed_type', $room->bed_type) === 'Queen' ? 'selected' : '' }}>Queen</option>
                <option value="King" {{ old('bed_type', $room->bed_type) === 'King' ? 'selected' : '' }}>King</option>
                <option value="Twin" {{ old('bed_type', $room->bed_type) === 'Twin' ? 'selected' : '' }}>Twin</option>
                <option value="Multi-bed" {{ old('bed_type', $room->bed_type) === 'Multi-bed' ? 'selected' : '' }}>Multi-bed</option>
            </select>
        </div>

        <div class="mb-4">
            <label for="bathroom" class="block text-gray-700 font-semibold mb-2">Bathroom Type</label>
            <input type="text" name="bathroom" id="bathroom" value="{{ old('bathroom', $room->bathroom) }}" class="w-full border rounded px-3 py-2 focus:outline-none focus:border-blue-500">
        </div>

        <div class="mb-4">
            <label for="area" class="block text-gray-700 font-semibold mb-2">Room Area (m²)</label>
            <input type="number" name="area" id="area" value="{{ old('area', $room->area) }}" class="w-full border rounded px-3 py-2 focus:outline-none focus:border-blue-500">
        </div>

        <div class="mb-4">
            <label for="amenities" class="block text-gray-700 font-semibold mb-2">Amenities</label>
            <textarea name="amenities" id="amenities" rows="3" class="w-full border rounded px-3 py-2 focus:outline-none focus:border-blue-500">{{ old('amenities', $room->amenities) }}</textarea>
        </div>

        <div class="mb-6">
            <label for="description" class="block text-gray-700 font-semibold mb-2">Description</label>
            <textarea name="description" id="description" rows="4" class="w-full border rounded px-3 py-2 focus:outline-none focus:border-blue-500">{{ old('description', $room->description) }}</textarea>
        </div>

        <div class="mb-4">
            <label for="image" class="block text-gray-700 font-semibold mb-2">Image URL</label>
            <input type="text" name="image" id="image" value="{{ old('image', $room->image) }}" class="w-full border rounded px-3 py-2 focus:outline-none focus:border-blue-500">
        </div>

        <div class="flex gap-4">
            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">
                Update Room
            </button>
            <a href="{{ route('admin.hotels.rooms.index', $hotel) }}" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
