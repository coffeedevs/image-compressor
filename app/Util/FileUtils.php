<?php
namespace App\Util;

use Illuminate\Support\Facades\File;

/**
 * Created by PhpStorm.
 * User: martin.guadalupe
 * Date: 3/3/2016
 * Time: 7:16 PM
 */
class FileUtils
{
    public static function listarDirectoriosRuta($ruta)
    {
        // abrir un directorio y listarlo recursivo
        if (is_dir($ruta)) {

            if ($dh = opendir($ruta)) {
                while (($file = readdir($dh)) !== false) {
                    //esta línea la utilizaríamos si queremos listar todo lo que hay en el directorio
                    //mostraría tanto archivos como directorios
                    if (is_dir($ruta . $file) && $file != "." && $file != "..") {
                        self::listarDirectoriosRuta($ruta . $file . "/");
                    } else {
                        $mime = File::mimeType($ruta . $file);
                        if ($mime == 'image/png' || $mime == 'image/jpeg') {
                            try {
                                $source = \Tinify\fromFile($ruta.$file);
                                $resized = $source->resize(array(
                                    "method" => "scale",
                                    "width" => 1920
                                ));
                                $resized->toFile($ruta.$file);
                                    echo $ruta.$file.' <span style="color:#00cc00">OK</span>';
                                    flush();
                                    ob_flush();
                                    usleep(100000);


                            } catch (\Exception $ex) {
                                echo $ruta.$file.' <span style="color:red">FAIL</span>';
                                flush();
                                ob_flush();
                                usleep(100000);
                            }
                        }
                    }
                }
                closedir($dh);
            }
        } else
            return -1;
    }

    public static function totalArchivosImagen($ruta)
    {

        if (is_dir($ruta)) {
            $i = 0;
            if ($dh = opendir($ruta)) {
                while (($file = readdir($dh)) !== false) {
                    if (is_dir($ruta . $file) && $file != "." && $file != "..") {
                        $i += self::totalArchivosImagen($ruta . $file . "/");
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

}