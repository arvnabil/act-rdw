<?php

function replace_content($path, $replacements, $append = false) {
    $content = file_get_contents($path);
    foreach ($replacements as $search => $replace) {
        $content = str_replace($search, $replace, $content);
    }
    if ($append) {
        $content .= $replacements;
    }
    file_put_contents($path, $content);
}

1;