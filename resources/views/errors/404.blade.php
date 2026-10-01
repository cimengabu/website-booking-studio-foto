@extends('layouts.app')

@section('title', '404 — Frame Ini Kosong | Kilat Studio')

@section('content')
<div class="py-20 md:py-28 text-center">
    <div class="container-studio max-w-2xl">

        {{-- Contact frame representation --}}
        <div class="inline-block p-8 md:p-12 relative mb-8"
             style="background-color: var(--color-darkroom); border: 2px solid var(--color-ink); color: var(--color-paper);">

            {{-- Perforations --}}
            <div class="absolute top-2 left-3 right-3 flex justify-between">
                @for($i=0; $i<6; $i++)
                <div class="w-3 h-2 bg-stone-700"></div>
                @endfor
            </div>

            <div class="py-8">
                <span style="font-family: var(--font-mono); font-size: 0.875rem; color: var(--color-safelight);" class="block mb-2 font-bold">
                    [ 404 · FRAME KOSONG ]
                </span>
                <h1 style="font-family: var(--font-heading); font-size: clamp(2rem, 4vw, 3rem); font-weight: 900; letter-spacing: -0.03em; color: var(--color-paper);" class="mb-3">
                    Frame Ini Kosong.
                </h1>
                <p style="color: #90887e;" class="text-sm md:text-base max-w-md mx-auto leading-relaxed">
                    Halaman yang kamu cari tidak ditemukan atau filmnya sudah terbakar (*overexposed*).
                </p>
            </div>

            <div class="absolute bottom-2 left-3 right-3 flex justify-between">
                @for($i=0; $i<6; $i++)
                <div class="w-3 h-2 bg-stone-700"></div>
                @endfor
            </div>
        </div>

        <div>
            <a href="{{ route('home') }}" class="btn-primary inline-flex items-center gap-2">
                &larr; Kembali ke Beranda
            </a>
        </div>

    </div>
</div>
@endsection
