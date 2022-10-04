"use strict";

jQuery(document).ready( function($) {
    //Call colorpicker option
    $( '.colorpicker' ).wpColorPicker();
    
    function ct_media_upload(button_class) {
        var _custom_media = true,
        _orig_send_attachment = wp.media.editor.send.attachment;
        $('body .rt-image-form').each( function (){
            $(this).find('.ct_tax_media_button').on('click', function() {
                var item = $(this);
                var parent = item.parents('.rt-image-form');
                var button_id = '#'+item.attr('id');
                var button = $(button_id);
                _custom_media = true;
                wp.media.editor.send.attachment = function(props, attachment){
                    if ( _custom_media ) {
                        parent.find('.custom_media_url').val(attachment.id);
                        parent.find('.category-image-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
                        parent.find('.category-image-wrapper .custom_media_image').attr('src',attachment.url).css('display','block');
                    } else {
                        return _orig_send_attachment.apply( button_id, [props, attachment] );
                    }
                }
                wp.media.editor.open(item.attr('id'));
                return false;
            });
        })
    }
    ct_media_upload(); 
    $('body .rt-image-form').each( function (){
        $(this).find('.ct_tax_media_remove').on('click', function(){
            var parent = $(this).parents('.rt-image-form');
            parent.find('.custom_media_url').val('');
            parent.find('.category-image-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
        });
    })
    // Thanks: http://stackoverflow.com/questions/15281995/wordpress-create-category-ajax-response
    $(document).ajaxComplete(function(event, xhr, settings) {
        var queryStringArr; 
        if(settings && settings.data){
            queryStringArr = settings.data.split('&');
        }
        if( $.inArray('action=add-tag', queryStringArr) !== -1 ){
            var xml = xhr.responseXML;
            var $response = $(xml).find('term_id').text();
            if($response!=""){
                // Clear the thumb image
                $('.category-image-wrapper').html('');
            }
        }
    });
});