@extends('layouts.app')

@section('title', 'Kategori')
@section('page-title', 'Kategori')
@section('page-subtitle')
    <p class="text-sm text-gray-500">Kelola Kategori Menu</p>
@endsection

@section('content')
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Daftar Kategori</h2>
        </div>
        <a href="{{ route('categories.create') }}" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition">
            <i class="fas fa-plus mr-2"></i> Tambah Kategori Baru
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-gray-700">No</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-gray-700">Nama Kategori</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-gray-700">Slug</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-gray-700">Deskripsi</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-gray-700">Menu Items</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($categories as $index => $category)
                        <tr class="hover:bg-gray-50">
                            <td class="py-4 px-6 text-sm">{{ ($categories->currentPage() - 1) * $categories->perPage() + $index + 1 }}</td>
                            <td class="py-4 px-6 text-sm font-medium text-gray-900">{{ $category->name }}</td>
                            <td class="py-4 px-6 text-sm text-gray-600">{{ $category->slug }}</td>
                            <td class="py-4 px-6 text-sm text-gray-600">{{ Str::limit($category->description, 50) }}</td>
                            <td class="py-4 px-6 text-sm">
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">
                                    {{ $category->menus()->count() }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-sm">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('categories.edit', $category->id) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('categories.destroy', $category->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500">Belum ada kategori</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t">
            {{ $categories->links('pagination::tailwind') }}
        </div>
    </div>
@endsection
