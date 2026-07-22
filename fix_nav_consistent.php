<?php
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('resources/views/pelanggan'));
$standardNav = <<<HTML
<div class="flex items-center gap-4 md:gap-10 text-[13px] font-bold text-gray-800">
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-yellow-600' : 'hover:text-yellow-600' }} transition">Dasboard</a>
                <a href="{{ route('home') }}#catalog" class="hover:text-yellow-600 transition">katalog</a>
                <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'text-yellow-600' : 'hover:text-yellow-600' }} transition">About</a>
                <a href="{{ route('pesanan.saya') }}" class="{{ request()->routeIs('pesanan.*') ? 'text-yellow-600' : 'hover:text-yellow-600' }} transition">Pesanan</a>
                <a href="{{ route('retur.saya') }}" class="{{ request()->routeIs('retur.*') ? 'text-yellow-600' : 'hover:text-yellow-600' }} transition">Retur</a>                                
            </div>
HTML;

foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        
        $newContent = preg_replace(
            '/<div class="flex items-center gap-4 md:gap-10 text-\[13px\] font-bold text-gray-800">.*?<\/div>/is',
            $standardNav,
            $content,
            1 // Only replace the first occurrence (the top nav menu)
        );
        
        if ($newContent !== null && $newContent !== $content) {
            file_put_contents($file->getPathname(), $newContent);
            echo "Updated: " . $file->getPathname() . "\n";
        }
    }
}
echo "Done\n";
