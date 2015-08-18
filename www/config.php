<?php
    require_once('functions.php');
    require_once('output_fns.php');
    $db = db_connect();
    
    // Start a secure session
	sec_session_start();
	
	do_header('Power Manager'); // Make a header with the title in quote marks.
    
    if(login_check($db) == false) {
		// User is not logged in.
	   do_login_page('Please login to use this site.');
	   exit();
	}
    
    do_nav_bar();
	start_content();
	
?>
<script type="text/javascript">
    $(document).ready(function() {
        // Set up AJAX requests
        $.ajaxSetup({
            cache: false,
            beforeSend: function() {
                // Dim ajax_content div while content loads
                $("#ajax_content").fadeTo('fast', 0.25);
                // Fade overlay div in
                $('#overlay').fadeIn('fast')
            },
            complete: function() {
                // Fade ajax_content div when content loads
                $("#ajax_content").fadeTo('fast', 1);
                // Fade overlay div out
                $('#overlay').fadeOut('fast');
            },
            success: function() {
                // Fade ajax_content div when content loads
                $("#ajax_content").fadeTo('fast', 1);
                // Fade overlay div out
                $('#overlay').fadeOut('fast');
            }
        });
        var $container = $("#ajax_content");
        $container.load("ajax_config.php");
        var refreshId = setInterval(function() {
            $container.load('ajax_config.php');
        }, 30000); // Auto refresh ajax_content div every 30 seconds
        
        $div = $("#ajax_content");
        
        // Set up overlay over ajax_content div while loading occurs
        $("#overlay").css({
          opacity : 1,
          top     : $div.offset().top,
          width   : $div.outerWidth(),
          height  : $div.outerHeight()
        });
        
        // Loading gif
        $("#img-load").css({
          top  : ($div.height() / 2),
          left : ($div.width() / 2)
        });
        
        // Send AJAX request when appliance name text box loses focus (user finished typing and clicks away)
        $(document).on('blur', '.appliance_name', function() {
            var data = { // POST data to send to ajax_config.php
                'appliance_name_number': this.id,
                'name': this.value,
            };
            $.post( "ajax_config.php", data, function(data) { // Send AJAX request and update ajax_content div with result
                $( "#ajax_content" ).html(data);
            });
        });
        
        // Send AJAX request when appliance enabled check box is checked or unchecked by user
        $(document).on('change', '.appliance_enabled', function() {
            var data = { // POST data to send to ajax_config.php
                'appliance_enabled_number': this.id,
                'enabled': this.checked,
            };
            $.post( "ajax_config.php", data, function(data) { // Send AJAX request and update ajax_content div with result
                $( "#ajax_content" ).html(data);
            });
        });
    });
</script>
<div id="ajax_wrapper">
    <div id="ajax_content"><br/><br/><br/><br/><br/><br/><br/></div>
    <div id="overlay"><br/><h2 style="text-align: center;"><img src="ajax-loader.gif" style="vertical-align: middle;"/>&nbsp;Loading...</h2></div>
</div>
<?php
    
    do_footer();
?>