@extends('admin.layouts.app')

@section('title', 'Rooms Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('admin.hotels.index') }}" class="text-blue-500 hover:underline">&larr; Back to Hotels</a>
    </div>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Rooms for {{ $hotel->name }}</h1>
            <p class="text-gray-600">Location: {{ $hotel->location }}</p>
        </div>
        <a href="{{ route('admin.hotels.rooms.create', $hotel) }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Add Room
        </a>
    </div>

    @if($message = Session::get('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ $message }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-2 text-left">Name</th>
                    <th class="border p-2 text-left">Price</th>
                    <th class="border p-2 text-left">Capacity</th>
                    <th class="border p-2 text-left">Bed Type</th>
                    <th class="border p-2 text-left">Amenities</th>
                    <th class="border p-2 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rooms as $room)
                    <tr class="hover:bg-gray-100">
                        <td class="border p-2">{{ $room->name }}</td>
                        <td class="border p-2">${{ number_format($room->price, 2) }}</td>
                        <td class="border p-2">{{ $room->capacity }} guests</td>
                        <td class="border p-2">{{ $room->bed_type ?? 'N/A' }}</td>
                        <td class="border p-2 text-sm">{{ \Str::limit($room->amenities, 50) }}</td>
                        <td class="border p-2 text-center">
                            <a href="{{ route('admin.hotels.rooms.edit', [$hotel, $room]) }}" class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 inline-block mr-2">
                                Edit
                            </a>
                            <form action="{{ route('admin.hotels.rooms.destroy', [$hotel, $room]) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($rooms->count() === 0)
        <div class="bg-gray-100 border border-gray-400 text-gray-700 px-4 py-3 rounded mt-4">
            No rooms added yet. <a href="{{ route('admin.hotels.rooms.create', $hotel) }}" class="text-blue-500 hover:underline">Add the first room</a>
        </div>
    @endif

    <div class="mt-4">
        {{ $rooms->links() }}
    </div>
</div>
@endsection
