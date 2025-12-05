@extends('admin.layouts.app')

@section('title', 'Hotels Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Hotels Management</h1>
        <a href="{{ route('admin.hotels.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Add Hotel
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
                    <th class="border p-2 text-left">Location</th>
                    <th class="border p-2 text-left">Price</th>
                    <th class="border p-2 text-left">Rating</th>
                    <th class="border p-2 text-left">Rooms</th>
                    <th class="border p-2 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hotels as $hotel)
                    <tr class="hover:bg-gray-100">
                        <td class="border p-2">{{ $hotel->name }}</td>
                        <td class="border p-2">{{ $hotel->location }}</td>
                        <td class="border p-2">${{ number_format($hotel->price, 2) }}</td>
                        <td class="border p-2">
                            @if($hotel->rating)
                                {{ $hotel->rating }}/5 ({{ $hotel->reviews ?? 0 }} reviews)
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="border p-2">
                            <a href="{{ route('admin.hotels.rooms.index', $hotel) }}" class="text-blue-500 hover:underline">
                                {{ $hotel->rooms->count() }} rooms
                            </a>
                        </td>
                        <td class="border p-2 text-center">
                            <a href="{{ route('admin.hotels.edit', $hotel) }}" class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 inline-block mr-2">
                                Edit
                            </a>
                            <form action="{{ route('admin.hotels.destroy', $hotel) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
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

    <div class="mt-4">
        {{ $hotels->links() }}
    </div>
</div>
@endsection
