<?php

$alt = $src = $title = [];

// Search for the first `<img>` tag…
if (strpos($content, '<img ') !== false && strpos($content, ' src="') !== false) {
    if (preg_match_all('#<img(?:\s[^<>]*?)?>#', $content, $m)) {
        foreach ($m[0] as $v) {
            $img = HTML::apart($v);
            if (!empty($img[2]['alt'])) {
                $alt[] = $img[2]['alt'];
            }
            if (isset($img[2]['src'])) {
                $src[] = $img[2]['src'];
            }
            if (!empty($img[2]['title'])) {
                $title[] = $img[2]['title'];
            }
        }
    }
// Search for `<a>` tag(s)…
} else if (strpos($content, '<a ') !== false && strpos($content, ' href="') !== false) {
    if (preg_match_all('#<a(?:\s[^<>]*?)?>([\s\S]*?)</a>#', $content, $m)) {
        foreach ($m[0] as $v) {
            $a = HTML::apart($v);
            if (!empty($a[1])) {
                $title[] = $a[1];
            }
            $s = isset($a[2]['href']) ? $a[2]['href'] : null;
            if (strpos($s, 'data:image/') !== 0 && strpos(',' . IMAGE_X . ',', ',' . Path::X($s) . ',') === false) {
                $s = null; // Is not an image URL, skip!
            }
            if (isset($s)) {
                $src[] = $s;
            }
        }
    }
// Search for link text…
} else if (strpos($content, 'data:image/') !== false || strpos($content, '://') !== false) {
    if (preg_match_all('#\b(?:https?://[^\s<>]+\.(?:' . str_replace(',', '|', IMAGE_X) . ')|data:image/[^\s<>]+;base64,[^\s<>]+)\b#i', $content, $m)) {
        foreach ($m[0] as $v) {
            $src[] = $v;
        }
    }
}

// No image source(s), skip!
if (!$src) {
    return "";
}

// Wrap in a figure tag…
$content = '<div class="gallery p" id="' . $id . '">';
foreach (array_unique($src) as $k => $v) {
    $t = To::text(Path::N($v));
    $alt[$k] = isset($alt[$k]) && $alt[$k] ? $alt[$k] : $t;
    $content .= "\n" . DENT . '<figure class="image image-' . ($k + 1) . '">';
    $content .= "\n" . DENT . DENT . '<a href="' . $v . '" title="' . (isset($title[$k]) ? $title[$k] : $alt[$k]) . '" target="_blank"><img alt="' . $alt[$k] . '" src="' . To::thumbnail($v, $state['width'], $state['height']) . '"></a>';
    $content .= "\n" . DENT . "</figure>\n";
}
$content .= '</div>';