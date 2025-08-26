<?php

return [
    'sitekey' => env('NOCAPTCHA_SITEKEY'),
    'secret'  => env('NOCAPTCHA_SECRET'),

    // IMPORTANT: use v3
    'version' => 'v3',

    // Optional: v3 score threshold (package validates it server-side)
    'score' => 0.5,
];
