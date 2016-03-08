@extends('main')
@section('content')
    <div class="starter-template">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="form-group">
                    <label for="path">Ingrese el Path:</label>
                    <input type="text" class="form-control" id="path">
                </div>
                <div class="form-group">
                    <button id="enviar" class="btn btn-info">Enviar</button>
                </div>


            </div>

            <div id="main-panel" class="col-md-6 col-md-offset-3 text-left" style="display:none;margin-top:20px">
                <div id="count">

                </div>

                <div class="progress">
                    <div id="progressbar" class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0"
                         aria-valuemax="100" style="width: 0%;">
                    </div>

                </div>
                <div id="list-files" style="overflow: scroll;background-color:#3c3c3c;color:white;height:400px">

                </div>
            </div>
        </div>

    </div>
@stop