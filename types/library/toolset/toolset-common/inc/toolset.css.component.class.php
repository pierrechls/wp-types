<?php
/*
 * This class will take care of loading bootstrap components buttons and custom buttons added by user
 * Buttons will be visible on pages defined in $this->allowed_pages()
 * 
 * since: Layouts 1.8
 */
if ( ! class_exists( 'Toolset_CssComponent' ) ) {

    Class Toolset_CssComponent{


        private static $instance;

        const BOOTSTRAP_CSS_COMPONENTS_DOC_BASE = 'http://getbootstrap.com/components/';
        const BOOTSTRAP_CSS_DOC_BASE = 'http://getbootstrap.com/css/';

        function __construct() {
                      
            add_action( 'admin_init', array( $this, 'admin_init' ) );
            add_action('init', array($this, 'load_dialog_boxes'));
            
            add_filter('toolset_add_registered_script', array(&$this, 'add_register_scripts'));
            add_filter('toolset_add_registered_styles', array(&$this, 'add_register_styles'));
            add_action('admin_print_scripts', array(&$this, 'admin_enqueue_scripts'));  
            
            add_action( 'wp_ajax_toolset_bs_update_option', array($this, 'toolset_bs_update_option') );
        }
        
        public static function getInstance() {
            if( !self::$instance ) {
                self::$instance = new Toolset_CssComponent();
            }
            
            return self::$instance;
        }

        function add_toggle_button($buttons)
        {
            if(wp_script_is( 'quicktags' )) {
                array_push($buttons, 'css_components_toolbar_toggle');
            }

            return $buttons;
        }
        
        public function add_register_scripts($scripts){           
            $scripts['toolset-css-component-buttons']	= new Toolset_Script( 'toolset-css-component-buttons', TOOLSET_COMMON_URL . "/res/js/toolset-bs-component-buttons.js", array('jquery'), false );
            $scripts['toolset-css-component-events']	= new Toolset_Script( 'toolset-css-component-events', TOOLSET_COMMON_URL . "/res/js/toolset-bs-component-events.js", array('jquery'), true );
            return $scripts;
        }

        public function add_register_styles($styles){
            $styles['toolset-bs-component-style']	= new Toolset_Style( 'toolset-bs-component-style', TOOLSET_COMMON_URL . '/res/css/toolset-bs-component.css', array(), TOOLSET_VERSION );
            $styles['glyphicons']					= new Toolset_Style( 'glyphicons', TOOLSET_COMMON_URL. '/res/lib/glyphicons/css/glyphicons.css', array(), '3.3.5', 'screen' );
            return $styles;
        }
        
        public function toolset_bs_update_option(){
            if($_POST['option'] && isset($_POST['option']) && $_POST['value'] && isset($_POST['value'])){
                
                $option_name = 'toolset_bs_component_'.sanitize_text_field( $_POST['option'] );
                $value = ($_POST['value'] === "true") ? "yes" : "no";
                
                update_option( $option_name, $value);
            }
            echo get_option( $option_name ); 
            
            wp_die();
        }
        
        public function admin_enqueue_scripts()
        {
            
            if(!$this->is_allowed_page()){
                return;
            }
            
            do_action('toolset_enqueue_styles', array(
                'toolset-bs-component-style',
                'wp-jquery-ui-dialog',
                'ddl-dialogs-css',
                'glyphicons'
            ));
            
            $get_components = $this->all_css_components();
            
            do_action('toolset_enqueue_scripts', array(
                'toolset-css-component-buttons',
                'toolset-css-component-events'
            ));

            do_action('toolset_localize_script', 'toolset-css-component-events', 'Toolset_CssComponent', array(
                    'DDL_CSS_JS' => array(
                        'available_css' => $get_components['css'],
                        'button_toggle_show' => __('Show Bootstrap buttons','ddl-layouts'),
                        'button_toggle_hide' => __('Hide Bootstrap buttons','ddl-layouts'),
                        'group_label_bs_components' => __('Bootstrap Elements:','ddl-layouts'),
                        'group_label_bs_css' => __('Bootstrap CSS:','ddl-layouts'),
                        'group_label_other' => __('Other Elements:','ddl-layouts'),
                        'codemirror_pop_message' => __('Got markup for "<span class="bs_pop_element_name_codemirror">Element</span>"? Now paste it into the editor.','ddl-layouts'),
                        'tinymce_pop_message' => __('Got markup for "<span class="bs_pop_element_name_tinymce">Element</span>"? Before you paste HTML into the editor, <br>remember to switch to HTML editing. Then, paste the code into the editor.','ddl-layouts'),
                        'available_components' => $get_components['components'],
                        'other' => $get_components['other'],
                        'hide_editor_pop_msg' => (get_option( 'toolset_bs_component_hide_pop_msg' )) ? get_option( 'toolset_bs_component_hide_pop_msg' ) : "no",
                        'show_bs_buttons_cm_status' => (get_option( 'toolset_bs_component_show_buttons_cm_status' )) ? get_option( 'toolset_bs_component_show_buttons_cm_status' ) : "no",
                        'show_bs_buttons_tinymce_status' => (get_option( 'toolset_bs_component_show_buttons_tinymce_status' )) ? get_option( 'toolset_bs_component_show_buttons_tinymce_status' ) : "no",
                        'toggle_button_label_toggle' => __('Toggle Bootstrap Components', "toolset-common"),
                        'toggle_button_tooltip' => __('Bootstrap Components Toggle', "toolset-common"),
                    ),
                )
            );
        }
        
        
        // list of allowed pages (only there we will load buttons)
        public function allowed_pages(){
            return array('dd_layouts_edit','views-editor','ct-editor','view-archives-editor');
        }
        
        // check is allowed page currently loaded
        public function is_allowed_page(){
            
            $bootstrap_available = false;
            
            $bootstrap_version_option = get_option( 'wpv_options' );
            
            if(isset($bootstrap_version_option["wpv_bootstrap_version"]) && $bootstrap_version_option["wpv_bootstrap_version"] != "-1"){
                $bootstrap_available = true;
            }
            
            if(defined('LAYOUTS_PLUGIN_NAME')){
                $bootstrap_available = true;
            }

            $get_page = (isset($_GET['page']) && $_GET['page']) ? sanitize_text_field( $_GET['page'] ) : null;
            
            if(!in_array($get_page, $this->allowed_pages()) || $bootstrap_available === false){
                return false;
            } else {
                return true;
            }
        }
        

        function admin_init(){
                 
            if(!$this->is_allowed_page()){
                return;
            }

            // register buttons for tinyMCE
            add_filter( 'mce_external_plugins', array($this,'toolset_tinymce_register_buttons') );
            add_filter( "mce_buttons_3", array( $this, "register_buttons_editor_components" ) );
            add_filter( "mce_buttons_3", array( $this, "register_buttons_editor_css" ) );
            add_filter( "mce_buttons_3", array( $this, "register_buttons_other" ) );
            add_filter('mce_buttons', array(&$this, 'add_toggle_button') );
        }

        function load_dialog_boxes(){
                     
            $dialogs = array();
            $dialogs[] = new WPDDL_CssComponenetDialog( array('toolset_page_views-editor','toolset_page_dd_layouts_edit','toolset_page_view-archives-editor','toolset_page_ct-editor') );
            
            foreach( $dialogs as &$dialog ){
                add_action('current_screen', array(&$dialog, 'init_screen_render') );
            }
            return $dialogs;
        }

        /*
         * TinyMCE buttons
         */

        public function toolset_tinymce_register_buttons(){
            
            if(wp_script_is( 'quicktags' )) {
                //enqueue TinyMCE plugin script with its ID.
                $get_components = $this->all_css_components();
                foreach($get_components['components'] as $key=>$value){
                    $plugin_array["css_components_".$key."_button"] = TOOLSET_COMMON_URL . "/res/js/toolset-bs-component-buttons.js";
                }
                foreach($get_components['css'] as $key=>$value){
                    $plugin_array["css_".$key."_button"] = TOOLSET_COMMON_URL . "/res/js/toolset-bs-component-buttons.js";
                }
                
                foreach($get_components['other'] as $key=>$value){
                    $plugin_array["other_".$key."_button"] = TOOLSET_COMMON_URL . "/res/js/toolset-bs-component-buttons.js";
                }

                $plugin_array['css_components_toolbar_toggle'] = TOOLSET_COMMON_URL . "/res/js/toolset-bs-component-buttons.js";
            }

            return $plugin_array;

        }
        
        function register_buttons_editor_components( $buttons ) {
            if(wp_script_is( 'quicktags' )) {
                //register buttons with their id.
                $get_components = $this->all_css_components();
                foreach($get_components['components'] as $key=>$value){
                    array_push( $buttons, "css_components_".$key."_button" );
                }

            }

            return $buttons;
        }

        function register_buttons_editor_css($buttons){
            if(wp_script_is( 'quicktags' )) {
                //register buttons with their id.
                $get_components = $this->all_css_components();
                foreach($get_components['css'] as $key=>$value){
                    array_push( $buttons, "css_".$key."_button" );
                }			
            }

            return $buttons;
        }

        function register_buttons_other($buttons){
            if(wp_script_is( 'quicktags' )) {
                //register buttons with their id.
                $get_components = $this->all_css_components();
                foreach($get_components['other'] as $key=>$value){
                    array_push( $buttons, "other_".$key."_button" );
                }

            }

            return $buttons;
        }

        /////////////////

        function preload_styles(){
            
            do_action('toolset_enqueue_styles', array(
                'toolset-bs-component-style',
                'wp-jquery-ui-dialog',
                'ddl-dialogs-css',
                'glyphicons'
            ));
        }



        public function get_extra_editor_buttons(){
            $additional_buttons = apply_filters('extra_editor_buttons', array());
            return $additional_buttons;
        }

        // list of all standard components
        public function all_css_components(){
          
            return array(
                "components"=>
                    array(
                        'glyphicons' => array(
                            'url' => self::BOOTSTRAP_CSS_COMPONENTS_DOC_BASE.'#glyphicons',
                            'description' => __('Includes over 250 glyphs in font format from the Glyphicon Halflings set. Glyphicons Halflings are normally not available for free, but their creator has made them available for Bootstrap free of cost.','ddl-layouts'),
                            'button_icon' => ' glyphicon glyphicon-plus-sign',
                            'button_icon_size' => 'ont-icon-18',
                            'dialog_icon_size' => 'ont-icon-72',
                            'name' => __('Glyphicons','ddl-layouts')
                        ),
                        'dropdowns' => array(
                            'url' => self::BOOTSTRAP_CSS_COMPONENTS_DOC_BASE.'#dropdowns',
                            'description' => __('Toggleable, contextual menu for displaying lists of links. Made interactive with the dropdown JavaScript plugin.','ddl-layouts'),
                            'button_icon' => ' glyphicon glyphicon-list-alt',
                            'button_icon_size' => 'ont-icon-18',
                            'dialog_icon_size' => 'ont-icon-72',
                            'name' => __('Dropdowns','ddl-layouts')
                        ),
                        'button_groups' => array(
                            'url' => self::BOOTSTRAP_CSS_COMPONENTS_DOC_BASE.'#btn-groups',
                            'description' => __('Group a series of buttons together on a single line with the button group. Add on optional JavaScript radio and checkbox style behavior with our buttons plugin.','ddl-layouts'),
                            'button_icon' => ' glyphicon glyphicon-tasks ',
                            'button_icon_size' => 'ont-icon-18',
                            'dialog_icon_size' => 'ont-icon-72',
                            'name' => __('Button groups','ddl-layouts')
                        ),
                        'button_dropdowns' => array(
                            'url' => self::BOOTSTRAP_CSS_COMPONENTS_DOC_BASE.'#btn-dropdowns',
                            'description' => __('Use any button to trigger a dropdown menu by placing it within a .btn-group and providing the proper menu markup.','ddl-layouts'),
                            'button_icon' => ' glyphicon glyphicon-th-list ',
                            'button_icon_size' => 'ont-icon-18',
                            'dialog_icon_size' => 'ont-icon-72',
                            'name' => __('Button dropdowns','ddl-layouts')
                        ),
                        'input_groups' => array(
                            'url' => self::BOOTSTRAP_CSS_COMPONENTS_DOC_BASE.'#input-groups',
                            'description' => __('Extend form controls by adding text or buttons before, after, or on both sides of any text-based input.','ddl-layouts'),
                            'button_icon' => ' icon-input-groups ',
                            'button_icon_size' => 'ont-icon-13',
                            'dialog_icon_size' => 'ont-icon-72',
                            'name' => __('Input groups','ddl-layouts')
                        ),
                        'navs' => array(
                            'url' => self::BOOTSTRAP_CSS_COMPONENTS_DOC_BASE.'#nav',
                            'description' => __('Navs available in Bootstrap have shared markup, starting with the base .nav class, as well as shared states. Swap modifier classes to switch between each style.','ddl-layouts'),
                            'button_icon' => ' glyphicon glyphicon-object-align-top ',
                            'button_icon_size' => 'ont-icon-18',
                            'dialog_icon_size' => 'ont-icon-72',
                            'name' => __('Navs','ddl-layouts')
                        ),
                        'navbar' => array(
                            'url' => self::BOOTSTRAP_CSS_COMPONENTS_DOC_BASE.'#navbar',
                            'description' => __('Navbars are responsive meta components that serve as navigation headers for your application or site.','ddl-layouts'),
                            'button_icon' => ' icon-navbar ',
                            'button_icon_size' => 'ont-icon-18',
                            'dialog_icon_size' => 'ont-icon-72',
                            'name' => __('Navbar','ddl-layouts')
                        ),
                        'breadcrumbs' => array(
                            'url' => self::BOOTSTRAP_CSS_COMPONENTS_DOC_BASE.'#breadcrumbs',
                            'description' => __('Indicate the current page\'s location within a navigational hierarchy.','ddl-layouts'),
                            'button_icon' => ' icon-breadcrumbs ',
                            'button_icon_size' => 'ont-icon-5',
                            'dialog_icon_size' => 'ont-icon-30',
                            'name' => __('Breadcrumbs','ddl-layouts')
                        ),
                        'pagination' => array(
                            'url' => self::BOOTSTRAP_CSS_COMPONENTS_DOC_BASE.'#pagination',
                            'description' => __('Provide pagination links for your site or app with the multi-page pagination component.','ddl-layouts'),
                            'button_icon' => ' icon-bootstrap-pagination ',
                            'button_icon_size' => 'ont-icon-6',
                            'dialog_icon_size' => 'ont-icon-30',
                            'name' => __('Pagination','ddl-layouts')
                        ),
                        'labels' => array(
                            'url' => self::BOOTSTRAP_CSS_COMPONENTS_DOC_BASE.'#labels',
                            'description' => __('Provide different kind of labels for your website.','ddl-layouts'),
                            'button_icon' => ' icon-labels ',
                            'button_icon_size' => 'ont-icon-18',
                            'dialog_icon_size' => 'ont-icon-72',
                            'name' => __('Labels','ddl-layouts')
                        ),
                        'badges' => array(
                            'url' => self::BOOTSTRAP_CSS_COMPONENTS_DOC_BASE.'#badges',
                            'description' => __('Easily highlight new or unread items by adding a <span class="badge"> to links, Bootstrap navs, and more.','ddl-layouts'),
                            'button_icon' => ' glyphicon glyphicon-certificate ',
                            'button_icon_size' => 'ont-icon-18',
                            'dialog_icon_size' => 'ont-icon-72',
                            'name' => __('Badges','ddl-layouts')
                        ),
                        'page_header' => array(
                            'url' => self::BOOTSTRAP_CSS_COMPONENTS_DOC_BASE.'#page-header',
                            'description' => __('A simple shell for an h1 to appropriately space out and segment sections of content on a page. It can utilize the h1\'s default small element, as well as most other components (with additional styles).','ddl-layouts'),
                            'button_icon' => ' glyphicon glyphicon-header ',
                            'button_icon_size' => 'ont-icon-18',
                            'dialog_icon_size' => 'ont-icon-72',
                            'name' => __('Page header','ddl-layouts')
                        ),
                        'thumbnails' => array(
                            'url' => self::BOOTSTRAP_CSS_COMPONENTS_DOC_BASE.'#thumbnails',
                            'description' => __('Extend Bootstrap\'s grid system with the thumbnail component to easily display grids of images, videos, text, and more.','ddl-layouts'),
                            'button_icon' => ' glyphicon glyphicon-picture ',
                            'button_icon_size' => 'ont-icon-18',
                            'dialog_icon_size' => 'ont-icon-72',
                            'name' => __('Thumbnails','ddl-layouts')
                        ),
                        'alerts' => array(
                            'url' => self::BOOTSTRAP_CSS_COMPONENTS_DOC_BASE.'#alerts',
                            'description' => __('Provide contextual feedback messages for typical user actions with the handful of available and flexible alert messages.','ddl-layouts'),
                            'button_icon' => ' glyphicon glyphicon-warning-sign ',
                            'button_icon_size' => 'ont-icon-18',
                            'dialog_icon_size' => 'ont-icon-72',
                            'name' => __('Alerts','ddl-layouts')
                        ),
                        'progress_bars' => array(
                            'url' => self::BOOTSTRAP_CSS_COMPONENTS_DOC_BASE.'#progress',
                            'description' => __('Provide up-to-date feedback on the progress of a workflow or action with simple yet flexible progress bars.','ddl-layouts'),
                            'button_icon' => ' glyphicon glyphicon-tasks ',
                            'button_icon_size' => 'ont-icon-18',
                            'dialog_icon_size' => 'ont-icon-72',
                            'name' => __('Progress bars','ddl-layouts')
                        ),
                        'media_object' => array(
                            'url' => self::BOOTSTRAP_CSS_COMPONENTS_DOC_BASE.'#media',
                            'description' => __('Abstract object styles for building various types of components (like blog comments, Tweets, etc) that feature a left- or right-aligned image alongside textual content.','ddl-layouts'),
                            'button_icon' => ' glyphicon glyphicon-film ',
                            'button_icon_size' => 'ont-icon-18',
                            'dialog_icon_size' => 'ont-icon-72',
                            'name' => __('Media object','ddl-layouts')
                        ),
                        'list_group' => array(
                            'url' => self::BOOTSTRAP_CSS_COMPONENTS_DOC_BASE.'#list-group',
                            'description' => __('List groups are a flexible and powerful component for displaying not only simple lists of elements, but complex ones with custom content.','ddl-layouts'),
                            'button_icon' => ' icon-list-group ',
                            'button_icon_size' => 'ont-icon-18',
                            'dialog_icon_size' => 'ont-icon-72',
                            'name' => __('List group','ddl-layouts')
                        ),
                        'panels' => array(
                            'url' => self::BOOTSTRAP_CSS_COMPONENTS_DOC_BASE.'#panels',
                            'description' => __('While not always necessary, sometimes you need to put your DOM in a box. For those situations, try the panel component.','ddl-layouts'),
                            'button_icon' => ' icon-panels ',
                            'button_icon_size' => 'ont-icon-18',
                            'dialog_icon_size' => 'ont-icon-72',
                            'name' => __('Panels','ddl-layouts')
                        ),
                        'responsive_embed' => array(
                            'url' => self::BOOTSTRAP_CSS_COMPONENTS_DOC_BASE.'#responsive-embed',
                            'description' => __('Allow browsers to determine video or slideshow dimensions based on the width of their containing block by creating an intrinsic ratio that will properly scale on any device.','ddl-layouts'),
                            'button_icon' => ' glyphicon glyphicon-resize-full ',
                            'button_icon_size' => 'ont-icon-18',
                            'dialog_icon_size' => 'ont-icon-72',
                            'name' => __('Responsive embed','ddl-layouts')
                        ),
                        'wells' => array(
                            'url' => self::BOOTSTRAP_CSS_COMPONENTS_DOC_BASE.'#wells',
                            'description' => __('Use the well as a simple effect on an element to give it an inset effect.','ddl-layouts'),
                            'button_icon' => ' icon-wells ',
                            'button_icon_size' => 'ont-icon-18',
                            'dialog_icon_size' => 'ont-icon-72',
                            'name' => __('Wells','ddl-layouts')
                        ),
                    ),
                "css"=> 
                    array(
                        'grid_system' => array(
                            'url' => self::BOOTSTRAP_CSS_DOC_BASE.'#grid',
                            'description' => __('Bootstrap includes a responsive, mobile first fluid grid system that appropriately scales up to 12 columns as the device or viewport size increases','ddl-layouts'),
                            'button_icon' => ' glyphicon glyphicon-th ',
                            'button_icon_size' => 'ont-icon-18',
                            'dialog_icon_size' => 'ont-icon-72',
                            'name' => __('Grid system','ddl-layouts')
                        ),
                        'typography' => array(
                            'url' => self::BOOTSTRAP_CSS_DOC_BASE.'#type',
                            'description' => __('Bootstrap includes simple and easily customized typography for headings, body text, lists, and more.','ddl-layouts'),
                            'button_icon' => ' glyphicon glyphicon-text-height ',
                            'button_icon_size' => 'ont-icon-18',
                            'dialog_icon_size' => 'ont-icon-72',
                            'name' => __('Typography','ddl-layouts')
                        ),
                        'code' => array(
                            'url' => self::BOOTSTRAP_CSS_DOC_BASE.'#code',
                            'description' => __('Bootstrap includes styling for different types of text quotes. This includes code, blockquotes, variables, and others.','ddl-layouts'),
                            'button_icon' => ' glyphicon glyphicon-console ',
                            'button_icon_size' => 'ont-icon-18',
                            'dialog_icon_size' => 'ont-icon-72',
                            'name' => __('Code','ddl-layouts')
                        ),
                        'tables' => array(
                            'url' => self::BOOTSTRAP_CSS_DOC_BASE.'#tables',
                            'description' => __('Bootstrap offers different styling options for tables, including different spacing of cells, contextual classes and responsive tables.','ddl-layouts'),
                            'button_icon' => ' glyphicon glyphicon-th-large ',
                            'button_icon_size' => 'ont-icon-18',
                            'dialog_icon_size' => 'ont-icon-72',
                            'name' => __('Tables','ddl-layouts')
                        ),
                        'forms' => array(
                            'url' => self::BOOTSTRAP_CSS_DOC_BASE.'#forms',
                            'description' => __('Bootstrap provides several form control styles, layout options, and custom components for creating a wide variety of forms.','ddl-layouts'),
                            'button_icon' => ' glyphicon glyphicon-check ',
                            'button_icon_size' => 'ont-icon-18',
                            'dialog_icon_size' => 'ont-icon-72',
                            'name' => __('Forms','ddl-layouts')
                        ),
                        'buttons' => array(
                            'url' => self::BOOTSTRAP_CSS_DOC_BASE.'#buttons',
                            'description' => __('Bootstrap offers different classes for styling different types of buttons as well as to indicate the different states. ','ddl-layouts'),
                            'button_icon' => ' icon-buttons ',
                            'button_icon_size' => 'ont-icon-11',
                            'dialog_icon_size' => 'ont-icon-60',
                            'name' => __('Buttons','ddl-layouts')
                        ),
                        'images' => array(
                            'url' => self::BOOTSTRAP_CSS_DOC_BASE.'#images',
                            'description' => __('Bootstrap provides classes to make your images responsive and adds lightweight styles to them.','ddl-layouts'),
                            'button_icon' => ' glyphicon glyphicon-picture ',
                            'button_icon_size' => 'ont-icon-18',
                            'dialog_icon_size' => 'ont-icon-72',
                            'name' => __('Images','ddl-layouts')
                        ),
                        'helper_classes' => array(
                            'url' => self::BOOTSTRAP_CSS_DOC_BASE.'#helper-classes',
                            'description' => __('Bootstrap features a wide array of different helper classes to help you. These include clearfix, contextual colors and backgrounds, content showing and hiding, and others.','ddl-layouts'),
                            'button_icon' => ' glyphicon glyphicon-question-sign ',
                            'button_icon_size' => 'ont-icon-18',
                            'dialog_icon_size' => 'ont-icon-72',
                            'name' => __('Helper classes','ddl-layouts')
                        ),
                        'responsive_utilities' => array(
                            'url' => self::BOOTSTRAP_CSS_DOC_BASE.'#responsive-utilities',
                            'description' => __('Bootstrap features additional responsive utilities for faster mobile-friendly development. These classes can be used for showing and hiding content by device via media query. ','ddl-layouts'),
                            'button_icon' => ' glyphicon glyphicon-phone ',
                            'button_icon_size' => 'ont-icon-18',
                            'dialog_icon_size' => 'ont-icon-72',
                            'name' => __('Responsive utilities','ddl-layouts')
                        ),
                    ),
                "other"=> $this->get_extra_editor_buttons()

            );
        }

        

    }

    class WPDDL_CssComponenetDialog extends Toolset_DialogBoxes{
      
        function __construct( $screens ){
            parent::__construct( $screens );
        }
        
        
        public function template(){
            ob_start();?>

            <script type="text/html" id="ddl-bootstrap-info-dialog-tpl">
                <div id="js-dialog-dialog-container">
                    <div class="ddl-dialog-content" id="js-dialog-content-dialog">
                        <div id="components-dialog-content">

                            <div class="toolset-bs-components-dialog-sides">
                                <div class="toolset-bs-components-dialog-sides-left">
                                    <i class="{{{icon}}} {{{dialog_icon_size}}}"></i>
                                </div>
                                <div class="toolset-bs-components-dialog-sides-right">
                                    <strong>{{{description}}}</strong><br>
                                    <p class="toolset-bs-button-p">
                                        
                                        <a href="{{{url}}}" target="_blank" class="button toolset-bs-componenet-check-button" data-bs_category="{{{bs_component_category}}}" data-buttons_type="{{{buttons_type}}}" data-editor_instance="{{{editor_instance}}}" data-bs_key="{{{bs_component_key}}}" onclick="ToolsetCommon.BSComponentsEventsHandler.editor_notification(this);">
                                            <?php _e('Get Bootstrap element','ddl-layouts');?>
                                        </a>
                                        
                                        <br>
                                        <small><?php _e('You will go to the official Bootstrap documentation, explaining all the options and CSS classes to create a {{title}}');?></small>
                                    </p>
                                </div>
                                <div class="toolset-bs-components-dialog-sides-bottom">
                                    <hr>
                                    <p class="toolset-bs-button-p">
                                        <a href="<?php _e('https://wp-types.com/user-guides/using-bootstrap-css-elements-content?utm_source=layoutsplugin&utm_campaign=layouts&utm_medium=bootstrap-components&utm_term=help-link','ddl-layouts');?>" target="_blank"><?php _e('Learn how to use Bootstrap in layouts','ddl-layouts');?></a>
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </script>
            <?php
            echo ob_get_clean();
        }
    }
}

