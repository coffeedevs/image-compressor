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

    function init() {
        $("#main-panel").css('visibility', 'visible');
        $("#list-files").html("");
        var $progressBar = $("#progressbar");
        $progressBar.css("width", "0%");
        $progressBar.css('background-color', '#337ab7');
        var $count = $("#count");
        $count.text(" ");
        var last_response_len = false;
        var totalFiles;
        var currentIteration = 0;
        var path = $("#path").val();
        $.ajax('{{url('compress')}}', {
                    xhrFields: {
                        onprogress: function (e) {

                            var this_response, response = e.currentTarget.response;
                            if (last_response_len === false) {
                                this_response = response;
                                last_response_len = response.length;
                            }
                            else {
                                this_response = response.substring(last_response_len);
                                last_response_len = response.length;
                            }
                            //this_response has the data for each stream output.

                            var data = JSON.parse(this_response);
                            console.log(data);
                            if (data.type == 'total_file_count') {
                                totalFiles = parseInt(data.data);
                                console.log(totalFiles);
                            }
                            else {
                                currentIteration++;
                                $count.text(currentIteration + " of " + totalFiles);
                                $progressBar.css("width", (currentIteration * (100 / totalFiles)) + "%")
                                $('#list-files').append('<div style="margin:2px">' + data.data + '</div>')
                                console.log(this_response);
                            }
                        }
                    },
                    method: 'POST',
                    data: 'path=' + path
                })
                .done(function (data) {
                    console.log('Complete response = ' + data);
                    $count.text('DONE!');
                    $progressBar.css("background-color", "limegreen");

                })
                .fail(function (data) {
                    $.notify({
                        // options
                        icon: 'glyphicon glyphicon-warning-sign',
                        message: data.responseText
                    }, {
                        // settings
                        type: 'danger'
                    });
                    console.log('Error: ', data);
                });
        console.log('Request Sent');
    }
</script>
