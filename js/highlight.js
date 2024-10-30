(function() {
    tinyMCE.PluginManager.add('sigijh_tc_button', function( editor, url ) {
        editor.addButton( 'sigijh_tc_button', {
            title: 'Just Highlight',
            icon: 'icon sigijh-own-icon',
            onclick: function() {

                var edtidor = tinyMCE.activeEditor;
                var className = edtidor.selection.getNode().className;
                var is_highlight = className.includes("sigijh_hlt");

                if(is_highlight){
                    var nodeRemove = edtidor.selection.getNode();
                    var nodeReplace = edtidor.selection.getNode().innerHTML;
                    edtidor.dom.remove(nodeRemove);
                    tinyMCE.execCommand('mceReplaceContent', false, nodeReplace);
                } else {
                    var node = edtidor.selection.getContent();
                    tinyMCE.execCommand('mceReplaceContent', false, '<span class="sigijh_hlt">' + node + '</span>'); 
                }
            }
        });
    });
})();


