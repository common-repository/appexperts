<?php

/**
 * Booking is cancelled
 *
 * An email sent to the admin when a booking is cancelled.
 *
 * @class   WC_Email_Customer_Booking_Cancelled
 * @extends WC_Email
 */
class  APP_EXPERT_WCB_Email_Customer_Booking_Cancelled extends WC_Email {

	/**
	 * Constructor
	 */
    private $_current_integration;
    public function __construct(App_Expert_Integration $_current_integration){
        $this->_current_integration = $_current_integration;
        $this->id             = 'booking_cancelled_by_customer';
		$this->title          = __( 'Booking Cancelled By Customer', 'woocommerce-bookings' );
		$this->description    = __( 'Booking cancelled emails are sent when the status of a booking goes to cancelled.', 'woocommerce-bookings' );

		$this->heading        = __( 'Booking Cancelled By Customer' , 'woocommerce-bookings' );
		$this->subject        = __( '[{blogname}] A booking of {product_title} has been cancelled By Customer', 'woocommerce-bookings' );
		$this->template_html  = 'emails/customer-cancel-booking.php';
		$this->template_plain = 'emails/plain/customer-cancel-booking.php';

		// Call parent constructor
        parent::__construct();
        //todo:fix the path
        $this->template_base = $this->_current_integration->get_current_path()."templates/";
		// Other settings
		$this->recipient     = $this->get_option( 'recipient', get_option( 'admin_email' ) );
	}

	/**
	 * trigger function.
	 */
	public function trigger( $booking_id ) {
		if ( $booking_id ) {
			$this->object = get_wc_booking( $booking_id );

			if ( ! is_object( $this->object ) || ! $this->object->get_order() ) {
				return;
			}

			if ( $this->object->has_status( 'in-cart' ) ) {
				return;
			}

			foreach ( array( '{product_title}', '{order_date}', '{order_number}' ) as $key ) {
				$key = array_search( $key, $this->find );

				if ( false !== $key ) {
					unset( $this->find[ $key ] );
					unset( $this->replace[ $key ] );
				}
			}

			if ( $this->object->get_product() ) {
				$this->find[]    = '{product_title}';
				$this->replace[] = $this->object->get_product()->get_title();
			}

			if ( $this->object->get_order() ) {
				if ( version_compare( wc()->version, '3.0', '<' ) ) {
					$order_date = $this->object->get_order()->order_date;
				} else {
					$order_date = $this->object->get_order()->get_date_created() ? $this->object->get_order()->get_date_created()->date( 'Y-m-d H:i:s' ) : '';
				}
				$this->find[]    = '{order_date}';
				$this->replace[] = date_i18n( wc_bookings_date_format(), strtotime( $order_date ) );

				$this->find[]    = '{order_number}';
				$this->replace[] = $this->object->get_order()->get_order_number();
			} else {
				$this->find[]    = '{order_date}';
				$this->replace[] = date_i18n( wc_bookings_date_format(), strtotime( $this->object->booking_date ) );

				$this->find[]    = '{order_number}';
				$this->replace[] = __( 'N/A', 'woocommerce-bookings' );
			}

			if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
				return;
			}

			$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		}
	}

	/**
	 * get_content_html function.
	 *
	 * @access public
	 * @return string
	 */
	public function get_content_html() {
		ob_start();
		wc_get_template( $this->template_html, array(
			'booking'       => $this->object,
			'email_heading' => $this->get_heading(),
			'email'         => $this,
		), 'app-experts-wc-booking-apis/' , $this->template_base );
		return ob_get_clean();
	}

	/**
	 * get_content_plain function.
	 *
	 * @access public
	 * @return string
	 */
	public function get_content_plain() {
		ob_start();
		wc_get_template( $this->template_plain, array(
			'booking'       => $this->object,
			'email_heading' => $this->get_heading(),
			'email'         => $this,
		), 'app-experts-wc-booking-apis/' , $this->template_base );
		return ob_get_clean();
	}

	/**
	 * Initialise Settings Form Fields
	 *
	 * @access public
	 *
	 * @return void
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'   => __( 'Enable/Disable', 'woocommerce-bookings' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable this email notification', 'woocommerce-bookings' ),
				'default' => 'yes',
			),
			'recipient' => array(
				'title'       => __( 'Recipient(s)', 'woocommerce-bookings' ),
				'type'        => 'text',
				/* translators: %s: admin email */
				'description' => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to <code>%s</code>.', 'woocommerce-bookings' ), esc_attr( get_option( 'admin_email' ) ) ),
				'placeholder' => '',
				'default'     => '',
			),
			'subject' => array(
				'title'       => __( 'Subject', 'woocommerce-bookings' ),
				'type'        => 'text',
				/* translators: %s: subject */
				'description' => sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'woocommerce-bookings' ), $this->subject ),
				'placeholder' => '',
				'default'     => '',
			),
			'heading' => array(
				'title'       => __( 'Email Heading', 'woocommerce-bookings' ),
				'type'        => 'text',
				/* translators: %s: heading */
				'description' => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'woocommerce-bookings' ), $this->heading ),
				'placeholder' => '',
				'default'     => '',
			),
			'email_type' => array(
				'title'       => __( 'Email type', 'woocommerce-bookings' ),
				'type'        => 'select',
				'description' => __( 'Choose which format of email to send.', 'woocommerce-bookings' ),
				'default'     => 'html',
				'class'       => 'email_type',
				'options'     => array(
					'plain'     => __( 'Plain text', 'woocommerce-bookings' ),
					'html'      => __( 'HTML', 'woocommerce-bookings' ),
					'multipart' => __( 'Multipart', 'woocommerce-bookings' ),
				),
			),
		);
	}
}
