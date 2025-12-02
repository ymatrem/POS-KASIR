@extends('layouts.app')

@section('title', 'Daftar Menu')
@section('page-title', 'Data Master')
@section('page-subtitle')
    <p class="text-sm text-gray-500">Kelola item menu Anda</p>
@endsection

@section('content')
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Item Menu</h2>
        </div>
        <a href="{{ route('menus.create') }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition">
            <i class="fas fa-plus mr-2"></i> Tambah Menu Baru
        </a>
    </div>

    <!-- Grid View of Menu Items -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($menus as $menu)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                <!-- Image Container -->
                <div class="relative bg-gray-200 h-48 overflow-hidden">
                    @if($menu->image_url)
                        <img src="{{ $menu->image_url }}" alt="{{ $menu->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gray-300">
                            <i class="fas fa-image text-gray-400 text-4xl"></i>
                        </div>
                    @endif
                </div>

                <!-- Content -->
                <div class="p-4">
                    <h3 class="font-semibold text-gray-800 mb-1 truncate">{{ $menu->name }}</h3>

                    <!-- Category Badge -->
                    <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-semibold mb-3">
                        {{ $menu->category->name ?? 'N/A' }}
                    </span>

                    <!-- Description -->
                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $menu->description ?? '-' }}</p>

                    <!-- Price -->
                    <div class="mb-3">
                        <p class="text-orange-600 font-bold text-lg">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                        <p class="text-gray-500 text-xs">Terjual: {{ $menu->sold_quantity }}</p>
                    </div>

                    <!-- Stock Status -->
                    <div class="mb-3 p-2 rounded-lg @if($menu->stock <= 0) bg-red-100 @elseif($menu->stock <= 5) bg-yellow-100 @else bg-green-100 @endif">
                        <p class="font-semibold @if($menu->stock <= 0) text-red-700 @elseif($menu->stock <= 5) text-yellow-700 @else text-green-700 @endif text-sm">
                            📦 Stok: {{ $menu->stock }}
                            @if($menu->stock <= 0)
                                <span class="text-xs">⚠️ Habis</span>
                            @elseif($menu->stock <= 5)
                                <span class="text-xs">⚠️ Terbatas</span>
                            @endif
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <a href="{{ route('menus.edit', $menu->id) }}" class="flex-1 bg-blue-500 text-white px-3 py-2 rounded-lg hover:bg-blue-600 transition text-center text-sm">
                            <i class="fas fa-edit mr-1"></i> Ubah
                        </a>
                        <form action="{{ route('menus.destroy', $menu->id) }}" method="POST" style="flex: 1" onsubmit="return confirm('Yakin ingin hapus?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full bg-red-500 text-white px-3 py-2 rounded-lg hover:bg-red-600 transition text-sm">
                                <i class="fas fa-trash mr-1"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <i class="fas fa-inbox text-gray-300 text-6xl mb-4 block"></i>
                <p class="text-gray-500 text-lg">Belum ada menu</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $menus->links('pagination::tailwind') }}
    </div>
@endsection
