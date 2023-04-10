<?php

include 'functions.php';

function process_all_files(array $files): void
{
    foreach ($files as $file) {
        process_single_file($file);
    }
}

isset($_FILES) ? process_all_files($_FILES) : 'roi';

?>

<form action="form.php" method="post" enctype="multipart/form-data">
    <label for="file">Insert file</label>
    <input type="file" name="file" id="file">
    <button type="submit">Send</button>
</form>

<?php

// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);


// declare(strict_types=1);

// $dsn = 'mysql:host=localhost;dbname=your_database_name;charset=utf8mb4';
// $username = 'your_username';
// $password = 'your_password';

// // Create a PDO object
// $pdo = new PDO($dsn, $username, $password);

// function process_image(array $image_file): void
// {
//     if (is_image_file($image_file)) {
//         $miniatures = [
//             [
//             'width' => 150,
//             'height' => 150
//             ]
//         ];
//         $sanitized_file_name = sanitize_file_name($image_file['name']);
//         $file_path = "images/{$sanitized_file_name}.{$image_file['type']}";
//         generate_miniatures($image_file['tmp_name'], $miniatures);
//         save_to_directory($image_file, $sanitized_file_name);
//         // save_to_database($pdo, $sanitized_file_name, $file_path);
//     } else {
//         prompt_error('Invalid file type. Please select a png, jpg, or webp file.');
//     }
// }

// function is_image_file(array $image_file): bool
// {
//     $valid_types = ['image/png', 'image/jpeg', 'image/webp'];
//     return in_array($image_file['type'], $valid_types);
// }

// function sanitize_file_name(string $file_name): string
// {
//     $sanitized_name = preg_replace('/[^0-9a-zA-Z\-]+/', '-', $file_name);
//     $sanitized_name = str_replace(' ', '', str_replace(['(', ')'], '', $sanitized_name));
//     return $sanitized_name;
// }

// function generate_miniatures(string $source_image_path, array $miniatures): void
// {
//     $source_image = imagecreatefromstring(file_get_contents($source_image_path));
//     foreach ($miniatures as $i => $miniature) {
//         $miniature_width = $miniature['width'];
//         $miniature_height = $miniature['height'];
//         $miniature_image = imagecreatetruecolor($miniature_width, $miniature_height);
//         imagecopyresampled($miniature_image, $source_image, 0, 0, 0, 0, $miniature_width, $miniature_height, imagesx($source_image), imagesy($source_image));
//         $miniature_path = "images/{$i}_{$source_image_path}";
//         imagejpeg($miniature_image, $miniature_path);
//         imagedestroy($miniature_image);
//     }
//     imagedestroy($source_image);
// }

// function get_image_dimensions(array $image_file): array
// {
//     [$width, $height] = getimagesize($image_file['tmp_name']);
//     return ['width' => $width, 'height' => $height];
// }

// function get_num_miniatures(): int
// {
//     return 1;
// }

// function get_miniature_dimensions(array $image_dimensions, int $num_miniatures): array
// {
//     $miniature_dimensions = [];
//     for ($i = 1; $i <= $num_miniatures; $i++) {
//         $width                  = (int) ($image_dimensions['width'] / ($i + 1));
//         $height                 = (int) ($image_dimensions['height'] / ($i + 1));
//         $miniature_dimensions[] = ['width' => $width, 'height' => $height];
//     }
//     return $miniature_dimensions;
// }

// function save_to_directory(array $image_file, string $sanitized_file_name): void
// {
//     $directory = 'images/' . $sanitized_file_name;
//     if (!file_exists($directory)) {
//         mkdir($directory, 0755, true);
//     }
//     move_uploaded_file($image_file['tmp_name'], "$directory/{$sanitized_file_name}.{$image_file['type']}");
// }

// function save_image(string $file_path, string $content_type): void
// {
//     header('Content-Type: ' . $content_type);
//     readfile($file_path);
// }

// function save_to_database(PDO $pdo, string $sanitized_file_name, string $file_path): bool
// {
//     $stmt    = $pdo->prepare('INSERT INTO images (name, path, date_added) VALUES (?, ?, NOW())');
//     $success = $stmt->execute([$sanitized_file_name, $file_path]);
//     return $success;
// }

// function prompt_error(string $message): void
// {
//     http_response_code(400);
//     echo $message;
// }

// isset($_FILES[0]) ? process_image($_FILES[0]) : 'Insira um arquivo para começar <br>';

?>