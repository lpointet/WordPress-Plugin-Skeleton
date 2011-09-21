(function() {
    tinymce.create("tinymce.plugins.gb_sk_shortcode", {
        init : function(ed, url) {
            // Shortcode insertion
            ed.addCommand('gb_sk_shortcode_insert', function() {
                var insert_num = prompt(ed.getLang('gb_sk_shortcode.num_prompt'), '');
                if(insert_num !== null)
                    ed.execCommand("mceInsertContent", false, "[skeleton num="+insert_num+"]");
                else
                    ed.execCommand("mceInsertContent", false, "[skeleton]");
            });

            /*
             * Buttons
             */

            // Shortcode button
            ed.addButton("gb_sk_shortcode", {
                title : ed.getLang('gb_sk_shortcode.button_title'),
                image : url+"/../image/button.gif",
                onclick : function() {
                    ed.execCommand("gb_sk_shortcode_insert");
                }
            });
        },
        createControl : function(n, cm) {
            return null;
        },
        getInfo : function() {
            return {
                longname : ed.getLang('gb_sk_shortcode.plugin_desc')
            };
        }
    });
    tinymce.PluginManager.add('gb_sk_shortcode', tinymce.plugins.gb_sk_shortcode);
})();