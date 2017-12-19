<?php

$class = $src = $type = [];

// Search for the first `<video>` tag…
if (strpos($content, '<video ') !== false && strpos($content, ' src="') !== false) {
    if (preg_match('#<video(?:\s[^<>]*?)?>#', $content, $m) && $video = HTML::apart($m[0])) {
        if (isset($video[2]['poster'])) {
            $image = $video[2]['poster'];
        }
        if (isset($video[2]['class[]'])) {
            $class = (array) $video[2]['class[]'];
        }
        if (isset($video[2]['src'])) {
            $src[] = ($s = $video[2]['src']);
            $type[] = isset($video[2]['type']) ? $video[2]['type'] : 'video/' . Anemon::alter(Path::X($s), [
                'ogv' => 'ogg'
            ]);
        }
        if (preg_match_all('#<source(?:\s[^<>]*?)?>#', $content, $m)) {
            foreach ($m[0] as $v) {
                $source = HTML::apart($v);
                if (isset($source[2]['src'])) {
                    $src[] = ($s = $source[2]['src']);
                    $type[] = isset($source[2]['type']) ? $source[2]['type'] : 'video/' . Anemon::alter(Path::X($s), [
                        'ogv' => 'ogg'
                    ]);
                }
            }
        }
    }
// Search for `<a>` tag(s)…
} else if (strpos($content, '<a ') !== false && strpos($content, ' href="') !== false) {
    if (preg_match_all('#<a(?:\s[^<>]*?)?>[\s\S]*?</a>#', $content, $m)) {
        foreach ($m[0] as $v) {
            $a = HTML::apart($v);
            $s = isset($a[2]['href']) ? $a[2]['href'] : null;
            $x = Path::X($s);
            if (strpos($s, 'data:video/') !== 0 && strpos(',' . MEDIA_X . ',', ',' . $x . ',') === false) {
                $s = null; // Is not an audio/video URL, skip!
            }
            if (isset($s)) {
                $src[] = $s;
                $type[] = 'video/' . Anemon::alter($x, [
                    'ogv' => 'ogg'
                ]);
            }
        }
    }
// Search for link text…
} else if (strpos($content, 'data:video/') !== false || strpos($content, '://') !== false) {
    if (preg_match_all('#\b(?:https?://[^\s<>]+\.(?:' . str_replace(',', '|', MEDIA_X) . ')|data:video/[^\s<>]+;base64,[^\s<>]+)\b#i', $content, $m)) {
        $src = $m[0];
        foreach ($src as $v) {
            $type[] = 'video/' . Anemon::alter(Path::X($v), [
                'ogv' => 'ogg'
            ]);
        }
    }
}

// No video source(s), skip!
if (!$src) {
    return "";
}

// Wrap in a `<video>` tag…
$src = array_unique($src);
$type = array_unique($type);
$class = array_unique(array_merge(['video', 'p'], $class));
$content  = '<section class="' . implode(' ', $class) . '">';
$content .= "\n" . DENT . '<video' . ($image ? ' poster="' . $image . '"' : "") . ' controls>';
foreach ($src as $k => $v) {
    $content .= "\n" . DENT . DENT . '<source src="' . $v . '"' . (isset($type[$k]) ? ' type="' . $type[$k] . '"' : "") . '>';
}
$content .= "\n" . DENT . "</video>\n";
$content .= '</section>';