var ToolsetCommon = ToolsetCommon || {};


ToolsetCommon.BootstrapCssComponentsTinyMCE = function($){
    
    var self = this,
    $bootstrap_components = Toolset_CssComponent.DDL_CSS_JS.available_components,
    $bootstrap_css = Toolset_CssComponent.DDL_CSS_JS.available_css,
    $other = Toolset_CssComponent.DDL_CSS_JS.other;
    
    self.init = function(){
        Toolset.hooks.addAction( 'toolset_text_editor_TinyMCE_init', function( instance ) {
            self.add_toggle_visibility_button( instance );
            self.add_bootstrap_components_buttons(instance);
            self.add_bootstrap_css_buttons(instance);
            self.add_other_buttons(instance);
            Toolset.hooks.doAction( 'bs_components_tinyMCE_divider', instance );
        });
    };

    self.add_toggle_visibility_button = function(instance){

        if(typeof tinymce !== 'object'){
            return;
        }

        tinymce.PluginManager.add('css_components_toolbar_toggle', function(editor, url){
            editor.addButton( 'css_components_toolbar_toggle', {
                icon: ' icon-bootstrap-original-logo ont-icon-25 css-components-toggle-icon',
                tooltip: Toolset_CssComponent.DDL_CSS_JS.toggle_button_tooltip,
                onclick: function() {
                    var $labels = jQuery('.toolset_qt_btn_group_labels'),
                        $container = $labels.closest('div.mce-toolbar');

                    $container.toggle();
                    ToolsetCommon.BSComponentsEventsHandler.update_tinyMCE_toggle_status($container);
                }
            });
        });

        _.defer(function(){
            var $icon = jQuery('.css-components-toggle-icon'),
                $button = $icon.parent(),
                $widget = jQuery('.css-components-toggle-icon').closest('div.mce-widget');

            $widget.addClass('css-components-toggle-div');

            $widget.css({"position": "relative"});
            $button.css({"position": "relative", "width" : "26px", "height" : "24px"});
        });

    };
    
    self.add_bootstrap_components_buttons = function(instance){
        
        if(typeof $bootstrap_components !== 'object'){
            return;
        }
        
        if(typeof tinymce !== 'object'){
            return;
        }
        
        jQuery.each( $bootstrap_components, function( index, value ){

            

            tinymce.PluginManager.add('css_components_'+index+'_button', function( editor, url ) {
                editor.addButton( 'css_components_'+index+'_button', {
                    icon: ' '+value.button_icon+' '+value.button_icon_size,
                    tooltip: value.name,
                    onclick: function() {
                        
                        Toolset.hooks.doAction( 'bs_components_open_dialog', {
                           name: value.name, 
                           description: value.description, 
                           url: value.url, 
                           button_icon: value.button_icon,
                           dialog_icon_size: value.dialog_icon_size,
                           bs_category: 'available_components',
                           bs_component_key: index,
                           editor_instance: instance,
                           buttons_type: 'tinymce'
                        });
                        
                    },
                    'class' : "toolset-components-buttons"
                });
            });


        });
    };
    
    self.add_bootstrap_css_buttons = function(instance){

        if(typeof $bootstrap_css !== 'object'){
            return;
        }
        if(typeof tinymce !== 'object'){
            return;
        }
        
        jQuery.each( $bootstrap_css, function( index, value ){

            tinymce.PluginManager.add('css_'+index+'_button', function( editor, url ) {
                editor.addButton( 'css_'+index+'_button', {
                    icon: ' '+value.button_icon+' '+value.button_icon_size,
                    tooltip: value.name,
                    onclick: function() {
                        
                        Toolset.hooks.doAction( 'bs_components_open_dialog', {
                           name: value.name, 
                           description: value.description, 
                           url: value.url, 
                           button_icon: value.button_icon,
                           dialog_icon_size: value.dialog_icon_size,
                           bs_category: 'available_css',
                           bs_component_key: index,
                           editor_instance: instance,
                           buttons_type: 'tinymce'
                        });

                    },
                    'class' : "toolset-components-buttons"
                });
            });
        });
    };
    
    self.add_other_buttons = function(instance){
        
        if(typeof $other !== 'object'){
            return;
        }
        if(typeof tinymce !== 'object'){
            return;
        }
        
        
        
        jQuery.each( $other, function( index, value ){

            tinymce.PluginManager.add('other_'+index+'_button', function( editor, url ) {
                editor.addButton( 'other_'+index+'_button', {
                    icon: ' '+value.button_icon+' '+value.button_icon_size,
                    tooltip: value.name,
                    onclick: function() {
                        
                        Toolset.hooks.doAction( 'bs_components_open_dialog', {
                           name: value.name, 
                           description: value.description, 
                           url: value.url, 
                           button_icon: value.button_icon,
                           dialog_icon_size: value.dialog_icon_size,
                           bs_category: 'other',
                           bs_component_key: index,
                           editor_instance: instance,
                           buttons_type: 'tinymce'
                        });

                    },
                    'class' : "toolset-components-buttons"
                });
            });
        });
        
        
    };

    self.init();
};


