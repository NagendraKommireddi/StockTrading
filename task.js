window.addEventListener( "pageshow", function ( event ) {
    var historyTraversal = event.persisted || 
                           ( typeof window.performance != "undefined" && 
                                window.performance.navigation.type === 2 );
    if ( historyTraversal ) {
      window.location.reload();
    }
  });

$(document).ready(
    function(){
        $( "#datepicker" ).datepicker({
            changeYear: true,
            changeMonth: true
        }
        );
        $( "#datepicker2" ).datepicker({
            changeYear: true,
            changeMonth: true
        });
        $('#fileToUpload').change(
            function(){
                if ($(this).val()) {
                    $('#submit').attr('disabled',false);
                } 
            }
            );

            $(window).bind("pageshow", function(event) {
                if (event.originalEvent.persisted) {
                    window.location.reload(); 
                }
            });

    });
