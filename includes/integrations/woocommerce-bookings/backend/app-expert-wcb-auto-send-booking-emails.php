<?php
class APP_EXPERT_WCB_Auto_Send_Email {

    private $_current_integration;
    public function __construct(App_Expert_Integration $_current_integration){
        $this->_current_integration = $_current_integration;
        add_filter( 'woocommerce_email_classes', array( $this, 'init_emails' ) );
		add_action( 'woocommerce_booking_cancelled', array($this,'send_email_customer_cancel_booking'), 10, 2);
    }

    	/**
	 * Include our mail templates
	 *
	 * @param  array $emails
	 * @return array
	 */
	function init_emails( $emails ) {
		if ( ! isset( $emails['WC_Email_Customer_Booking_Cancelled'] ) ) {
			require_once $this->_current_integration->get_current_path()."backend/emails/app-expert-wcb-email-customer-booking-cancelled.php";
            $emails['WC_Email_Customer_Booking_Cancelled'] = new  APP_EXPERT_WCB_Email_Customer_Booking_Cancelled($this->_current_integration);
		}
		return $emails;
	}

	function send_email_customer_cancel_booking ($booking_id, $booking)
	{
        $customer_cancel_booking = get_post_meta($booking_id,"customer_cancel_booking",true);
		if($customer_cancel_booking){
			$mailer   = WC()->mailer();
			$reminder = $mailer->emails['WC_Email_Customer_Booking_Cancelled'];
			$reminder->trigger( $booking_id );
		}
	}
}