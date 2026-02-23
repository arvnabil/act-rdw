<?php

use App\Models\Page;

$pages = Page::latest()->take(3)->get();

$sql = "";
foreach ($pages as $page) {
    $pageAttrs = $page->getAttributes();
    $pageColumns = array_keys($pageAttrs);
    $pageValues = array_map(function($v) {
        if ($v === null) return 'NULL';
        return "'" . addslashes($v) . "'";
    }, array_values($pageAttrs));

    $sql .= "-- Page: {$page->title}\n";
    $sql .= "INSERT INTO pages (" . implode(', ', $pageColumns) . ") VALUES (" . implode(', ', $pageValues) . ");\n\n";

    $sections = $page->sections()->orderBy('position')->get();
    if ($sections->count() > 0) {
        $sql .= "-- Sections for Page: {$page->title}\n";
        foreach ($sections as $section) {
            $sectAttrs = $section->getAttributes();
            $sectColumns = array_keys($sectAttrs);
            $sectValues = array_map(function($v) {
                if ($v === null) return 'NULL';
                return "'" . addslashes($v) . "'";
            }, array_values($sectAttrs));

            $sql .= "INSERT INTO page_sections (" . implode(', ', $sectColumns) . ") VALUES (" . implode(', ', $sectValues) . ");\n";
        }
        $sql .= "\n";
    }
}

file_put_contents('latest_pages.sql', $sql);
echo "SQL exported to latest_pages.sql\n";
