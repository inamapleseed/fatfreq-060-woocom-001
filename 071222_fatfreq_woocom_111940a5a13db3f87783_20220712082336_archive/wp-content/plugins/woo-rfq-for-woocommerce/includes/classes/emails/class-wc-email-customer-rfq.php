<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists('WC_Email_Customer_RFQ') ) :

/**
 * Customer Processing RFQ Email
 *
 * An email sent to the customer when a new RFQ is received.
 * @extends     WC_Email
 */
class WC_Email_Customer_RFQ extends WC_Email {

	/**
	 * Constructor
	 */
	function __construct() {

		$this->id               = 'customer_rfq';
		$this->title            = __( 'RFQ-ToolKit New Request for Quote', 'woo-rfq-for-woocommerce' );
		$this->description      = __( 'This is an quote request notification sent to customers containing their order details after quote request.', 'woo-rfq-for-woocommerce' );

		$this->heading          = __( 'Thank you for your quote request', 'woo-rfq-for-woocommerce' );
		$this->subject          = __( 'Your {site_title} quote request confirmation from {order_date}', 'woo-rfq-for-woocommerce' );

		$this->template_html    = 'emails/customer-rfq.php';
		$this->template_plain   = 'emails/plain/customer-rfq.php';
		$this->_templates = array($this->template_html,$this->template_plain);

        $this->placeholders   = array(
            '{order_date}'   => '',
            '{order_number}' => '',
        );



		// Triggers for this email

        $this->customer_email = true;

		add_filter('woocommerce_template_directory',array( $this, 'gpls_rfq_woocommerce_locate_template_dir' ), 10, 2);

		if(!has_action('woocommerce_order_status_gplsquote-req_notification', array($this, 'trigger'))) {
            add_action('woocommerce_order_status_gplsquote-req_notification', array($this, 'trigger'));
        }



		// Call parent constructor
		parent::__construct();




	}



	public function gpls_rfq_woocommerce_locate_template_dir($dir,$template)
	{

			return $dir;

	}

	/**
	 * Trigger.
	 */
	function trigger( $order_id ) {

        if(defined("WC_Email_Customer_RFQ_EMAIL".$order_id))return;

        define("WC_Email_Customer_RFQ_EMAIL".$order_id,true);

        $this->setup_locale();

	    if ( $order_id ) {
			$this->object       = wc_get_order( $order_id );
			$this->recipient    = $this->object->get_billing_email();

            $this->placeholders['{order_date}']   = wc_format_datetime( $this->object->get_date_created() );
            $this->placeholders['{order_number}'] = $this->object->get_order_number();
            $this->placeholders['{order_billing_full_name}'] = $this->object->get_formatted_billing_full_name();

		}

        if ( $this->is_enabled() && $this->get_recipient() ) {
            $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
        }

        $this->restore_locale();



	}

    function replace_placeholders( $order_id ) {

        if(defined("WC_Email_Customer_RFQ_EMAIL".$order_id))return;

        define("WC_Email_Customer_RFQ_EMAIL".$order_id,true);

        require_once(WC()->plugin_path() . '/includes/emails/class-wc-email.php');
        require_once(WC()->plugin_path() . '/includes/class-wc-emails.php');
        $WC_Emails = WC_Emails::instance();


        $this->setup_locale();

        if ( $order_id ) {
            $this->object       = wc_get_order( $order_id );
            $this->recipient    = $this->object->get_billing_email();

            $this->placeholders['{order_date}']   = wc_format_datetime( $this->object->get_date_created() );
            $this->placeholders['{order_number}'] = $this->object->get_order_number();
            $this->placeholders['{order_billing_full_name}'] = $this->object->get_formatted_billing_full_name();

        }

        $this->restore_locale();

    }




	/**
	 * get_content_html function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_html() {


	    ob_start();


		wc_get_template( $this->template_html, array(
			'order'         => $this->object,
			'email_heading' => $this->get_heading(),
            'additional_content' => is_callable(array($this, 'get_additional_content'))?$this->get_additional_content():"",
			'sent_to_admin' => false,
			'plain_text'    => false,
            'email'			=> $this,
		) ,'',gpls_woo_rfq_DIR . 'woocommerce/' );
		//write_log(gpls_woo_rfq_WOO_PATH.$this->template_html);

		return ob_get_clean();
	}

	/**
	 * get_content_plain function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_plain() {

		ob_start();
		wc_get_template( $this->template_plain, array(
			'order'         => $this->object,
			'email_heading' => $this->get_heading(),
            'additional_content' => is_callable(array($this, 'get_additional_content'))?$this->get_additional_content():"",
			'sent_to_admin' => false,
			'plain_text'    => true,
            'email'			=> $this,
		),'',gpls_woo_rfq_DIR . 'woocommerce/'  );
		return ob_get_clean();
	}

    public function init_form_fields() {

        $this->form_fields = array(
            'enabled' => array(
                'title'         => __( 'Enable/Disable', 'woo-rfq-for-woocommerce' ),
                'type'          => 'checkbox',
                'label'         => __( 'Enable this email notification', 'woo-rfq-for-woocommerce' ),
                'default'       => 'yes'
            ),

            'subject' => array(
                'title'         => __( 'Subject', 'woo-rfq-for-woocommerce' ),
                'type'          => 'text',
                'description'   => sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'woo-rfq-for-woocommerce' ), $this->subject ),
                'placeholder'   => '',
                'default'       => ''
            ),
            'heading' => array(
                'title'         => __( 'Email Heading', 'woo-rfq-for-woocommerce' ),
                'type'          => 'text',
                'description'   => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'woo-rfq-for-woocommerce' ), $this->heading ),
                'placeholder'   => '',
                'default'       => ''
            ),
            'additional_content' => array(
                'title'       => __( 'Additional content', 'woo-rfq-for-woocommerce' ),
                'description' => __( 'Text to appear below the main email content.', 'woo-rfq-for-woocommerce' ),
                'css'         => 'width:400px; height: 75px;',
                'placeholder' => __( 'N/A', 'woo-rfq-for-woocommerce' ),
                'type'        => 'textarea',
                'default'     => '',
                'desc_tip'    => true,
            ),
            'email_type' => array(
                'title'         => __( 'Email type', 'woo-rfq-for-woocommerce' ),
                'type'          => 'select',
                'description'   => __( 'Choose which format of email to send.', 'woo-rfq-for-woocommerce' ),
                'default'       => 'html',
                'class'         => 'email_type wc-enhanced-select',
                'options'       => $this->get_email_type_options()
            )
        );
    }

}

endif;

return new WC_Email_Customer_RFQ();
