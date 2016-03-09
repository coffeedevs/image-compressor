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
                        $originalSize = File::size($ruta . $file);
                        usleep(600000);
                        if (strcasecmp($mime, 'image/png') == 0) {
                            try {
                                if (self::tinify) {
                                    self::compress_with_tinify($ruta, $file);
                                } else {
                                    self::compress_with_pngquant($ruta . $file);
                                }
                                $newSize = File::size($ruta . $file);
                                $compressRatio = $originalSize * 100 / $newSize;
                                self::echoFileInformation($ruta, $file, '<span style="color:green"> ' . $compressRatio . '% OK</span>');
                            } catch (\Exception $ex) {
                                self::echoFileInformation($ruta, $file, '<span style="color:red">FAIL</span>');
                            }
                        } else if (strcasecmp($mime, 'image/jpg') == 0 || (strcasecmp($mime, 'image/jpeg') == 0)) {
                            try {
                                self::compress_with_jpegtran($ruta . $file);
                                $newSize = File::size($ruta . $file);
                                $compressRatio = $originalSize * 100 / $newSize;
                                self::echoFileInformation($ruta, $file, '<span style="color:green"> ' . $compressRatio . '% OK</span>');
                            } catch (\Exception $ex) {
                                self::echoFileInformation($ruta, $file, '<span style="color:red">FAIL</span><br>' . $ex->getMessage());
                                //self::echoFileInformation($ruta, $file, '<span style="color:red">FAIL</span>');
                            }
                        }
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
                        if (strcasecmp($mime, 'image/png') == 0 || (strcasecmp($mime, 'image/jpg') == 0) || (strcasecmp($mime, 'image/jpeg') == 0)) {
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
    private static function compress_with_pngquant($path_to_png_file)
    {
        if (!file_exists($path_to_png_file)) {
            throw new Exception("File does not exist: $path_to_png_file");
        }

        $compressed_png_content = shell_exec("pngquant --force --ext .png " . escapeshellarg($path_to_png_file));

        return $compressed_png_content;
    }

    private static function echoFileInformation($route, $file, $status)
    {
        echo json_encode([
            'type' => 'compression_status',
            'data' => $route . $file . $status,
        ]);
        self::flush();
    }

    private static function flush()
    {
        flush();
        ob_flush();
    }

    private static function compress_with_jpegtran($path_to_jpeg_file)
    {
        if (!file_exists($path_to_jpeg_file)) {
            throw new Exception("File does not exist: $path_to_jpeg_file");
        }

        $compressed_jpeg_file = system("jpegtran -copy none -optimize -outfile " . escapeshellarg($path_to_jpeg_file)) . " " . escapeshellarg($path_to_jpeg_file);
        if ($compressed_jpeg_file === null) {
            throw new Exception("Conversion to compressed JPEG failed.");
        }
        return $compressed_jpeg_file;
    }
}