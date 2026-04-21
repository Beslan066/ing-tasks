<?php

if (!function_exists('getBackgroundImages')) {
    function getBackgroundImages()
    {
        $images = [];
        $path = public_path('images/fones');

        if (is_dir($path)) {
            $files = scandir($path);
            foreach ($files as $file) {
                if (in_array(pathinfo($file, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $images[] = asset('images/fones/' . $file);
                }
            }
        }

        return $images;
    }
}
