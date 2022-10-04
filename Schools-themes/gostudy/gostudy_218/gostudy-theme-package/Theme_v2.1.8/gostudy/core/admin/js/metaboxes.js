"use strict";
( function( $ ) {

    $(document).ready( function() {
        window.rt_metabox_addon = new rt_metabox_addon({});
    });

    function rt_metabox_addon() {
        var that = this;

        that.set_tabs = rt_metabox_tab;
        that.set_dependancy = rt_metaboxio_dependancy;

        that.set_tabs();
        that.set_dependancy(this);

        $( ':input.rwmb-select_icon' ).each( update_icon );
        $( '.rwmb-input' ).on( 'clone', ':input.rwmb-select_icon', update_icon );
    }

    function rt_metabox_tab() {
        var $item = $('#advanced-sortables');
        $item.removeClass("meta-box-sortables ui-sortable");
        var item_children = $('#advanced-sortables .postbox');
        $item.wrapInner( "<div class='tabs-container'></div>");
        $item.append('<div class="tabs__caption"></div>');

        $(item_children).each( function() {
            var clone = $(this).find('h2').clone().attr('data-item', $(this).attr('id'));
            $('.tabs__caption').append(clone);
        });

        $(item_children).first().css( "display", "block" );
        $('.tabs__caption h2').first().addClass('active');

        $('.tabs__caption h2').on('click',function() {
            var active = $(this).data('item');
            $('.tabs__caption h2').removeClass('active');
            $(this).addClass('active');

            $(this).closest('#advanced-sortables').find('.postbox').css({'display' : 'none'});
            $(this).closest('#advanced-sortables').find('.postbox').filter(function( index ) {
                return $( this ).attr( "id" ) === active;
              }).fadeIn('slow');
        })
    }

    /**
    * Gets the value either from the parent data
    */
    function get_value(depend, item = null) {
        if (!depend) {
            return;
        }
        depend = $(depend)[0];

        if (depend) {
            if (depend.tagName == 'SELECT') {
                return depend.value
            } else if ( depend.tagName == 'DIV' ) {
                if ($(depend).attr('id') == 'formatdiv') {
                    return $(depend).find('input[type=radio]:checked').val()
                };
            }
            else if ( depend.tagName == 'INPUT' ) {
                if ($(depend).hasClass('rwmb-image_select')) {
                    return $( depend ).val();
                } else if ($(depend).hasClass('rwmb-button_group')) {
                    return $( depend ).val();
                } else {
                    return $(depend).is(":checked");
                }
            }
        } else {
            return $( "input[name='"+item+"']:checked" ).val();
        }

    }

    function rt_metaboxio_dependancy($a) {
        $('.rwmb-field').each( function() {
            if (jQuery(this).find('.rwmb-input').is(':empty')) {
                jQuery(this).closest('.rwmb-field').css({'display' : 'none'});
            }
        })
        var rwmb_item = $('.rwmb-input').find('[data-conditional-logic]');
        $(rwmb_item).each( function() {
            var container = $(this).closest('.rwmb-field');
            var depArray = $(this).data('conditionalLogic');
            var $output = true;
            var element = {};
            for (var i = 0; i < depArray[0].length; i++) {

                var $selector   = depArray[0][i][0];
                var depend      = depArray[0][i][1];
                var logicApply  = depArray[0][i][2];
                var $logicValue = get_value ($('#'+ $selector), $selector);

                $output = $output && get_compare ($logicValue ,depend, logicApply);

                if (typeof(element[$selector]) == 'undefined') {
                    var val = [depend, logicApply]
                    var metabox = [];
                    //Add properties to the element
                    metabox['properties'] = [val];
                    element[$selector] = metabox;
                } else {
                    var val = [depend, logicApply];
                    Array.prototype.push.apply(element[$selector]['properties'], [val]);
                }
            };

            for (var i = 0; i < Object.keys(element).length; i++) {
                var item = $('#'+ Object.keys(element)[i]);
                var event = 'change';
                if (item.length === 0) {
                    if ($("input[name='"+Object.keys(element)[i]+"']").hasClass( "rwmb-image_select" )) {
                        item = $("input[name='"+Object.keys(element)[i]+"']").closest('.rwmb-image-select');
                        event = 'click';
                    }
                    if ($("input[name='"+Object.keys(element)[i]+"']").hasClass( "rwmb-button_group" )) {
                        item = $("input[name='"+Object.keys(element)[i]+"']").closest('label');
                        event = 'click';
                    }
                }
                item.on(event,function() {
                    var id = $(this).attr('id');
                    var scope = this;
                    if (!id) {
                        id = $(this).find('input').attr( "name" );
                        scope = $(this).find('input');
                    }
                    var $logicValue = get_value (scope);

                    var obj = element[id]['properties'];
                    var $return = true;

                    for (var i = 0; i < obj.length; i++) {
                        $return = $return && get_compare ($logicValue ,obj[i][0], obj[i][1]);
                    }

                    for (var i = 0; i < Object.keys(element).length; i++ ) {
                        id = Object.keys(element)[i];
                        $logicValue = get_value ('#'+id, id);
                        obj = element[id]['properties'];
                        $return = $return && get_compare ($logicValue ,obj[0][0], obj[0][1]);
                    }
                    visible_item($return, container);
                });
            }
            visible_item($output, container);
        })
    }

    function visible_item($output, container) {
        //If return true show container
        if ($output) {
            container.fadeIn(400);
        } else {
            container.fadeOut(400);
        }
    }

    /**
    * Determines and validates what comparison operator to use.
    */
    function get_compare ($logicValue, depend, logicApply) {
        if (depend == '=') {
            return $logicValue == logicApply;
        } else if (depend == '!=') {
            return $logicValue != logicApply;
        }
    }

    function update_icon() {
        /**
        * Update select with icon element
        * Used for static & dynamic added elements (when clone)
        */
        var self = $( this ),
        obj = self.data( 'options' );

        function formatState (result) {
            if (!result.id) {
                 return result.text;
            }

            var $result = $(
                '<span><i class="'+ result.element.value.toLowerCase() + '"/> ' + result.text + '</span>'
             );
            return $result;
        };

        //Customizes the way that search results are rendered.
        obj['templateResult'] = formatState;
        //Customizes the way that selections are rendered.
        obj['templateSelection'] = formatState;

        self.siblings( '.select2-container' ).remove();
        self.select2( obj );
    }


} )( jQuery );