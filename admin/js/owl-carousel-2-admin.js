(function( $ ) {
	'use strict';
    $(document).ready(function(){
    
        $('.dd_owl_tooltip').tooltip();
    
    // ##### CLICK EVENT HANDLER FOR THE SHORTCODE 'Copy to Clipboard' BUTTON #####
        $('#dd_shortcode_copy').click(function() {
            var shortcode = document.getElementById('dd_owl_shortcode').innerHTML;
            var aux = document.createElement("input"); // Create a "hidden" input
            aux.setAttribute("value", shortcode); // Assign it the value of the specified element
            document.body.appendChild(aux); // Append it to the body
            aux.select(); // Highlight its content
            document.execCommand("copy"); // Copy the highlighted text
            document.body.removeChild(aux); // Remove it from the body
            // DISPLAY 'Shortcode Copied' message 
            document.getElementById('dd_owl_shortcode').innerHTML = "Copied!";
            setTimeout(function(){ document.getElementById('dd_owl_shortcode').innerHTML = shortcode; }, 1000);
        });
	
    }); // Document Ready
    
})( jQuery );
