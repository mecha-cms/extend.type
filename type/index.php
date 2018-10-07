<?php namespace fn;

function type($content, $lot = [], $that = null, $key = null) {
    // No `type` data has been set, skip!
    if (!$t = $that->get('type')) {
        return $content;
    // Filter does not exist, skip!
    } else if (!$type = \File::exist(__DIR__ . DS . 'lot' . DS . 'worker' . DS . ($t = \__c2f__($t)) . '.php')) {
        return $content;
    }
    extract(\Lot::get(null, [])); // Inherit global variable(s)…
    $content = \n(trim($content)); // Trim white-space(s) and normalize line-break…
    $id = $t . ':' . ($that->get('id') ?: time());
    $state = \Extend::state('type', $t);
    $f = $that->get('path') ?: X;
    $image = $that->get('image');
    require $type; // Require the filter…
    return str_replace("\n", N, $content);
}

\Hook::set('page.content', __NAMESPACE__ . '\type', 2.1);

$s = __DIR__ . DS . 'lot' . DS . 'asset' . DS . 'css' . DS;
\Asset::set([
    $s . 'audio.min.css',
    $s . 'comic.min.css',
    $s . 'gallery.min.css',
    $s . 'video.min.css'
]);