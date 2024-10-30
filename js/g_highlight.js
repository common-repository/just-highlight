( function( wp ) {
    var sigijh_texthighligh_button = function( props ) {
        return wp.element.createElement(
            wp.editor.RichTextToolbarButton, {
                icon: 'admin-customizer', 
                title: 'Just Highlight', 
                onClick: function() {
                    props.onChange( 
                        wp.richText.toggleFormat(props.value, {
                            type: 'webomnizz/text-highlight'
                        }) 
                    );
                }
            }
        );
    }
    wp.richText.unregisterFormatType('core/underline');
    wp.richText.registerFormatType(
        'webomnizz/text-highlight', {
            title: 'Just Highlight',
            tagName: 'span',
            className: 'sigijh_hlt',
            edit: sigijh_texthighligh_button,
        }
    );
} )( window.wp );
