@extends('layouts.app')

@section('title', 'Tambah Menu')
@section('page-title', 'Tambah Menu Baru')
@section('page-subtitle')
    <p class="text-sm text-gray-500">Buat item menu baru</p>
@endsection

@section('content')
    <div class="max-w-2xl">
        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('menus.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-6">
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Menu *</label>
                    <input type="text" id="name" name="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 @error('name') border-red-500 @enderror" value="{{ old('name') }}" required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                    <textarea id="description" name="description" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="price" class="block text-sm font-semibold text-gray-700 mb-2">Harga (Rp) *</label>
                        <input type="number" id="price" name="price" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 @error('price') border-red-500 @enderror" value="{{ old('price') }}" required>
                        @error('price')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-2">Kategori *</label>
                        <select id="category_id" name="category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 @error('category_id') border-red-500 @enderror" required>
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Image Upload Section -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Gambar Menu</label>
                    
                    <!-- Drag & Drop Area -->
                    <div id="dropZone" class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:border-orange-500 hover:bg-orange-50 transition" style="min-height: 200px;">
                        <div id="dropContent">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3 block"></i>
                            <p class="text-gray-600 font-semibold mb-1">Seret gambar di sini atau klik untuk pilih</p>
                            <p class="text-gray-400 text-sm">Format: JPG, PNG, GIF (Maksimal 2MB)</p>
                        </div>
                        <div id="uploadProgress" style="display: none;" class="mt-4">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div id="progressBar" class="bg-orange-500 h-2 rounded-full transition-all" style="width: 0%"></div>
                            </div>
                            <p class="text-gray-600 text-sm mt-2">Sedang mengunggah...</p>
                        </div>
                        <input type="file" id="imageInput" name="image" accept="image/*" style="display: none;" />
                    </div>

                    <!-- Image Preview -->
                    <div id="imagePreview" style="display: none;" class="mt-4">
                        <img id="previewImage" src="" alt="Preview" class="max-w-xs h-auto rounded-lg shadow">
                        <button type="button" id="removeImage" class="mt-2 bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">
                            <i class="fas fa-trash mr-2"></i> Hapus Gambar
                        </button>
                    </div>

                    <!-- Hidden input to store image URL -->
                    <input type="hidden" id="imageUrl" name="image_url" value="{{ old('image_url') }}">

                    @error('image')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                    @error('image_url')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex space-x-4">
                    <button type="submit" class="bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600 transition">
                        <i class="fas fa-save mr-2"></i> Simpan Menu
                    </button>
                    <a href="{{ route('menus.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        const dropZone = document.getElementById('dropZone');
        const imageInput = document.getElementById('imageInput');
        const imagePreview = document.getElementById('imagePreview');
        const previewImage = document.getElementById('previewImage');
        const imageUrl = document.getElementById('imageUrl');
        const dropContent = document.getElementById('dropContent');
        const uploadProgress = document.getElementById('uploadProgress');
        const progressBar = document.getElementById('progressBar');
        const removeImageBtn = document.getElementById('removeImage');

        // Click to upload
        dropZone.addEventListener('click', () => imageInput.click());

        // Drag & drop
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('border-orange-500', 'bg-orange-50');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('border-orange-500', 'bg-orange-50');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-orange-500', 'bg-orange-50');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleImageUpload(files[0]);
            }
        });

        // File input change
        imageInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleImageUpload(e.target.files[0]);
            }
        });

        // Handle image upload
        function handleImageUpload(file) {
            if (!file.type.startsWith('image/')) {
                alert('Harap pilih file gambar');
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file terlalu besar (maksimal 2MB)');
                return;
            }

            // Show preview immediately
            const reader = new FileReader();
            reader.onload = (e) => {
                previewImage.src = e.target.result;
                imagePreview.style.display = 'block';
                dropContent.style.display = 'none';
            };
            reader.readAsDataURL(file);

            // Upload to server
            const formData = new FormData();
            formData.append('image', file);

            dropContent.style.display = 'none';
            uploadProgress.style.display = 'block';

            fetch('{{ route('menus.upload-image') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                },
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    imageUrl.value = data.url;
                    uploadProgress.style.display = 'none';
                    progressBar.style.width = '100%';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal mengunggah gambar');
                uploadProgress.style.display = 'none';
                dropContent.style.display = 'block';
                imagePreview.style.display = 'none';
            });
        }

        // Remove image
        removeImageBtn.addEventListener('click', () => {
            imageUrl.value = '';
            imagePreview.style.display = 'none';
            dropContent.style.display = 'block';
            imageInput.value = '';
            progressBar.style.width = '0%';
        });

        // Add CSRF meta tag if not exists
        if (!document.querySelector('meta[name="csrf-token"]')) {
            const meta = document.createElement('meta');
            meta.name = 'csrf-token';
            meta.content = '{{ csrf_token() }}';
            document.head.appendChild(meta);
        }
    </script>
    @endpush
@endsection
