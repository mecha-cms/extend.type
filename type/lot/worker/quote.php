<?php

$cite = null;

// Already has HTML markup, add the required HTML class name to the block quote or skip!
if (strpos($content, '</p>') !== false) {
    if (strpos($content, '</blockquote>') !== false) {
        if (strpos($content, '<blockquote ') === false) {
            $content = str_replace('<blockquote>', '<blockquote class="quote" id="' . $id . '">', $content);
        } else {
            $content = preg_replace_callback('#<blockquote(?:\s[^<>]*?)?>#', function($m) use($id) {
                $quote = HTML::apart($m[0]);
                if (!isset($quote[2]['id'])) {
                    $quote[2]['id'] = $id;
                }
                if (isset($quote[2]['class[]'])) {
                    $quote[2]['class[]'] = array_merge(['quote'], $quote[2]['class[]']);
                    unset($quote[2]['class[]']);
                } else {
                    $quote[2]['class[]'] = ['quote'];
                }
                return HTML::unite(...$quote);
            }, $content);
        }
    } else {
        $content = '<blockquote class="quote" id="' . $id . '">' . $content . '</blockquote>';
    }
} else {
    // Has author…
    if (strpos($content, ":\n") > 0) {
        $s = explode(":\n", $content, 2);
        $cite = trim($s[0]);
        // Has source URL…
        if ($that->get('link')) {
            // Wrap the author name in an anchor tag. This is typically an external link,
            // so it is better to add `rel="nofollow" and `target="_blank"` attribute too!
            $cite = '<a href="' . $that->link . '" rel="nofollow" target="_blank">' . $cite . '</a>';
        }
        // unset($that->link);
        $content = trim($s[1]);
    }
    // Force wrap in double curly quote(s)…
    if (strpos($content, '"') === false) {
        $content = '&#x201C;' . $content . '&#x201D;';
    // Convert double straight quote(s) into double curly quote…
    } else if (strpos($content, '"') === 0) {
        $content = '&#x201C;' . trim($content, '"') . '&#x201D;';
    }
    // Convert single straight quote(s) into single curly quote…
    if (strpos($content, "'") !== false) {
        $content = preg_replace('#\B\'([^\']*?)\'#', '&#x2018;$1&#x2019;', $content);
        // The rest should be a closing single curly quote…
        $content = str_replace("'", '&#x2019;', $content);
    }
    // Convert double line break into paragraph tag(s), single line break into break tag(s)…
    $content = str_replace(["\n\n", "\n", '</p>'], ['</p>' . DENT . '<p>', "<br>\n" . DENT . DENT, "</p>\n"], $content);
    $content = '<p>' . $content . '</p>';
    // Insert author at the end of the quote text…
    if ($cite) {
        $content .= "\n" . DENT . '<p class"cite"><span>&#x2014;</span>' . $cite . '</p>';
    }
    // Wrap in a block quote tag…
    $content = "<blockquote class=\"quote\" id=\"" . $id . "\">\n" . DENT . $content . "\n</blockquote>";
}