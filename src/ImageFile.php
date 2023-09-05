<?php

namespace Caio\Workshop;

use Intervention\Image\ImageManagerStatic as Image;
use Ramsey\Uuid\Uuid;

/**
 * Classe de interface com a biblioteca Intervention\Image.
 * 
 */
class ImageFile
{

    /**
     * @var array $dirs Array with all relevant directories:
     * `full_path` path to output images
     * `minis_path` path to output miniature images
     * `watermark_path` path to watermark image
     */
    private array $dirs = [
        'full_path' => 'assets/images/full/',
        'minis_path' => 'assets/images/mini/',
        // 'watermark_path' => 'assets/images/watermark.png'
    ];

    private array $miniatures = [
        [
            'width' => 354,
            'height' => 236,
            'method' => 'blur'
        ],
        [
            'width' => 273,
            'height' => 182,
            'method' => 'resize'
        ],
        [
            'width' => 150,
            'height' => 150,
            'method' => 'fit'
        ],
        [
            'width' => 50,
            'height' => 50,
            'method' => 'fit'
        ]
    ];

    /**
     * @param array $dirs Array with all relevant directories:
     * 	`full_path` path to output images;
     * 	`minis_path` path to output miniature images;
     * 	`watermark_path` path to watermark image;
     */
    public function __construct(array $dirs)
    {
        $this->dirs = array_combine(array_keys($this->dirs), $dirs);
        $dirs       = $this->dirs;
        if (!is_dir($dirs['minis_path'])) {
            mkdir($dirs['minis_path'], 0777, true);
        }
        foreach ($this->getMinisDimensions() as $dimension) {
            if (!is_dir($dirs['minis_path'] . $dimension . "/")) {
                mkdir($dirs['minis_path'] . $dimension . "/", 0777, true);
            }
        }
    }

    /**
     * Gets the number of miniatures as well as their dimentions.
     * @return array an array with width and height of each desired miniature.
     * Should be edited by dev to set the dimentions desired for the project.
     */
    function getMiniatures()
    {
        return $this->miniatures;
    }

    function setMiniatures(array $miniatures)
    {
        $this->miniatures = $miniatures;
    }

    /**
     * @return array an array of strings, one string per miniature. Example:
     * `[300x200, 380x240, 150x150]`
     */
    function getMinisDimensions(): array
    {
        foreach ($this->getMiniatures() as $mini) {
            $dimensions[] = "{$mini['width']}x{$mini['height']}";
        }

        return $dimensions;
    }

    /**
     * Creates the miniatures for an image file, based on a given name and on specified dimensions.
     * Uses the `getMiniatures()` method to define how many miniatures will be generated and create them based on their dimensions.
     * Please refer to `getMiniatures` and edit it to define all needed miniatures.
     * Always outputs jpg files.
     */
    function makeMiniatures(string $file, string $miniDir)
    {
        // Use native PHP function to get name of the file without its extension.
        $path     = pathinfo($file);
        $fileName = $path['filename'];

        // Store miniatures' dimensions in array
        $minis = $this->getMiniatures();

        foreach ($minis as $mini) {

            $miniature = match ($mini['method']) {
                // Smallest dimension fits into dimension. Largest dimension gets cropped to respect aspect ratio.
                'fit' => Image::make($file)->fit($mini['width'], $mini['height']),

                /**
                 * Resize the image so that the largest side fits within the limit; the smaller
                 * side will be scaled to maintain the original aspect ratio
                 * example from https://image.intervention.io/v2/api/resize
                 */
                'resize' => Image::make($file)->resize($mini['width'], $mini['height'], function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    }),

                /**
                 * Creates blurry background to fit a specific aspect ratio.
                 * Similar to "resize", but fills area with blur.
                 */
                'blur' => $this->blurImage($file, $mini['width'], $mini['height']),
                
                default => Image::make($file)->resize($mini['width'], $mini['height'], function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
            };

            /**
             * Save miniature
             */
            $miniature->save($miniDir . $mini['width'] . "x" . $mini['height'] . "/" . $fileName . ".jpg", 80);
        }
    }

