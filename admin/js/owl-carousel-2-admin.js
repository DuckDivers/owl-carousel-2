(function( $ ) {
	'use strict';
    var gotPosts = 0;
    var getPostType, postSelected, taxOptions, taxCounter = 0;
    $(document).bind('ready ajaxcomplete', function(){
        postSelected = $('select#dd_owl_post_type').val();
        taxOptions = $('input[name="dd_owl_tax_options"]:checked').val();
        if (postSelected === ''){getPostType = 'Null';} else {getPostType = postSelected;}
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
                $('.image-options').removeClass('hidden');
            }
            else {
               $('.image-options').addClass('hidden');
            }
        });

        $('#dd_owl_show_cta').change(function(){
            if ($(this).is(':checked')) {
                $('.show-button').removeClass('hidden');
            }
            else {
               $('.show-button').addClass('hidden');
            }
        });

        $('select#dd_owl_post_type').change(function(){
            var postType = $(this).val();
            $('span.ajax-loader').css('display', 'block');
            $('#term-row.visible').addClass('hidden').removeClass('visible');
            if (postType !== postSelected) {
                gotPosts = 0;
                $('select#dd_owl_post_ids').find('option').remove().end();
                $('input[name="dd_owl_tax_options"]').trigger('change');
                postSelected = postType;
            }
            if (postType === 'product'){
                 $('.product-rows').show();
                }
            else {
                $('.product-rows').hide();
            }
            // Select the product category
            var postID = $('input#post_ID').val();
            $.ajax({
                url: ajaxurl,
                type: "POST",
                data: {
                    posttype: postType,
                    action: 'owl_carousel_tax',
                    postid: postID,
                    },
                success: function(data){
                        $('#taxonomy').html(data);
                        ajax_get_terms();
                        }
                });

            });
        $('select#dd_owl_post_type').trigger('change');

        $('input[name="dd_owl_tax_options"]').change(function(){
            var ck = $('input[name="dd_owl_tax_options"]:checked').val();
			var terms = ($('#dd_owl_post_taxonomy_term').length) ? $('#dd_owl_post_taxonomy_term').val() : -1;
			var showTerms = false;
			if (!null == terms) {
				showTerms = ( terms.length ) ? true : false;
			}
            if (ck === 'taxonomy'){
                $('#category-row').removeClass('hidden').addClass('visible');
                $('#choose-postids.visible').addClass('hidden').removeClass('visible');
				if (showTerms) $('#term-row').addClass('visible').removeClass('hidden');
            }
            else if (ck === 'postID'){
                if (gotPosts === 0) {
                    dd_select_posts();
                }
                else {
                    $('#choose-postids').removeClass('hidden').addClass('visible');
                    $('#dd_owl_post_ids').show();
                }
                $('#category-row.visible, #term-row.visible').addClass('hidden').removeClass('visible');
            }
            else if (ck === 'featured_product') {
                $('#category-row.visible, #term-row.visible, #choose-postids').addClass('hidden').removeClass('visible');
            }
			else if (ck === 'show_tax_only') {
                $('#category-row').removeClass('hidden').addClass('visible');
				$('#choose-postids.visible').addClass('hidden').removeClass('visible');
				if (showTerms) $('#term-row').addClass('visible').removeClass('hidden');
			} else {
                $('#category-row.visible, #term-row.visible').addClass('hidden').removeClass('visible');
                $('#choose-postids.visible').addClass('hidden').removeClass('visible');
            }
            return false;
        });

        $('select#dd_owl_btn_display').change(function(){
            if ($(this).val() !== 'inline'){
                $('.button-margin.hidden').addClass('visible').removeClass('hidden');
            }
            else {$('button-margin').addClass('hidden').removeClass('visible');}
        });

        // Trigger All Functions to Run on Load
        $('#dd_owl_thumbs, #dd_owl_show_cta, select#dd_owl_btn_display').trigger('change');

        if ( taxOptions !== null && taxCounter === 0 ) {$('input[name="dd_owl_tax_options"]').trigger('change'); taxCounter = 1;}

    }); // Document Ready

    $(document).on('ajaxComplete', function(){
        $('#dd_owl_post_taxonomy_type').change(function(){
            ajax_get_terms();
        });
    });

    function ajax_get_terms(){
        $('span.ajax-loader').css('display', 'block');
        var postType = $('select#dd_owl_post_type').val();
        var taxType = $('select#dd_owl_post_taxonomy_type').val();
        var postID = $('input#post_ID').val();
        $.ajax({
                url: ajaxurl,
                type: "POST",
                data: {
                        posttype: postType,
                        taxtype: taxType,
                        postid: postID,
                        action: 'owl_carousel_terms',
                    },
				dataType: 'json',
                success: function(data){
                    $('#taxterm').html(data);
                    $('span.ajax-loader').css('display', 'none');
                        if (data.length > 234){
                            $('#term-row').addClass('visible').removeClass('hidden');
                        }
						$('select#dd_owl_post_taxonomy_term').select2({
                            placeholder: "Choose Terms",
                            allowClear: true
                        });
                        $('select#dd_owl_post_taxonomy_term').find(':selected').data('selected');
                    },
					error: function(data) {
						console.log(data);
					}
                });

    }

    function dd_select_posts(){
        $('span.ajax-loader').css('display', 'block');
        var postType = $('select#dd_owl_post_type').val();
        var carouselID = $('input#post_ID').val();
        $.ajax({
                url: ajaxurl,
                type: "POST",
                data: {
                    posttype: postType,
                    carousel_id: carouselID,
                    action : 'owl_carousel_posts'
                },
            success: function(data){
                    $('#dd_owl_post_ids').append(data);
                    $('.dd-owl-multi-select').select2({
                        placeholder: 'Choose the posts'
                    });
                    $('#choose-postids').removeClass('hidden').addClass('visible');
                    $('#dd_owl_post_ids').show();
                    $('span.ajax-loader').css('display', 'none');
            },
            error: function(data) {
                console.log(data);
            }
        });
        gotPosts = 1;
    }

})( jQuery );
