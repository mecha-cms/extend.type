<?php

$alt = $src = null;
$caption = $that->get('description');

// Search for the first `<img>` tag…
if (strpos($content, '<img ') !== false && strpos($content, ' src="') !== false) {
    if (preg_match('#<img(?:\s[^<>]*?)?>#', $content, $m) && $img = HTML::apart($m[0])) {
        if (!$caption && !empty($img[2]['alt'])) {
            $caption = $alt = $img[2]['alt'];
        }
        if (isset($img[2]['id'])) {
            $id = $img[2]['id'];
        }
        $src = isset($img[2]['src']) ? $img[2]['src'] : null;
    }
// Search for the first `<a>` tag…
} else if (strpos($content, '<a ') !== false && strpos($content, ' href="') !== false) {
    if (preg_match('#<a(?:\s[^<>]*?)?>([\s\S]*?)</a>#', $content, $m) && $a = HTML::apart($m[0])) {
        if (!$caption && !empty($a[1])) {
            $caption = $alt = $a[1];
        }
        $src = isset($a[2]['href']) ? $a[2]['href'] : null;
        if (strpos($src, 'data:image/') !== 0 && strpos(',' . IMAGE_X . ',', ',' . Path::X($src) . ',') === false) {
            $src = null; // Is not an image URL, skip!
        }
    }
// Search for link text…
} else if (strpos($content, 'data:image/') !== false || strpos($content, '://') !== false) {
    if (preg_match('#\b(?:https?://[^\s<>]+\.(?:' . str_replace(',', '|', IMAGE_X) . ')|data:image/[^\s<>]+;base64,[^\s<>]+)\b#i', $content, $m)) {
        $src = $m[0];
    }
}

// No image source, skip!
if (!$src) {
    return "";
}

// Multi-line caption text will be treated as a paragraph…
if ($caption && strpos($caption, "\n") !== false) {
    // Convert double line break into paragraph tag(s), single line break into break tag(s)…
    $caption = str_replace(["\n\n", "\n", '</p>'], ['</p>' . DENT . DENT . '<p>', "<br>\n" . DENT . DENT . DENT, "</p>\n"], $content);
    $caption = "\n" . DENT . '<p>' . $caption . "</p>\n";
}

// Wrap in a figure tag…
$alt = $alt ?: To::text(Path::N($src));
$content  = '<figure class="image" id="' . $id . '">';
$content .= "\n" . DENT . '<img alt="' . $alt . '" src="' . To::thumbnail($src, $state['width'], $state['height']) . "\">\n";
$content .= $caption ? DENT . '<figcaption class="caption">' . $caption . "</figcaption>\n" : "";
$content .= '</figure>';