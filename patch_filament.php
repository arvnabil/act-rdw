<?php
$file = 'vendor/filament/actions/src/ImportAction.php';
if (file_exists($file)) {
    $content = file_get_contents($file);
    $old = "->acceptedFileTypes(['text/csv', 'text/x-csv', 'application/csv', 'application/x-csv', 'text/comma-separated-values', 'text/x-comma-separated-values', 'text/plain', 'application/vnd.ms-excel'])";
    $new = "->acceptedFileTypes(['*'])";
    if (str_contains($content, $old)) {
        $content = str_replace($old, $new, $content);
        file_put_contents($file, $content);
        echo "SUCCESS: ImportAction patched.\n";
    } else {
        echo "ERROR: Target string not found. Maybe already patched or different version.\n";
    }
} else {
    echo "ERROR: File not found.\n";
}
