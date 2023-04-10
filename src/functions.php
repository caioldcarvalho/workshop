<?php

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

function save_miniatures($miniatures)
{
    foreach ($miniatures as $miniature) {
        echo $miniature['width'];
        echo $miniature['height'];
    }
}

function save_file($file, $name, $img_dir, $miniatures = array()): void
{
    $current_dir = $img_dir . $name[0];
    if (!is_dir($current_dir)) {
        mkdir($current_dir, 0777, true);
    }

    if (!empty($miniatures)) {
        save_miniatures($miniatures);
    }


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