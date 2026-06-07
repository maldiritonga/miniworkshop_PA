<x-admin-layout>
    <x-slot name="title">{{ $title ?? 'Halaman Admin' }}</x-slot>

    <div class="rounded-[32px] bg-white p-8 shadow-sm">
        <h1 class="text-3xl font-bold text-gray-900">{{ $title ?? 'Halaman Admin' }}</h1>
        <p class="mt-4 text-gray-600">Halaman ini sementara ditampilkan untuk route <span class="font-semibold">{{ request()->route()->getName() }}</span>.</p>
        <p class="mt-6 text-sm text-gray-500">Silakan tambahkan konten halaman ini sesuai kebutuhan aplikasi.</p>
    </div>
</x-admin-layout>
