<?php

$aiResponse = "Halo! Vion di sini. Jika yang Anda maksud adalah Zoom Workplace Pro (ID: PAR1-PRO-BASE-NH1Y), lisensi ini dirancang...";

$recommendedProducts = [];
if (preg_match_all('/\(?(?:ID_PRODUK|ID|SKU):\s*([a-zA-Z0-9\-\.]+)\)?/i', $aiResponse, $matches, PREG_SET_ORDER)) {
    foreach ($matches as $match) {
        $productIdentifier = trim($match[1], " ()");
        echo "MATCHED IDENTIFIER: " . $productIdentifier . "\n";
    }
}

$cleanResponse = preg_replace('/\(?(?:ID_PRODUK|ID|SKU):\s*[a-zA-Z0-9\-\.]+\)?/i', '', $aiResponse);
echo "CLEAN RESPONSE: " . $cleanResponse . "\n";
