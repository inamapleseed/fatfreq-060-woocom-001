<?php
/**
* The admin-specific functionality of the plugin.
*
* @link       ofb
* @since      1.0.0
*
* @package    Ofb
* @subpackage Ofb/admin
*/

/**
* The admin-specific functionality of the plugin.
*
* Defines the plugin name, version, and two examples hooks for how to
* enqueue the admin-specific stylesheet and JavaScript.
*
* @package    Ofb
* @subpackage Ofb/admin
* @author     ORION <help@orionorigin.com>
*/
class Ofb_Admin {

  /**
  * The ID of this plugin.
  *
  * @since    1.0.0
  * @access   private
  * @var      string    $plugin_name    The ID of this plugin.
  */
  private $plugin_name;
  private $version;

  public function __construct($plugin_name, $version) {

    $this->plugin_name = $plugin_name;
    $this->version = $version;
  }
  /**
  *  Set  admin styles
  */
  public function enqueue_styles() {
    if ( is_vpc_admin_screen() ) {
      wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/ofb-admin.css', array(), $this->version, 'all');
      wp_enqueue_style('o-ui', plugin_dir_url(__FILE__) . 'css/UI.css', array(), $this->version, 'all');
      wp_enqueue_style('o-ui', plugin_dir_url(__FILE__) . 'css/UI.min.css', array(), $this->version, 'all');
      wp_enqueue_style('o-tooltip', plugin_dir_url(__FILE__) . 'css/tooltip.min.css', array(), $this->version, 'all');
      wp_enqueue_style('o-bs-modal-css', plugin_dir_url(__FILE__) . 'css/modal/modal.min.css', array(), $this->version, 'all');
      wp_enqueue_style('o-flexgrid', plugin_dir_url(__FILE__) . 'css/flexiblegs.css', array(), $this->version, 'all');
      wp_enqueue_style('template', plugin_dir_url(__FILE__) . 'css/template.css', array(), $this->version, 'all');
    }
  }

  /**
  * set admin script
  */
  public function enqueue_scripts() {
    if ( is_vpc_admin_screen() ) {
      wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/ofb-admin.js', array('jquery', 'o-admin'), $this->version, false);
      wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/modal.js', array('jquery', 'o-admin'), $this->version, false);
      wp_enqueue_script('o-admin', plugin_dir_url(__FILE__) . 'js/o-admin.js', array('jquery', 'jquery-ui-sortable'), $this->version, false);
      wp_enqueue_script('o-modal-js', plugin_dir_url(__FILE__) . 'js/modal/modal.min.js', array('jquery'), false, false);
    }
  }

  /**
  * Runs the new version check and upgrade process
  * @return \Vpc_Ofb_Updater
  */
  function get_updater() {
    if(class_exists( 'vpc' )){
      do_action( 'vpc_before_init_updater' );
      require_once( VPC_OFB_DIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'updaters' . DIRECTORY_SEPARATOR . 'class-vpc-ofb-updater.php' );
      $updater = new Vpc_Ofb_Updater();
      $updater->init();
      require_once( VPC_OFB_DIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'updaters' . DIRECTORY_SEPARATOR . 'class-vpc-ofb-updating-manager.php' );
      $updater->setUpdateManager( new Vpc_Ofb_Updating_Manager( VPC_OFB_VERSION, $updater->versionUrl(), VPC_OFB_MAIN_FILE ) );
      do_action( 'vpc_after_init_updater' );
      return $updater;
    }
  }

  function add_global_settings($global_settings){
    $begin = array(
      'type' => 'sectionbegin',
      'id' => 'vpc-ofb-container',
      'table' => 'options',
    );

    $end = array('type' => 'sectionend');

    $license_key = array(
      'title' => __( 'Form builder add on license key', 'vpc' ),
      'name' => 'vpc-options[purchase-code-form-builder-add-on]',
      'type' => 'text',
      'desc' => __( 'This is the licence key you received after your purchase.<a href=“https://configuratorsuiteforwp.com/guide/how-to-add-my-license/” target=“_blank”>Learn more</a>. <a href=“https://configuratorsuiteforwp.com/my-account/orders/“ target=“blank”>Where can I find my license key</a>?', 'vpc' ),
        'default' => '',
      );

      array_push($global_settings,$begin);
      array_push($global_settings,$license_key);
      array_push($global_settings,$end);
      return $global_settings;
    }

