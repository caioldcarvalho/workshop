<?php

namespace Caio\Workshop;

require 'vendor/autoload.php';

function getMiniatures(): array
{
    // Quantity detected automatically, just create as many sets of width/height
    // as you needed
    $miniatures = [
        [
            'width' => 360,
            'height' => 280
        ]
    ];
    return $miniatures;
}

function splitNamFromExtension(string $file_name): array
{
    $extension                  = pathinfo($file_name, PATHINFO_EXTENSION);
    $filename_without_extension = pathinfo($file_name, PATHINFO_FILENAME);

    return array($filename_without_extension, $extension);
}

function sanitizeName(string $name): string
{
    $str = strtolower($name);
    $str = htmlspecialchars($str);
    $str = preg_replace('/[áãàäâª]/u', 'a', $str);
    $str = preg_replace('/[éèêë]/u', 'e', $str);
    $str = preg_replace('/[íìîï]/u', 'i', $str);
    $str = preg_replace('/[óòôõöº°]/u', 'o', $str);
    $str = preg_replace('/[úùûü]/u', 'u', $str);
    $str = preg_replace('/[ç]/u', 'c', $str);
    $str = preg_replace('/[\/\\\;\:\(\)\*\&\%\$\#\@\!\=\+\.\,\?\>\<]/u', '', $str);
    $str = str_replace(' ', '-', $str);
    $str = str_replace('_', '-', $str);
    $str = str_replace('---', '-', $str);
    $str = str_replace('--', '-', $str);
    $str = html_entity_decode($str);
    return $str;
}

function saveMiniatures($miniatures, $name, $dir, $original_image, $original_width, $original_height): void
{
    // name[0] = file name without extenxion
    // name[1] = file extension

    foreach ($miniatures as $miniature) {
        $miniature_name = $dir . $name[0] . "-" . $miniature['width'] . "x" . $miniature['height'];

        $new_width  = $miniature['width'];
        $new_height = $miniature['height'];

        $new_image = imagecreatetruecolor($new_width, $new_height);

        // Resize the original image to the new dimensions
        imagecopyresampled($new_image, $original_image, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);

        // Output the resized image
        imagejpeg($new_image, $miniature_name . ".jpg", 90);

        imagedestroy($new_image);    
    }

}

function saveFile($file, $name, $miniatures = array()): void
{
    clearstatcache();
    $relative_path = getcwd() . "/" . "img/" . $name[0] . "/";
    if (!is_dir($relative_path)) {
        mkdir($relative_path, 0766, true);
    }

    $full_file_name = $relative_path . $name[0] . "." . $name[1];
    
    $original_image = match ($name[1]) {
        "png" => imagecreatefrompng($file["tmp_name"]),
        "jpg" => imagecreatefromjpeg($file["tmp_name"]),
        "jpeg" => imagecreatefromjpeg($file["tmp_name"]),
        "webp" => imagecreatefromwebp($file["tmp_name"])
    };
    
    $width  = imagesx($original_image);
    $height = imagesy($original_image);
    
    imagejpeg($original_image, $full_file_name);

    if (!empty($miniatures)) {
        saveMiniatures($miniatures, $name, $relative_path, $original_image, $width, $height);
    }

    imagedestroy($original_image);
}

function processSingleFile(array $file)
{
    // Please, define how many miniatures you need, and their dimensions.
    $miniatures = getMiniatures();
    $name       = splitNamFromExtension($file['name']);
    $name[0]    = sanitizeName($name[0]);
    saveFile($file, $name, $miniatures);
    echo "$name[0].$name[1]";
}

function processAllFiles(array $files): void
{
    foreach ($files as $file) {
        processSingleFile($file);
    }
}

// End of File
