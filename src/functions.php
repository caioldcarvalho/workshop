<?php

namespace Caio\Workshop;

require 'vendor/autoload.php';

function get_miniatures(): array
{
    // Quantity detected automatically, just create as many sets of dimentions as needed.
    $miniatures = [
        [
            'width' => 360,
            'height' => 280
        ]
    ];
    return $miniatures;
}

function split_name_from_extension(string &$file_name): array
{
    $extension                  = pathinfo($file_name, PATHINFO_EXTENSION);
    $filename_without_extension = pathinfo($file_name, PATHINFO_FILENAME);

    return array($filename_without_extension, $extension);
}

function sanitize_name(string $name): string
{
    $str = strtolower($name);
    $str = htmlspecialchars($str);
    $str = preg_replace('/[áãàäâª]/u', 'a', $str);
    $str = preg_replace('/[éèêë]/u', 'e', $str);
    $str = preg_replace('/[íìîï]/u', 'i', $str);
    $str = preg_replace('/[óòôõö]/u', 'o', $str);
    $str = preg_replace('/[úùûü]/u', 'u', $str);
    $str = preg_replace('/[ç]/u', 'c', $str);
    $str = preg_replace('/[\/\\\;\:\(\)\*\&\%\$\#\@\!\=\+\.\,]/u', '', $str);
    $str = str_replace(' ', '-', $str);
    $str = html_entity_decode($str);
    return $str;
}

function save_miniatures($miniatures, $name, $dir, $original_image, $original_width, $original_height): void
{
    // name[0] = file name without extenxion
    // name[1] = file extension

    foreach ($miniatures as $miniature) {
        $miniature_name = $dir . $name[0] . $miniature['width'] . "x" . $miniature['height'];

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

function save_file($file, $name, $img_dir, $miniatures = array()): void
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
        save_miniatures($miniatures, $name, $relative_path, $original_image, $width, $height);
    }

    imagedestroy($original_image);
}

define("ROOT_DIR", "/opt/htdocs/workshop/");
define("IMG_DIR", ROOT_DIR . "img/");

function process_single_file(array $file)
{
    // Please, define how many miniatures you need, and their dimensions.
    $miniatures = get_miniatures();
    $name       = split_name_from_extension($file['name']);
    $name[0]    = sanitize_name($name[0]);
    save_file($file, $name, IMG_DIR, $miniatures);
    echo "$name[0].$name[1]";
}

function process_all_files(array $files): void
{
    foreach ($files as $file) {
        process_single_file($file);
    }
}