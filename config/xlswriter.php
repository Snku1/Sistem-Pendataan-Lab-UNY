<?php

return [
    'disk' => env('XLSWRITER_DISK', 'local'),

    'path' => env('XLSWRITER_PATH', 'exports'),

    'temp_path' => env('XLSWRITER_TEMP_PATH', storage_path('app/tmp/xlswriter')),

    'chunk_size' => (int) env('XLSWRITER_CHUNK_SIZE', 1000),

    'queue' => env('XLSWRITER_QUEUE', 'exports'),

    'default_sheet_name' => env('XLSWRITER_DEFAULT_SHEET_NAME', 'Sheet1'),

    'auto_filter' => env('XLSWRITER_AUTO_FILTER', true),

    'freeze_header' => env('XLSWRITER_FREEZE_HEADER', true),

    'use_zip64' => env('XLSWRITER_USE_ZIP64', true),

    'max_rows_per_sheet' => (int) env('XLSWRITER_MAX_ROWS_PER_SHEET', 1048576),

    'file_ttl_days' => (int) env('XLSWRITER_FILE_TTL_DAYS', 7),

    'track_tasks' => env('XLSWRITER_TRACK_TASKS', false),
];
