<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Util\FileUtils;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;

class CompressionController extends Controller
{
    use ValidatesRequests;

    public function compress(Request $request)
    {
        $this->initialSetup();
        $this->validate($request, [
            'path' => 'required',
        ]);

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

        echo json_encode([
            'type' => 'total_file_count',
            'data' => $countFiles,
        ]);
    }

    private function initialSetup()
    {
        ini_set('max_execution_time', 3000);
        \Tinify\setKey(env('TINY_API_KEY', ''));
    }
}