    function blurImage($sourcePath, $frameWidth = 354, $frameHeight = 236)
    {

        // Load the original image
        $image = Image::make($sourcePath);

        // Create a new empty canvas for the final image
        $finalImage = Image::canvas($frameWidth, $frameHeight);

        // Clone the original image
        $blurredImage = clone $image;

        // Resize the cloned image to fit the frame (keeping aspect ratio)
        $blurredImage->fit($frameWidth, $frameHeight);

        // Apply the blur effect to the cloned image
        $blurredImage->blur(50);

        // Insert the blurred and resized clone into the canvas
        $finalImage->insert($blurredImage, 'center');

        $image->resize($frameWidth, $frameHeight, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        // Insert the original image over the blurred clone with keeping aspect ratio
        $finalImage->insert($image, 'center');

        // Return the final image (don't forget to save it to final path)
        return $finalImage;
    }

    /**
     * Creates name, output directory and output file for specific image.
     * Adds watermark to image and creates miniatures.
     * @param string $name
     * @param string $tmp_name
     */
    function processSingleFile(string $name, string $tmp_name)
    {
        $fullDir = $this->dirs['full_path'];
        $miniDir = $this->dirs['minis_path'];

        move_uploaded_file($tmp_name, $fullDir . $name);

        $image     = Image::make($fullDir . $name);
        
        if (! empty($this->dirs['watermark_path'])) {
            $watermark = Image::make($this->dirs['watermark_path'])->resize(128, 128, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
    
            $image->insert($watermark, 'top-right', 10, 10);
        }

        $image->save($fullDir . $name, 80, 'jpg');

        $this->makeMiniatures($fullDir . $name, $miniDir);
    }

    /**
     * Loops through all files and process each one separately.
     * Call it via
     * `$yourObject->processAllFiles($_FILES["your_file_form_element"]);`
     * @param array
     */
    function processAllFiles(array $files): void
    {
        for ($i = 0; $i < count($files["name"]); $i++) {
            $name     = $files["name"][$i];
            $tmp_name = $files["tmp_name"][$i];
            $this->processSingleFile($name, $tmp_name);
        }
    }

    function generateBlurImages(int $from, int $to) {
        $files = [];
        $property = $from;
        for ($i = 0; $i <= $to - $from; $i++) {
            if (is_dir($this->dirs['minis_path'] . "/{$property}")){
                $files[$i] = glob($this->dirs['minis_path'] . "/{$property}/*.{jpg, jpeg}", GLOB_BRACE);
                $this->createBlurredFiles($files[$i], $property);
                echo "[" . date("Y/m/d - H:i:s") . "] finished <b>{$property}</b> <br><br>";
            }
            $property++;
        }
    }

    function createBlurredFiles(array $files, $code = null)
    {

        for ($i = 0; $i < count($files); $i++) {
            $pathinfo = pathinfo($files[$i]);
            // dd($pathinfo);
            $name     = $pathinfo["basename"];
            $tmp_name = $files[$i];
            $code ??= $this->getCodeFromFileName($name);

            $targetImagePath = "assets/images/output/{$code}/miniaturas/354x236/";
            $this->createDirIfNotExists($targetImagePath);
            try {
                $img = $this->blurImage($tmp_name);
            } catch (\Throwable $th) {
            echo "[" . date("Y/m/d - H:i:s") . "] tried {$name} <br>";
            echo "<h1>Error: </h1>
                {$th}
                <h2>On file:</h2>
                <h3>{$tmp_name}</h3>";
                continue;
            }
            $img->save($targetImagePath . $name);
            echo "[" . date("Y/m/d - H:i:s") . "] saved {$name} <br>";
        }
        // Usage example

    }

    function getCodeFromFileName(string $fileName): string
    {
        $file = pathinfo($fileName)['filename'];
        // Split name-of-the-file with "-" as separator and retrieve last element
        $array = explode("-", $file);
        $code = end($array); 

        return $code;
    }

    function createDirIfNotExists($path)
    {
        if (! is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }

    /**
     * Cleans the filename to a url-like name. For example: "My Sweet Puppy" becomes "my-sweet-puppy".
     * Should be called without the file extension. If needed, use `pathinfo($yourVar)['filename']` when calling this function.
     * e.g. `sanitizeName(pathinfo($yourVar)['filename'])`. You should split this line into smaller snippets to make your code more organized and readable.
     * @param string
     * @return string the cleaned filename.
     */
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

}

// End of File
