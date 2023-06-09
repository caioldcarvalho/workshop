<?php

namespace Caio\Workshop;

use Intervention\Image\ImageManagerStatic as Image;

require 'vendor/autoload.php';

$dirs = [
    'full_path' => 'assets/images/full/',
    'minis_path' => 'assets/images/mini/'
];

$file = new ImageFile($dirs);

Image::configure(['driver' => 'gd']);

isset($_FILES['files']) ? $file->processAllFiles($_FILES['files']) : 'Insira um arquivo';
isset($_FILES['blur']) ? $file->createBlurredFiles($_FILES['blur']) : 'Insira um arquivo para borrar';

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index</title>
</head>

<body>
    <?php
        include_once 'form.php';
    ?>
</body>

</html>

<!-- End of File -->
