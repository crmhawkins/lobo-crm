<?php

return [
    'temporary_file_upload' => [
        'rules' => 'file|mimes:png,jpg,pdf|max:102400', // (100MB max, and only pngs, jpegs, and pdfs.)
        'directory' => '/public',
    ],
];