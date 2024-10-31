<?php

return [
    'temporary_file_upload' => [
        'rules' => 'file|max:102400', // (100MB max, and only pngs, jpegs, and pdfs.)
        'directory' => '/public',
    ],
];
