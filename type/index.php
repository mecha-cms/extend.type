<?php

function fn_type($content, $lot = [], $that = null, $key = null) {
    // No `type` data has been set, skip!
    if (!isset($lot['type'])) {
        return $content;
    // Filter does not exist, skip!
    } else if (!$type = File::exist(__DIR__ . DS . 'lot' . DS . 'worker' . DS . ($t = __c2f__($lot['type'])) . '.php')) {
        return $content;
    }
    $content = n(trim($content)); // Trim white-space(s) and normalize line-break…
    $id = $t . ':' . (isset($lot['id']) ? $lot['id'] : time());
    $state = Extend::state(__DIR__, $t);
    $f = isset($lot['path']) ? Path::F($lot['path']) : X;
    $image = isset($lot['image']) ? $lot['image'] : null;
    require $type; // Require the filter…
    return str_replace("\n", N, $content);
}

Hook::set('page.content', 'fn_type', 2.1);

$s = __DIR__ . DS . 'lot' . DS . 'asset' . DS . 'css' . DS;
Asset::set([
    $s . 'audio.min.css',
    $s . 'gallery.min.css',
    $s . 'video.min.css'
]);