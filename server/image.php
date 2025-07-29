<?php
// Image handling utility functions

function imageUpload($file_name = 'image') {
    $image_data = null;
    $image_type = null;
    
    if (isset($_FILES[$file_name]) && $_FILES[$file_name]['error'] === 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES[$file_name]['type'];
        $file_size = $_FILES[$file_name]['size'];
        
        if (!in_array($file_type, $allowed_types)) {
            return [
                'error' => "Only JPG, PNG, and GIF images are allowed.",
                'image_data' => null,
                'image_type' => null
            ];
        } elseif ($file_size > (5 * 1024 * 1024)) {
            return [
                'error' => "Image must be smaller than 5MB.",
                'image_data' => null,
                'image_type' => null
            ];
        } else {
            $image_data = file_get_contents($_FILES[$file_name]['tmp_name']);
            $image_type = $file_type;
        }
    }

    return [
        'error' => null,
        'image_data' => $image_data,
        'image_type' => $image_type,
    ];
}

function displayImage($image_data, $image_type) {
    if ($image_data && $image_type) {
        $base64 = base64_encode($image_data);
        return "data:$image_type;base64,$base64";
    }
    return "https://placehold.co/120x120";
}