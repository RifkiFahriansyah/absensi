<?php

// This file is the entry point for Vercel's serverless functions.

// Create required writable directories in /tmp for Vercel's read-only filesystem
foreach (['/tmp/views', '/tmp/cache', '/tmp/sessions', '/tmp/logs'] as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

require __DIR__ . '/../public/index.php';