    /**
    *  create ofb custom post type
    */
    public function register_ofb() {

      $labels = array(
        'name' => _x('All Forms', 'ofb'),
        'singular_name' => _x('Form', 'ofb'),
        'menu_name' => __('Form settings', 'ofb'),
        'parent_item_colon' => __('Parent Form:', 'ofb'),
        'all_items' => __('All Forms', 'ofb'),
        'view_item' => __('View Forms', 'ofb'),
        'add_new_item' => __('Add New Form', 'ofb'),
        'add_new' => __('Add New', 'ofb'),
        'edit_item' => __('Edit Form', 'ofb'),
        'update_item' => __('Update Form', 'ofb'),
        'search_items' => __('Search Form', 'ofb'),
        'not_found' => __('Not found', 'ofb'),
        'not_found_in_trash' => __('Not found in Trash', 'ofb'),
      );
      $args = array(
        'label' => __('ofb', 'ofb'),
        'description' => __('Form settings', 'ofb'),
        'labels' => $labels,
        'supports' => array('title'),
        'hierarchical' => false,
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => false,
        'show_in_nav_menus' => false,
        'show_in_admin_bar' => true,
        'menu_position' => 9,
        'menu_icon' => 'dashicons-tickets-alt',
        'can_export' => true,
        'has_archive' => false,
        'exclude_from_search' => false,
        'publicly_queryable' => false,
        'rewrite' => false,
        'capability_type' => 'post',
      );
      register_post_type('ofb', $args);
    }

    /**
    * get ofb columns
    * @param array $columns
    * @return int
    */
    public function get_ofb_screen_layout_columns($columns) {
      $columns['ofb'] = 1;
      return $columns;
    }

    /**
    *
    * @return int
    */
    public function get_ofb_config_screen_layout() {
      return 1;
    }

    /**
    * Reorder ofb metax box
    * @param array $order
    * @return string
    */
    public function metabox_order($order) {
      $order['advanced'] = 'slugdiv,ofb_box,submitdiv';
      return $order;
    }

    /**
    * add ofb meta box
    */
    public function meta_box_ofb() {
      add_meta_box(
        'ofb_box', __('Form settings', 'ofb'), (array($this, 'ofb_post_repeatable_field')), 'ofb'
      );
    }

