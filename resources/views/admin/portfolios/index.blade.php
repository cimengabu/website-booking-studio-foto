@extends('layouts.admin')

@section('page-title', 'Konten Website & Portofolio')

@section('content')
<div class="space-y-8" x-data="{
    addModal: false,
    selectedCategory: 'all',
    uploadMode: 'url'
}">
    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-heading font-black text-white flex items-center gap-2.5">
                <i class="fa-solid fa-images text-[#E8451E]"></i>
                Konten Website & Galeri Portofolio
            </h2>
            <p class="text-xs text-gray-400 font-mono mt-1">Kelola foto showcase portofolio studio, kategori galeri, serta foto unggulan di beranda website.</p>
        </div>

        <button @click="addModal = true" class="px-5 py-2.5 rounded-xl bg-[#E8451E] hover:bg-[#d03a16] text-white font-bold text-xs shadow-lg transition-all flex items-center gap-2 self-start sm:self-auto">
            <i class="fa-solid fa-cloud-arrow-up text-xs"></i> Tambah Foto Galeri
        </button>
    </div>

    <!-- Category Filters -->
    <div class="flex flex-wrap gap-2 border-b border-[#2D2B26] pb-3">
        <button @click="selectedCategory = 'all'" :class="selectedCategory === 'all' ? 'bg-[#E8451E] text-white' : 'bg-[#1E1D1A] text-gray-400 hover:text-white border border-[#2D2B26]'" class="px-4 py-1.5 rounded-xl text-xs font-mono font-bold transition-all">
            Semua ({{ $portfolios->total() }})
        </button>
        @foreach($categories as $cat)
            <button @click="selectedCategory = '{{ $cat }}'" :class="selectedCategory === '{{ $cat }}' ? 'bg-[#E8451E] text-white' : 'bg-[#1E1D1A] text-gray-400 hover:text-white border border-[#2D2B26]'" class="px-4 py-1.5 rounded-xl text-xs font-mono font-bold capitalize transition-all">
                {{ $cat }}
            </button>
        @endforeach
    </div>

    <!-- Portfolio Gallery Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
        @forelse($portfolios as $item)
            <div x-show="selectedCategory === 'all' || selectedCategory === '{{ $item->category }}'" class="group bg-[#1E1D1A] border border-[#2D2B26] rounded-2xl overflow-hidden shadow-lg hover:border-[#E8451E]/50 transition-all flex flex-col justify-between">
                <div class="aspect-square relative overflow-hidden bg-gray-900">
                    <img src="{{ $item->image_path }}" alt="{{ $item->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-between p-3">
                        <div class="flex justify-between items-start">
                            @if($item->is_featured)
                                <span class="px-2 py-0.5 rounded-md bg-[#F2C94C] text-black text-[9px] font-mono font-black uppercase shadow">
                                    <i class="fa-solid fa-star"></i> Featured
                                </span>
                            @else
                                <span></span>
                            @endif

                            <form method="POST" action="{{ route('admin.portfolios.destroy', $item->id) }}" onsubmit="return confirm('Hapus foto ini dari galeri?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg bg-rose-600/90 hover:bg-rose-600 text-white text-[10px]" title="Hapus Foto">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>

                        <div>
                            <p class="text-white font-bold text-xs line-clamp-1">{{ $item->title }}</p>
                            <p class="text-gray-400 text-[10px] font-mono capitalize">{{ $item->category }} {{ $item->room ? '· ' . $item->room->name : '' }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-2.5 bg-[#141311] border-t border-[#2D2B26] flex items-center justify-between">
                    <span class="text-[10px] font-mono text-[#F2C94C] uppercase tracking-wider">{{ $item->category }}</span>
                    @if($item->is_featured)
                        <i class="fa-solid fa-star text-amber-400 text-[10px]" title="Tampil di Beranda"></i>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-16 bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl text-gray-500 font-mono text-xs">
                Belum ada foto portofolio yang diunggah.
            </div>
        @endforelse
    </div>

    @if($portfolios->hasPages())
        <div class="pt-4">
            {{ $portfolios->links() }}
        </div>
    @endif

    <!-- MODAL: Tambah Portofolio -->
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" @keydown.escape.window="addModal = false">
        <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl max-w-lg w-full p-6 space-y-5 shadow-2xl relative" @click.away="addModal = false">
            <div class="flex items-center justify-between border-b border-[#2D2B26] pb-4">
                <h3 class="font-heading font-bold text-lg text-white">Tambah Foto Portofolio Baru</h3>
                <button @click="addModal = false" class="text-gray-500 hover:text-white p-1"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form method="POST" action="{{ route('admin.portfolios.store') }}" enctype="multipart/form-data" class="space-y-4 text-xs font-mono">
                @csrf
                <div>
                    <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Judul / Caption Foto</label>
                    <input type="text" name="title" required placeholder="Contoh: Graduation Session with Sarah" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Kategori Galeri</label>
                        <select name="category" required class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none capitalize">
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ ucfirst($cat) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Ruangan Terkait</label>
                        <select name="room_id" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                            <option value="">-- Umum / Tanpa Ruangan --</option>
                            @foreach($rooms as $r)
                                <option value="{{ $r->id }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Toggle URL / File Upload -->
                <div class="space-y-2 pt-1">
                    <div class="flex items-center gap-4 text-xs text-gray-400">
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="radio" name="upload_type" value="url" @change="uploadMode = 'url'" checked class="text-[#E8451E] focus:ring-0">
                            <span>Input Link URL</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="radio" name="upload_type" value="file" @change="uploadMode = 'file'" class="text-[#E8451E] focus:ring-0">
                            <span>Upload File Komputer</span>
                        </label>
                    </div>

                    <div x-show="uploadMode === 'url'">
                        <input type="url" name="image_url" placeholder="https://images.unsplash.com/..." class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                    </div>

                    <div x-show="uploadMode === 'file'">
                        <input type="file" name="image_file" accept="image/*" class="w-full bg-[#141311] border border-dashed border-[#2D2B26] text-gray-400 text-xs px-4 py-4 rounded-xl text-center cursor-pointer">
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" id="add_is_featured" name="is_featured" value="1" class="rounded border-[#2D2B26] text-[#E8451E] focus:ring-0">
                    <label for="add_is_featured" class="text-xs text-gray-300">Jadikan Foto Unggulan (Tampil di Hero / Beranda)</label>
                </div>

                <div class="pt-4 border-t border-[#2D2B26] flex justify-end gap-3">
                    <button type="button" @click="addModal = false" class="px-4 py-2.5 rounded-xl bg-[#141311] border border-[#2D2B26] text-gray-400 hover:text-white">Batal</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#E8451E] text-white font-bold hover:bg-[#d03a16]">Simpan Foto</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
