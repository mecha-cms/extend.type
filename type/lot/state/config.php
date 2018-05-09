<?php

return [
    'audio' => [
        // List of hard-coded audio MIME type(s)
        'mime' => [
            'mp3' => 'audio/mpeg'
        ]
    ],
    'comic' => [
        'a' => [
            'previous' => true,
            'range' => 3,
            'next' => true
        ],
        'q' => 'page',
        'hash' => true
    ],
    'gallery' => [
        'width' => 400,
        'height' => null // auto
    ],
    'image' => [
        'width' => 600,
        'height' => null // auto
    ],
    'log' => [],
    'quote' => [],
    'video' => [
        // List of hard-coded video MIME type(s)
        'mime' => [
            'mov' => 'video/mp4',
            'ogv' => 'video/ogg'
        ]
    ]
];