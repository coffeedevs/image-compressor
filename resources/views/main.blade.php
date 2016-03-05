
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="{{asset('favicon.ico')}}">

    <style>
        body {
            padding-top: 50px;
        }
        .starter-template {
            padding: 40px 15px;
            text-align: center;
        }

    </style>


    <title>Starter Template for Bootstrap</title>

    <!-- Bootstrap core CSS -->
    <link href="{{asset('/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('/css/animate.css')}}" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>

<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Tiny Compressor</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                {{--<li class="active"><a href="#">Home</a></li>--}}
                {{--<li><a href="#about">About</a></li>--}}
                {{--<li><a href="#contact">Contact</a></li>--}}
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>

<div class="container">
    <div class="starter-template">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="form-group">
                    <label for="path">Ingrese el Path:</label>
                    <input type="text" class="form-control" id="path">
                </div>
                <div class="form-group">
                    <button id="enviar" class="bnt-default">Enviar</button>
                </div>
                <div class="progress">
                    <div id="progressbar" class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                    </div>

                </div>


            </div>

            <div class="col-md-6 col-md-offset-3 text-left">
                <div id="list-files" style="overflow: scroll;background-color:#3c3c3c;color:white;height:400px">

                </div>
            </div>
        </div>

    </div>

</div><!-- /.container -->


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="{{asset('assets/js/vendor/jquery.min.js')}}"><\/script>')</script>
<script src="{{asset('js/bootstrap.min.js')}}"></script>
<script src="{{asset('assets/js/bootstrap-notify.min.js')}}"></script>
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script>
    $("#enviar").click(init);

    function init(){
        var last_response_len = false;
        var first = false;
        var tot;
        var c=0;
        var path = $("#path").val();
        $.ajax('{{url('compress')}}', {
                    xhrFields: {
                        onprogress: function(e)
                        {

                            var this_response, response = e.currentTarget.response;
                            if(last_response_len === false)
                            {
                                this_response = response;
                                last_response_len = response.length;
                            }
                            else
                            {
                                this_response = response.substring(last_response_len);
                                last_response_len = response.length;
                            }

                            if(!first){
                                tot = parseInt(this_response);
                                first = true;
                                console.log(tot);
                            }
                            else{
                                c++;

                                $("#progressbar").css("width",(c*(100/tot))+"%")

                                $('#list-files').append('<div style="margin:2px">'+this_response+'</div>')
                                console.log(this_response);
                            }


                        }
                    },
                    method:'POST',
                    data:'path='+path
                })
                .done(function(data)
                {
                    console.log('Complete response = ' + data);
                })
                .fail(function(data)
                {
                    $.notify({
                        // options
                        icon: 'glyphicon glyphicon-warning-sign',
                        message: data.responseText
                    },{
                        // settings
                        type: 'danger'
                    });
                    console.log('Error: ', data);
                });
        console.log('Request Sent');
    }
</script>




</body>
</html>
