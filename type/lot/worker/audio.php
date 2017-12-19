<?php

$class = $src = $type = [];

// Search for the first `<audio>` tag…
if (strpos($content, '<audio ') !== false && strpos($content, ' src="') !== false) {
    if (preg_match('#<audio(?:\s[^<>]*?)?>#', $content, $m) && $audio = HTML::apart($m[0])) {
        if (isset($audio[2]['src'])) {
            $src[] = ($s = $audio[2]['src']);
            $type[] = isset($audio[2]['type']) ? $audio[2]['type'] : 'audio/' . Path::X($s);
        }
        if (isset($audio[2]['class[]'])) {
            $class = (array) $audio[2]['class[]'];
        }
        if (preg_match_all('#<source(?:\s[^<>]*?)?>#', $content, $m)) {
            foreach ($m[0] as $v) {
                $source = HTML::apart($v);
                if (isset($source[2]['src'])) {
                    $src[] = ($s = $source[2]['src']);
                    $type[] = isset($source[2]['type']) ? $source[2]['type'] : 'audio/' . Anemon::alter(Path::X($s), [
                        'mp3' => 'mpeg'
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
            if (strpos($s, 'data:audio/') !== 0 && strpos(',' . MEDIA_X . ',', ',' . $x . ',') === false) {
                $s = null; // Is not an audio/video URL, skip!
            }
            if (isset($s)) {
                $src[] = $s;
                $type[] = 'audio/' . Anemon::alter($x, [
                    'mp3' => 'mpeg'
                ]);
            }
        }
    }
// Search for link text…
} else if (strpos($content, 'data:audio/') !== false || strpos($content, '://') !== false) {
    if (preg_match_all('#\b(?:https?://[^\s<>]+\.(?:' . str_replace(',', '|', MEDIA_X) . ')|data:audio/[^\s<>]+;base64,[^\s<>]+)\b#i', $content, $m)) {
        $src = $m[0];
        foreach ($src as $v) {
            $type[] = 'audio/' . Anemon::alter(Path::X($v), [
                'mp3' => 'mpeg'
            ]);
        }
    }
}

// No audio source(s), skip!
if (!$src) {
    return "";
}

// Wrap in a `<audio>` tag…
$src = array_unique($src);
$type = array_unique($type);
$class = array_unique(array_merge(['audio', 'p'], $class));
$content  = '<section class="' . implode(' ', $class) . '">';
$content .= "\n" . DENT . '<audio' . ($image ? ' poster="' . $image . '"' : "") . ' controls>';
foreach ($src as $k => $v) {
    $content .= "\n" . DENT . DENT . '<source src="' . $v . '"' . (isset($type[$k]) ? ' type="' . $type[$k] . '"' : "") . '>';
}
$content .= "\n" . DENT . "</audio>\n";
$content .= '</section>';