    /**
    * create form fields
    * @global type $o_row_templates
    * @param type $post
    */
    public function ofb_post_repeatable_field($post) {
      ?>
      <div class='block-form'>
        <?php
        $begin = array(
          'type' => 'sectionbegin',
          'id' => 'ofb-config-container',
        );

        $type = array(
          'title' => __('Type', 'ofb'),
          'name' => 'type',
          'type' => 'select',
          'options' => array(
            'text' => 'Text',
            'textarea' => 'Textarea',
            'radio' => 'Radio',
            'checkbox' => 'Checkbox',
            'select' => 'Select',
            'email' => 'Email',
            'file' => 'File',
            // 'url' => 'URL',
            // 'tel' => 'Tel',
            // 'number' => 'Number',
            // 'date' => 'Date',
          ),
          'class' => 'ofb_types',
          'id' => 'ofb_types',
          'default' => 'text',
        );

        $length = array(
          'title' => __('Length', 'ofb'),
          'name' => 'length',
          'type' => 'number',
          'class' => 'ofb-length',
        );

        $label = array(
          'title' => __('Label', 'ofb'),
          'name' => 'label',
          'type' => 'text',
          'class' => 'ofb-label',
        );
        $name = array(
          'title' => __('Name', 'ofb'),
          'name' => 'name',
          'type' => 'text',
          'class' => 'ofb-name',
        );

        $required = array(
          'title' => __('Required', 'ofb'),
          'name' => 'required',
          'type' => 'radio',
          'options' => array(
            'Yes' => 'Yes',
            'No' => 'No',
          ),
          'default' => 'Yes',
          'class' => 'ofb-required',
        );

        $price = array(
          'title' => __('Price', 'ofb'),
          'name' => 'price',
          'type' => 'number',
          'class' => 'ofb-price',
          'custom_attributes' => array('step' => 'any'),
        );
        $option_price = array(
          'title' => __('Price', 'ofb'),
          'name' => 'option_price',
          'type' => 'number',
          'class' => 'ofb-option_price',
          'custom_attributes' => array('step' => 'any'),
        );

        $option_name = array(
          'title' => __('Label', 'ofb'),
          'name' => 'label',
          'type' => 'text',
          'class' => 'ofb-option-name',
        );

        $option_value = array(
          'title' => __('Option value', 'ofb'),
          'name' => 'op_value',
          'type' => 'text',
          'class' => 'ofb-option-value',
        );

        $option_default = array(
          'title' => __('Default', 'ofb'),
          'name' => 'default',
          'type' => 'radio',
          'options' => array(1 => ''),
          'class' => 'default-config',
          'tip' => 'yes',
        );

        $options = array(
          'title' => __('Options', 'ofb'),
          'name' => 'options',
          'type' => 'repeatable-fields',
          'class' => 'manage',
          'id' => 'manage',
          'fields' => array($option_name, $option_value, $option_default, $option_price),
          'desc' => __('Field options', 'ofb'),
          'row_class' => 'ofb-option-row',
          'popup' => true,
          'popup_button' => __('Manage options', 'ofb'),
          'popup_title' => __('Options', 'ofb'),
          'add_btn_label' => __('Add option', 'ofb'),
        );

        $components = array(
          'title' => __('Components', 'ofb'),
          'name' => 'ofb[components]',
          'type' => 'repeatable-fields',
          'id' => 'ofb-config-components-table',
          'fields' => array($type, $label, $name, $required, $length, $price, $options),
          'ignore_desc_col' => true,
          'class' => 'striped',
          'add_btn_label' => __('Add field', 'ofb'),
        );

        $end = array('type' => 'sectionend');
        $settings = array(
          $begin,
          $components,
          $end,
        );
        echo o_admin_fields($settings);
        global $o_row_templates;
        ?>
      </div>
      <script>
      var o_rows_tpl =<?php echo wp_json_encode($o_row_templates); ?>;
      </script>

      <?php
    }

    /**
    *  save ofb form post type
    * @param type $post_id
    */
    public function save_post_callback($post_id) {
      $meta_key = 'ofb';
      if (isset($_POST[$meta_key])) {
        $form_datas = $_POST[$meta_key];
        foreach($form_datas['components'] as $key=>$components ){
          if(isset($components['name']) && empty($components['name'])){
            $label=$components['label'];
            if(empty($label)){
              $label = $components['type'];
              $form_datas['components'][$key]['label'] = $label;
            }
            $form_datas['components'][$key]['name']=sanitize_title($label).'_'.$key;
          }
          if($components['type']=="checkbox" || $components['type']=="radio"){
            foreach($components['options'] as $opt_key=>$options){
              if((isset($options['op_value']) && empty($options['op_value'])) && (isset($options['label']) && empty($options['label']))){
                $form_datas['components'][$key]['options'][$opt_key]['label']= $opt_key;
                $form_datas['components'][$key]['options'][$opt_key]['op_value']= $opt_key;
              }
              else if((isset($options['op_value']) && empty($options['op_value'])) && (isset($options['label']) && !empty($options['label'])))
              $form_datas['components'][$key]['options'][$opt_key]['op_value']=$options['label'].'_'.$opt_key;
              else if((isset($options['op_value']) && !empty($options['op_value'])) && (isset($options['label']) && empty($options['label'])))
              $form_datas['components'][$key]['options'][$opt_key]['label']=$options['op_value'].'_'.$opt_key;

            }
          }
        }
        update_post_meta($post_id, $meta_key, $form_datas);
      }
    }

    /**
    * get max input vars
    */
    public function get_max_input_vars_php_ini() {
      $total_max_normal = ini_get('max_input_vars');
      $msg = __("Your max input var is <strong>$total_max_normal</strong> but this page contains <strong>{nb}</strong> fields. You may experience a lost of data after saving. In order to fix this issue, please increase <strong>the max_input_vars</strong> value in your php.ini file.", 'ofb');
      ?>

      <script type="text/javascript">
      var o_max_input_vars = <?php echo esc_html($total_max_normal); ?>;
      var o_max_input_msg = "<?php echo esc_html($msg); ?>";
      </script>
      <?php
    }

  }
