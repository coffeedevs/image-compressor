<?php
namespace App\Util;

use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Created by PhpStorm.
 * User: martin.guadalupe
 * Date: 3/3/2016
 * Time: 7:16 PM
 */
class FileUtils
{
    const tinify = false;

    public static function listDirectoriesForRoute($ruta)
    {
        // abrir un directorio y listarlo recursivo
        if (is_dir($ruta)) {

            if ($dh = opendir($ruta)) {
                while (($file = readdir($dh)) !== false) {
                    //esta línea la utilizaríamos si queremos listar todo lo que hay en el directorio
                    //mostraría tanto archivos como directorios
                    if (is_dir($ruta . $file) && $file != "." && $file != "..") {
                        self::listDirectoriesForRoute($ruta . $file . "/");
                    } else {
                        $mime = File::mimeType($ruta . $file);
                        if ($mime == 'image/png' || $mime == 'image/jpeg') {
                            try {
                                if (self::tinify) {
                                    Storage::put('file.png', self::compress_with_tinify($ruta, $file));
                                } else {
                                    Storage::put('file.png', self::compress_with_pngquant($ruta . $file));
                                }
                                echo $ruta . $file . ' <span style="color:#00cc00">OK</span>';
                                flush();
                                ob_flush();
                                //usleep(100000);
                                sleep(5);
                            } catch (\Exception $ex) {
                                //echo $ruta . $file;
                                echo $ex->getMessage();
                                //echo $ruta . $file . ' <span style="color:red">FAIL</span>';
                                flush();
                                ob_flush();
                                usleep(100000);
                            }
                        }
                        //TODO else
                    }
                }
                closedir($dh);
            }
        } else
            return -1;
    }

    public static function imageFilesTotal($ruta)
    {
        if (is_dir($ruta)) {
            $i = 0;
            if ($dh = opendir($ruta)) {
                while (($file = readdir($dh)) !== false) {
                    if (is_dir($ruta . $file) && $file != "." && $file != "..") {
                        $i += self::imageFilesTotal($ruta . $file . "/");
                    } else {
                        $mime = File::mimeType($ruta . $file);
                        if ($mime == 'image/png' || $mime == 'image/jpeg') {
                            $i++;
                        }
                    }
                }
                closedir($dh);
            }
            return $i;

        } else
            return -1;
    }

    private static function compress_with_tinify($route, $file)
    {
        $source = \Tinify\fromFile($route . $file);
        $resized = $source->resize(array(
            "method" => "scale",
            "width" => 1920
        ));
        $resized->toFile($route . $file);
        return $resized;
    }


    /**
     * Optimizes PNG file with pngquant 1.8 or later (reduces file size of 24-bit/32-bit PNG images).
     *
     * You need to install pngquant 1.8 on the server (ancient version 1.0 won't work).
     * There's package for Debian/Ubuntu and RPM for other distributions on http://pngquant.org
     *
     * @param $path_to_png_file string - path to any PNG file, e.g. $_FILE['file']['tmp_name']
     * @param $max_quality int - conversion quality, useful values from 60 to 100 (smaller number = smaller file)
     * @return string - content of PNG file after conversion
     * @throws Exception
     */
    private static function compress_with_pngquant($path_to_png_file, $max_quality = 90)
    {
        if (!file_exists($path_to_png_file)) {
            throw new Exception("File does not exist: $path_to_png_file");
        }

        // guarantee that quality won't be worse than that.
        $min_quality = 60;
        // '-' makes it use stdout, required to save to $compressed_png_content variable
        // '<' makes it read from the given file path
        // escapeshellarg() makes this safe to use with any path
        //$compressed_png_content = shell_exec("pngquant --quality=$min_quality-$max_quality - < " . escapeshellarg($path_to_png_file));
        //$compressed_png_content = exec("pngquant '$path_to_png_file'");

        $compressed_png_content = shell_exec("pngquant --quality=$min_quality-$max_quality - < " . escapeshellarg($path_to_png_file) . " --ext .png --force ");
        if ($compressed_png_content === null) {
            throw new Exception("Conversion to compressed PNG failed . Is pngquant 1.8 + installed on the server ? ");
        }
        return $compressed_png_content;
    }
}