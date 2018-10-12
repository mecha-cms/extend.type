<?php

$class = $src = $type = [];

// Search for the first `<audio>` tag…
if (strpos($content, '<audio ') !== false && strpos($content, ' src="') !== false) {
    if (preg_match('#<audio(?:\s[^<>]*?)?>#', $content, $m) && $audio = HTML::apart($m[0])) {
        if (isset($audio[2]['id'])) {
            $id = $audio[2]['id'];
        }
        if (isset($audio[2]['src'])) {
            $src[] = ($s = $audio[2]['src']);
            $x = Path::X($s);
            $type[] = isset($audio[2]['type']) ? $audio[2]['type'] : (isset($state['mime'][$x]) ? $state['mime'][$x] : 'audio/' . $x);
        }
        if (isset($audio[2]['class[]'])) {
            $class = (array) $audio[2]['class[]'];
        }
        if (preg_match_all('#<source(?:\s[^<>]*?)?>#', $content, $m)) {
            foreach ($m[0] as $v) {
                $source = HTML::apart($v);
                if (isset($source[2]['src'])) {
                    $src[] = ($s = $source[2]['src']);
                    $x = Path::X($s);
                    $type[] = isset($source[2]['type']) ? $source[2]['type'] : (isset($state['mime'][$x]) ? $state['mime'][$x] : 'audio/' . $x);
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
            if (strpos($s, 'data:audio/') !== 0 && strpos(',' . AUDIO_X . ',', ',' . $x . ',') === false) {
                $s = null; // Is not an audio/video URL, skip!
            }
            if (isset($s)) {
                $src[] = $s;
                $type[] = isset($state['mime'][$x]) ? $state['mime'][$x] : 'audio/' . $x;
            }
        }
    }
// Search for link text…
} else if (strpos($content, 'data:audio/') !== false || strpos($content, '://') !== false) {
    if (preg_match_all('#\b(?:https?://[^\s<>]+\.(?:' . str_replace(',', '|', AUDIO_X) . ')|data:audio/[^\s<>]+;base64,[^\s<>]+)\b#i', $content, $m)) {
        $src = $m[0];
        foreach ($src as $v) {
            $x = Path::X($v);
            $type[] = isset($state['mime'][$x]) ? $state['mime'][$x] : 'audio/' . $x;
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
$class = array_unique(concat(['audio', 'p'], $class));
$content  = '<div class="audio p" id="' . $id . '">';
$content .= "\n" . DENT . '<audio' . ($class ? ' class="' . implode(' ', $class) . '"' : "") . ' controls>';
foreach ($src as $k => $v) {
    $content .= "\n" . DENT . DENT . '<source src="' . $v . '"' . (isset($type[$k]) ? ' type="' . $type[$k] . '"' : "") . '>';
}
$content .= "\n" . DENT . DENT . '<p>Your browser doesn&#x2019;t support HTML5 audio. Here is a <a href="' . $src[0] . '">link to the audio</a> instead.</p>';
$content .= "\n" . DENT . "</audio>\n";
$content .= '</div>';