ToolsetCommon.BootstrapCssComponentsQuickTags = function($){
    
    var self = this,
    $bootstrap_components = Toolset_CssComponent.DDL_CSS_JS.available_components,
    $bootstrap_css = Toolset_CssComponent.DDL_CSS_JS.available_css,
    $other = Toolset_CssComponent.DDL_CSS_JS.other,
    $instance = null;
    
    
    
    self.init = function(){
        
        $instance = null;
        
        Toolset.hooks.addAction( 'toolset_text_editor_CodeMirror_init', function( get_instance ) {
            if(get_instance){ 
                $instance = get_instance;
                self.add_bootstrap_components_buttons($instance);
                
                var $my_buttons = self.generate_codemirror_bs_buttons($instance);
                self.wrap_codemirror_buttons($instance,$my_buttons);
            }
        });
        
        Toolset.hooks.addAction( 'toolset_text_editor_CodeMirror_init_only_buttons', function( get_instance ) {
            if(get_instance){ 
                $instance = get_instance;
                self.add_bootstrap_components_buttons($instance);
            }
        });
        
        
    };

    self.wrap_codemirror_buttons = function($instance,$buttons){
        
        
        jQuery("#qt_"+$instance+"_toolbar").after('<div class="bs-quicktags-toolbar code-editor-toolbar" id="codemirror-buttons-for-'+$instance+'">'+$buttons+'</div>');
        jQuery("#"+$instance).before('<div id="pop_'+$instance+'" class="pop pop_top_margin pop_right_margin pop_hidden"><a href="#" class="pop_close" data-pop_id="pop_'+$instance+'"><i class="glyphicon glyphicon-remove"></i></a><p class="pop_msg_p">'+Toolset_CssComponent.DDL_CSS_JS.codemirror_pop_message+'<br><br><label><input type="checkbox" id="hide_pop_'+$instance+'" name="hide_tooltip" value="hide_pop"> Dont show this tip again</label></p></div>');
        
        _.defer(function(){
            if(Toolset_CssComponent.DDL_CSS_JS.show_bs_buttons_cm_status === "no"){
                jQuery('#codemirror-buttons-for-'+$instance).hide();
                jQuery("#qt_"+$instance+"_bs_component_show_hide_button").val(Toolset_CssComponent.DDL_CSS_JS.button_toggle_show);
            } else {
                jQuery("#qt_"+$instance+"_bs_component_show_hide_button").val(Toolset_CssComponent.DDL_CSS_JS.button_toggle_hide);
            }
        });
            
        ToolsetCommon.BSComponentsEventsHandler.editor_notification_handler($instance);
    };
    
    self.generate_codemirror_bs_buttons = function(instance){
        
        var codemirror_buttons = '';
        // bs components
        codemirror_buttons += '<span class="toolset_qt_btn_group_labels">'+Toolset_CssComponent.DDL_CSS_JS.group_label_bs_components+'</span>';
        codemirror_buttons += '<ul class="js-wpv-filter-edit-toolbar" >';
        
        jQuery.each( $bootstrap_components, function( index, value ){
            codemirror_buttons +='<li class="js-editor-addon-button-wrapper">';
            codemirror_buttons +='<button class="button-secondary js-code-editor-toolbar-button js-codemirror-bs-component-button bs-'+index+'-button" data-bs_category="available_components" data-cm_instance="'+instance+'" data-bs_key="'+index+'" title="'+value.name+'" onclick="ToolsetCommon.BSComponentsEventsHandler.openBSDialog(this);">';
            codemirror_buttons +='<i class="'+value.button_icon+' bs-'+index+'-icon"></i>';
            codemirror_buttons +='</li>';
            
        });
        codemirror_buttons +='</ul>';
        
        // bs css
        codemirror_buttons += '<span class="toolset_qt_btn_group_labels">'+Toolset_CssComponent.DDL_CSS_JS.group_label_bs_css+'</span>';
        codemirror_buttons += '<ul class="js-wpv-filter-edit-toolbar">';
        
        jQuery.each( $bootstrap_css, function( index, value ){
            
            codemirror_buttons +='<li class="js-editor-addon-button-wrapper">';
            codemirror_buttons +='<button class="button-secondary js-code-editor-toolbar-button js-codemirror-bs-component-button bs-'+index+'-button" data-bs_category="available_css" data-cm_instance="'+instance+'" data-bs_key="'+index+'" title="'+value.name+'" onclick="ToolsetCommon.BSComponentsEventsHandler.openBSDialog(this);">';
            codemirror_buttons +='<i class="'+value.button_icon+' bs-'+index+'-icon"></i>';
            codemirror_buttons +='</li>';
            
        });
        codemirror_buttons +='</ul>';
        
        // other buttons
        if(typeof $other === 'object' && _.keys($other).length > 0 ){
            codemirror_buttons += '<span class="toolset_qt_btn_group_labels">'+Toolset_CssComponent.DDL_CSS_JS.group_label_other+'</span>';
        
            codemirror_buttons += '<ul class="js-wpv-filter-edit-toolbar">';

            jQuery.each( $other, function( index, value ){

                codemirror_buttons +='<li class="js-editor-addon-button-wrapper">';
                codemirror_buttons +='<button class="button-secondary js-code-editor-toolbar-button js-codemirror-bs-component-button bs-'+index+'-button" data-bs_category="other" data-cm_instance="'+instance+'" data-bs_key="'+index+'" title="'+value.name+'"  onclick="ToolsetCommon.BSComponentsEventsHandler.openBSDialog(this);">';
                codemirror_buttons +='<i class="'+value.button_icon+' bs-'+index+'-icon"></i>';
                codemirror_buttons +='</li>';

            });
            codemirror_buttons +='</ul>';
        }
        
        return codemirror_buttons;

    };
    
    
    
    self.add_bootstrap_components_buttons = function(instance){

        if(typeof $bootstrap_components !== 'object'){
            return;
        }
        // button toogle button :)
        if(jQuery('#qt_'+instance+'_bs_component_show_hide_button').length === 0){
            
            if(jQuery('#codemirror-buttons-for-'+$instance).is(":hidden") === true){
                var button_value = Toolset_CssComponent.DDL_CSS_JS.button_toggle_show;
            } else {
                var button_value = Toolset_CssComponent.DDL_CSS_JS.button_toggle_hide;
            }
            
            
            jQuery("#qt_"+instance+"_toolbar").append('<input type="button" id="qt_'+instance+'_bs_component_show_hide_button" class="ed_button button button-small" value="'+button_value+'">');
            jQuery( '#qt_'+instance+'_bs_component_show_hide_button' ).click(function() {
                Toolset.hooks.doAction( 'bs_components_toggle_buttons', instance );
            });
        }
       
    };
    

    self.init();
};
