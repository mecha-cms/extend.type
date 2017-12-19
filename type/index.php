<?php

function fn_type($content, $lot = [], $that = null, $key = null) {
    // No `type` data has been set, skip!
    if (!isset($lot['type'])) {
        return $content;
    // Filter does not exist, skip!
    } else if (!$type = File::exist(__DIR__ . DS . 'lot' . DS . 'worker' . DS . ($t = __c2f__($lot['type'])) . '.php')) {
        return $content;
    }
    // Trim white-space(s) and normalize line-break…
    $content = n(trim($content));
    // Require the filter…
    $state = Extend::state(__DIR__, $t);
    $f = isset($lot['path']) ? Path::F($lot['path']) : X;
    $image = $that->get('image');
    require $type;
    return str_replace("\n", N, $content);
}

Hook::set('page.content', 'fn_type', 2.1);

Asset::set(__DIR__ . DS . 'lot' . DS . 'asset' . DS . 'css' . DS . 'type.min.css');