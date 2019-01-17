<?php

require_once(DIR_SYSTEM . "stripe/vendor/autoload.php");

class ControllerPaymentStripe extends Controller {
	
	public function fetch() {
		if($_POST['stripeToken'] && $_POST['stripeEmail'] && $_POST['amount']) {
			$success = true;
			$message = '';
			
			try {
				$token = $_POST['stripeToken'];
				$email = $_POST['stripeEmail'];
				$amount = $_POST['amount'];
				
				$stripe = array(
				  "secret_key"      => 'sk_test_ZO68j5YyjIBIEPu0lEv6HanM',
				  "publishable_key" => 'pk_test_DlH51ukUhK0mAJKiBL1BB3xT'
				);

				\Stripe\Stripe::setApiKey($stripe['secret_key']);	
				
				$customer = \Stripe\Customer::create(array(
					'email' => $email,
					'source'  => $token
				));
				
				$charge = \Stripe\Charge::create(array(
					'customer' => $customer->id,
					'amount'   => $amount,
					'currency' => 'usd'
				));
				
			} catch(Exception $e) {
				$sucess = false;
				$message = $e->getMessage();
			}
			
			$response = array(
				'success' => $success,
				'message' => $message
			);
				
			$this->api->sendResponse(200, $response);
			
		} else{
			$response = array(
				'success' => false,
				'message' => 'data invalid, have you post stripeToken, stripeEmail and amount?'
			);
			
			$this->api->sendResponse(200, $response);
		}
    }
}