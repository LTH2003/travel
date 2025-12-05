@extends('admin.layouts.app')

@section('title', 'Edit Hotel')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Edit Hotel</h1>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.hotels.update', $hotel) }}" method="POST" class="bg-white shadow rounded-lg p-6">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="name" class="block text-gray-700 font-semibold mb-2">Hotel Name *</label>
            <input type="text" name="name" id="name" value="{{ old('name', $hotel->name) }}" class="w-full border rounded px-3 py-2 focus:outline-none focus:border-blue-500" required>
        </div>

        <div class="mb-4">
            <label for="location" class="block text-gray-700 font-semibold mb-2">Location *</label>
            <input type="text" name="location" id="location" value="{{ old('location', $hotel->location) }}" class="w-full border rounded px-3 py-2 focus:outline-none focus:border-blue-500" required>
        </div>

        <div class="mb-4">
            <label for="description" class="block text-gray-700 font-semibold mb-2">Description</label>
            <textarea name="description" id="description" rows="4" class="w-full border rounded px-3 py-2 focus:outline-none focus:border-blue-500">{{ old('description', $hotel->description) }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label for="price" class="block text-gray-700 font-semibold mb-2">Price *</label>
                <input type="number" name="price" id="price" value="{{ old('price', $hotel->price) }}" step="0.01" class="w-full border rounded px-3 py-2 focus:outline-none focus:border-blue-500" required>
            </div>
            <div>
                <label for="original_price" class="block text-gray-700 font-semibold mb-2">Original Price</label>
                <input type="number" name="original_price" id="original_price" value="{{ old('original_price', $hotel->original_price) }}" step="0.01" class="w-full border rounded px-3 py-2 focus:outline-none focus:border-blue-500">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label for="rating" class="block text-gray-700 font-semibold mb-2">Rating</label>
                <input type="number" name="rating" id="rating" value="{{ old('rating', $hotel->rating) }}" step="0.1" min="0" max="5" class="w-full border rounded px-3 py-2 focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label for="reviews" class="block text-gray-700 font-semibold mb-2">Number of Reviews</label>
                <input type="number" name="reviews" id="reviews" value="{{ old('reviews', $hotel->reviews) }}" class="w-full border rounded px-3 py-2 focus:outline-none focus:border-blue-500">
            </div>
        </div>

        <div class="mb-4">
            <label for="image" class="block text-gray-700 font-semibold mb-2">Image URL</label>
            <input type="text" name="image" id="image" value="{{ old('image', $hotel->image) }}" class="w-full border rounded px-3 py-2 focus:outline-none focus:border-blue-500">
        </div>

        <div class="mb-4">
            <label for="check_in" class="block text-gray-700 font-semibold mb-2">Check-in Time</label>
            <input type="text" name="check_in" id="check_in" value="{{ old('check_in', $hotel->check_in) }}" placeholder="e.g., 2:00 PM" class="w-full border rounded px-3 py-2 focus:outline-none focus:border-blue-500">
        </div>

        <div class="mb-4">
            <label for="check_out" class="block text-gray-700 font-semibold mb-2">Check-out Time</label>
            <input type="text" name="check_out" id="check_out" value="{{ old('check_out', $hotel->check_out) }}" placeholder="e.g., 11:00 AM" class="w-full border rounded px-3 py-2 focus:outline-none focus:border-blue-500">
        </div>

        <div class="mb-4">
            <label for="cancellation" class="block text-gray-700 font-semibold mb-2">Cancellation Policy</label>
            <textarea name="cancellation" id="cancellation" rows="3" class="w-full border rounded px-3 py-2 focus:outline-none focus:border-blue-500">{{ old('cancellation', $hotel->cancellation) }}</textarea>
        </div>

        <div class="mb-6">
            <label for="children" class="block text-gray-700 font-semibold mb-2">Children Policy</label>
            <textarea name="children" id="children" rows="3" class="w-full border rounded px-3 py-2 focus:outline-none focus:border-blue-500">{{ old('children', $hotel->children) }}</textarea>
        </div>

        <div class="flex gap-4">
            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">
                Update Hotel
            </button>
            <a href="{{ route('admin.hotels.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
