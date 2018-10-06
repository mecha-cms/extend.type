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
$content = '<div class="comic p" id="' . $id . '">';
$src = array_unique($src);
$hash = !empty($state['hash']) ? '#' . $id : "";
if ($i = HTTP::get($state['q'], 1)) {
    $ii = $i - 1;
    if ($i > 0 && isset($src[$ii])) {
        $content .= "\n" . DENT . '<figure class="image image-' . $i . '">';
        $content .= "\n" . DENT . DENT . '<img alt="' . (isset($alt[$ii]) ? $alt[$ii] : "") . '" src="' . $src[$ii] . '">';
        if (isset($title[$ii])) {
            $content .= "\n" . DENT . DENT . '<figcaption>' . $title[$ii] . '</figcaption>';
        }
        $content .= "\n" . DENT . '</figure>';
        $content .= "\n" . DENT . '<p>';
        if (!empty($state['a']['previous'])) {
            if ($i > 0 && isset($src[$ii - 1])) {
                $content .= '<a class="a-previous" href="' . $url->clean . HTTP::query([$state['q'] => $i - 1]) . $hash . '" rel="prev">' . $language->previous . '</a>';
            } else {
                $content .= '<span class="a a-previous">' . $language->previous . '</span>';
            }
        }
        if (!empty($state['a']['range'])) {
            $d = $state['a']['range'] - 1;
            $k = count($src);
            if ($i - $d > 1) {
                $content .= ' <a class="a-step a-step:1" href="' . $url->clean . HTTP::query([$state['q'] => 1]) . $hash . '" rel="prev">1</a>';
                if ($i - $d > 2) {
                    $content .= ' <span class="s">&#x2026;</span>';
                }
            }
			      $begin = max(1, $i - $d);
			      $end = min($k, $i + $d);
            for ($j = $begin; $j <= $end; ++$j) {
                if ($j === $i) {
                    $content .= ' <span class="a a-step a-step:' . $j . '">' . $j . '</span>';
                } else {
                    $content .= ' <a class="a-step a-step:' . $j . '" href="' . $url->clean . HTTP::query([$state['q'] => $j]) . $hash . '" rel="' . ($j < $i ? 'prev' : 'next') . '">' . $j . '</a>';
                }
            }
            if ($i + $d < $k) {
                if ($i + $d < $k - 1) {
                    $content .= ' <span class="s">&#x2026;</span>';
                }
                $content .= ' <a class="a-step a-step:' . $k . '" href="' . $url->clean . HTTP::query([$state['q'] => $k]) . $hash . '" rel="next">' . $k . '</a>';
            }
        }
        if (!empty($state['a']['next'])) {
            if (isset($src[$i])) {
                $content .= ' <a class="a-next" href="' . $url->clean . HTTP::query([$state['q'] => $i + 1]) . $hash . '" rel="next">' . $language->next . '</a>';
            } else {
                $content .= ' <span class="a a-next">' . $language->next . '</span>';
            }
        }
        $content .= "</p>\n";
    } else {
        $content .= "\n" . DENT . '<figure class="image image-0">' . To::sentence($language->_finded) . '</figure>';
    }
}
$content .= '</div>';