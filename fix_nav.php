<?php
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('resources/views/pelanggan'));
foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        // Remove all current retur.saya links
        $content = preg_replace('/<a href="\{\{\s*route\(\'retur\.saya\'\)\s*\}\}".*?<\/a>/is', '', $content);
        // Remove literal `n
        $content = str_replace('`n', '', $content);
        // Append one retur.saya link after pesanan.saya
        $content = preg_replace(
            '/(<a href="\{\{\s*route\(\'pesanan\.saya\'\)\s*\}\}".*?<\/a>)/is',
            "$1\n                <a href=\"{{ route('retur.saya') }}\" class=\"hover:text-yellow-600 transition\">Retur</a>",
            $content
        );
        // Clean up any double blank lines that might have been created
        $content = preg_replace("/\n\s*\n\s*<a href=\"\{\{\s*route\(\'retur\.saya\'\)\s*\}\}\"/is", "\n                <a href=\"{{ route('retur.saya') }}\"", $content);
        
        file_put_contents($file->getPathname(), $content);
    }
}
echo "Done\n";
