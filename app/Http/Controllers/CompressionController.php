<?php

namespace App\Http\Controllers;

use App\Util\FileUtils;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class CompressionController extends Controller
{
    public function compress(Request $request)
    {
        $this->initialSetup();
        $validator = Validator::make($request->all(), [
            'path' => 'required'
        ]);

        if ($validator->fails()) {
            return new Response('Debe especificar el path de la carpeta de imágenes', 500);
        }

        $path = Input::get('path');
        $path = $this->addFinalSlashToPath($path);

        $this->echoTotalImageFiles($path);
        $this->flushTheBuffer();

        FileUtils::listDirectoriesForRoute($path);
    }

    private function addFinalSlashToPath($path)
    {
        if (is_dir($path))
            return realpath($path) . '\\';
        else return $path;
    }

    private function flushTheBuffer()
    {
        flush();
        ob_flush();
    }

    private function echoTotalImageFiles($path)
    {
        $countFiles = FileUtils::imageFilesTotal($path);

        if ($countFiles == -1)
            return new Response('El "path" ingresado no es válido', 500);
        elseif ($countFiles == 0)
            return new Response('La carpeta no contiene ninguna imagen', 500);

        echo $countFiles;
    }

    private function initialSetup()
    {
        ini_set('max_execution_time', 3000);
        \Tinify\setKey(env('TINY_API_KEY', ''));
    }
}
