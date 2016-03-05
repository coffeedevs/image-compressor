<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

Route::get('/', function () {
    return view('main');
});


Route::post('compress', function (Request $request) {

    $validator = Validator::make($request->all(), [
        'path' => 'required'
    ]);

    if ($validator->fails()) {
        return new \Illuminate\Http\Response('Debe especificar el path de la carpeta de imágenes',500);
    }

    $path=Input::get('path');
    if(is_dir($path))
        $path = realpath($path).'\\';

    $countFiles = \App\Util\FileUtils::totalArchivosImagen($path);

    if($countFiles == -1)
        return new \Illuminate\Http\Response('El "path" ingresado no es válido',500);
    elseif($countFiles == 0)
        return new \Illuminate\Http\Response('La carpeta no contiene ninguna imagen',500);

    echo $countFiles;
    flush();
    ob_flush();
    \App\Util\FileUtils::listarDirectoriosRuta($path);
});

Route::group(['middleware' => ['web']], function () {
    //
});
