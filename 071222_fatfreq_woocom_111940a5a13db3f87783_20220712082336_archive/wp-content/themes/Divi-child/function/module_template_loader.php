<?php
add_action( 'et_builder_ready', 'et_builder_initialize_divi_modules' );

function et_builder_initialize_divi_modules() {
    if ( ! class_exists( 'ET_Builder_Module' ) ) { return; }
   
      //custom get layout files
    function get_layout_list(){
        $dir = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'layouts/*';//dirname dirname is to get the parent
        $fileList = glob($dir);
        //Loop through the array that glob returned.
        $list = array();
        foreach($fileList as $file){
            $name = pathinfo($file,PATHINFO_FILENAME); 
            $list[$name] = esc_html__( ucwords(str_replace('_', ' ',$name)), 'et_builder' ) ;
           /*$list = array_merge($list, array(
                    $name => esc_html__( ucwords(str_replace('_', ' ',$name)), 'et_builder' ) 
                ));*/

        }
        return $list;
    }
    //custom get shortcode params
    function get_layout_params( $args, $args_str='' ){
        $args_list = array_map('trim', explode(',', $args_str ));//explose args
        if (!empty($args_list)) {
            foreach ($args_list as $list) {
                $explode = array_map('trim', explode('=', $list));
                $index   = $explode[0];
                $value   = (isset($explode[1]))?$explode[1]:'';
                if (!empty($index)) {// eliminate space or null index
                    $args = array_merge($args, array($index => $value));//convert to array index
                }
            }
        }
        return $args;
    }


    class et_builder_Builder_Module_TemplateLoader extends ET_Builder_Module {
        function init() {
            $this->name       = esc_html__( 'Template Loader', 'et_builder' );
            $this->slug       = 'et_builder_pb_template_loader';
            $this->vb_support      = 'on';
            $this->use_row_content = true;
            $this->decode_entities = true;

            $this->settings_modal_toggles = array(
                'general'  => array(
                    'toggles' => array(
                        'main_content' => esc_html__( 'FCS Module', 'et_builder' ),
                    ),
                ),
                'advanced' => array(
                    'toggles' => array(
                        'width' => array(
                            'title'    => esc_html__( 'Sizing', 'et_builder' ),
                            'priority' => 65,
                        ),
                    ),
                ),
            );

            $this->advanced_fields = array(
                'borders'               => array(
                    'default' => false,
                ),
                'margin_padding' => array(
                    'css' => array(
                        'important' => array( 'custom_margin' ), // needed to overwrite last module margin-bottom styling
                    ),
                ),
                'text_shadow'           => array(
                    // Don't add text-shadow fields since they already are via font-options
                    'default' => false,
                ),
                'box_shadow'            => array(
                    'default' => false,
                ),
                'fonts'                 => false,
                'button'                => false,
            );

            $this->help_videos = array(
                array(
                    'id'   => esc_html( 'dTY6-Cbr00A' ),
                    'name' => esc_html__( 'An introduction to the Code module', 'et_builder' ),
                ),
            );
            add_filter( 'no_texturize_shortcodes', array( $this, 'disable_wptexturize' ) );
            
        }

        function get_fields() {
            $fields = array(
                'template' => array(
                    'label'           => esc_html__( 'Template', 'et_builder' ),
                    'type'            => 'select',
                    'option_category' => 'basic_option',
                    'options'         => get_layout_list(),
                    'description' => esc_html__( 'Select file template on layouts.', 'et_builder' ),
                ),
                'template_args' => array(
                    'label'           => esc_html__( 'Optional Arguments ', 'et_builder' ),
                    'type'            => 'text',
                    'option_category' => 'basic_option',
                    'description'     => esc_html__( 'Separate by comma for shortcode argument. Separate by equal for value Example: post_id=16,taxonomy=post_category ...', 'et_builder')
                ),
            );
            return $fields;
        }

        function render( $attrs, $content = null, $render_slug ) {
            //custom
            $content = '';
            if(isset($attrs['template'])){
                $layout  = array( 'layout' => $attrs['template'] );
                $params  = isset($attrs['template_args'])?$attrs['template_args']:'';
                $args    = get_layout_params($layout,$params);
                $content = get_layouts($args);
            }
            // custom
            $video_background          = $this->video_background();
            $parallax_image_background = $this->get_parallax_image_background();

            // Module classnames
            $this->add_classname( $this->get_text_orientation_classname() );

            $output = sprintf(
                '<div%2$s class="%3$s">
                    %5$s
                    %4$s
                    <div class="et_pb_code_inner">
                        %1$s
                    </div> <!-- .et_pb_code_inner -->
                </div> <!-- .et_pb_code -->',
                $content,//overide $this->content
                $this->module_id(),
                $this->module_classname( $render_slug ),
                $video_background,
                $parallax_image_background
            );

            return $output;
        }



    }

    new et_builder_Builder_Module_TemplateLoader;


    class ET_Builder_Module_Fullwidth_TemplateLoader extends ET_Builder_Module {
        function init() {
            $this->name            = esc_html__( 'Fullwidth Template Loader', 'et_builder' );
            $this->slug            = 'et_pb_fullwidth_code';
            $this->vb_support      = 'on';
            $this->fullwidth       = true;
            $this->use_row_content = true;
            $this->decode_entities = true;

            $this->settings_modal_toggles = array(
                'general'  => array(
                    'toggles' => array(
                        'main_content' => esc_html__( 'FCS Module', 'et_builder' ),
                    ),
                ),
            );

            $this->advanced_fields = array(
                'borders'     => array(
                    'default' => false,
                ),
                'text_shadow' => array(
                    // Don't add text-shadow fields since they already are via font-options
                    'default' => false,
                ),
                'box_shadow'  => array(
                    'default' => false,
                ),
                'fonts'       => false,
                'button'      => false,
            );

            $this->help_videos = array(
                array(
                    'id'   => esc_html( 'dTY6-Cbr00A' ),
                    'name' => esc_html__( 'An introduction to the Fullwidth Code module', 'et_builder' ),
                ),
            );

            // wptexturize is often incorrectly parsed single and double quotes
            // This disables wptexturize on this module
            add_filter( 'no_texturize_shortcodes', array( $this, 'disable_wptexturize' ) );
        }

        function get_fields() {
            $fields = array(
                'template' => array(
                    'label'           => esc_html__( 'Template', 'et_builder' ),
                    'type'            => 'select',
                    'option_category' => 'basic_option',
                    'options'         => get_layout_list(),
                    'description' => esc_html__( 'Select file template on layouts.', 'et_builder' ),
                ),
                'template_args' => array(
                    'label'           => esc_html__( 'Optional Arguments ', 'et_builder' ),
                    'type'            => 'text',
                    'option_category' => 'basic_option',
                    'description'     => esc_html__( 'Separate by comma for shortcode argument. Separate by equal for value Example: post_id=16,taxonomy=post_category ...', 'et_builder')
                ),
            );
            return $fields;
        }

        function render( $attrs, $content = null, $render_slug ) {
            //custom
            $layout  = array( 'layout' => $attrs['template'] );
            $args    = get_layout_params($layout,$attrs['template_args']);
            $content = get_layouts($args);
            // custom
            $video_background          = $this->video_background();
            $parallax_image_background = $this->get_parallax_image_background();

            // Module classnames
            $this->add_classname( $this->get_text_orientation_classname() );

            $output = sprintf(
                '<div%2$s class="%3$s">
                    %5$s
                    %4$s
                    <div class="et_pb_code_inner">
                        %1$s
                    </div> <!-- .et_pb_code_inner -->
                </div> <!-- .et_pb_code -->',
                $content,//overide $this->content
                $this->module_id(),
                $this->module_classname( $render_slug ),
                $video_background,
                $parallax_image_background
            );

            return $output;
        }
    }

    new ET_Builder_Module_Fullwidth_TemplateLoader;

}