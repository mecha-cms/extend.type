<?php

// No HTML tag(s) allowed in page content!
$content = To::text($content, [], true);

// Convert text link into clickable link, image URL text into view-able image…
if (strpos($content, '://') !== false || strpos($content, 'data:image/') !== false) {
    $content = preg_replace_callback('#\b(?:https?://\S+\b/*|data:image/\S+;base64,\S+)#ui', function($m) {
        // Maybe an image URL?
        if (strpos($m[0], 'data:') === 0 || strpos(',' . IMAGE_X . ',', ',' . Path::X($m[0]) . ',') !== false) {
            return '<img alt="" src="' . $m[0] . '">';
        }
        // This is typically an external link so it is better to add `rel="nofollow" and `target="_blank"` attribute too!
        return '<a href="' . $m[0] . '" rel="nofollow" target="_blank">' . $m[0] . '</a>';
    }, $content);
}

// Convert double line break into paragraph tag(s), single line break into break tag(s)…
$content = str_replace(["\n\n", "\n", '</p>'], ['</p><p>', "<br>\n" . DENT, "</p>\n"], $content);
$content = '<p>' . $content . '</p>';