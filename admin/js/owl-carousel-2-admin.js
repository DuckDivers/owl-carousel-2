(function( $ ) {
	'use strict';
    $(document).ready(function(){
    
        $('.dd_owl_tooltip').tooltip();
    
        //  Copy to Clipboard
        
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
        
        $('#dd_owl_thumbs').change(function(){
            if ($(this).is(':checked')) {
                $('#image-options').removeClass('hidden');
            }
            else {
               $('#image-options').addClass('hidden');
            }
        });
        
        $('#dd_owl_show_cta').change(function(){
            if ($(this).is(':checked')) {
                $('#show-button').removeClass('hidden');
            }
            else {
               $('#show-button').addClass('hidden');
            }
        });

        $('select#dd_owl_post_type').change(function(){
            var postType = $(this).val();
                console.log(postType);
            if (postType === 'product'){
                 $('.product-rows').show();
                }    
            else {
                $('.product-rows').hide();
            }            
            // Select the product category
            
            $.ajax({
                url: ajaxurl,
                type: "POST",
                data: {
                    posttype: postType,
                    action: 'owl_carousel_tax',
                    },
                success: function(data){
                    $('#taxonomy').html(data);
                    }
                });
               
            });
        $('select#dd_owl_post_type').trigger('change');
        
        $('#dd_owl_featured_product').change(function(){
            if ($(this).is(':checked')) {
                $('.product-rows.product-ids').hide();
            }
            else {
               $('.product-rows.product-ids').show();
            }
        });

        $('#dd_owl_thumbs, #dd_owl_show_cta, #dd_owl_featured_product').trigger('change');    // Trigger All Functions to Run on Load
        
    }); // Document Ready
        
    
})( jQuery );
