<?php
// No dirrect access
if ( ! defined( 'MYWORLDCLASS_VERSION' ) ) exit;

/**
 * 
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_load_authorize_net' ) ) :
	function mywclass_load_authorize_net() {

		if ( class_exists( 'AuthorizeNetARB' ) ) return;

		require_once MYWORLDCLASS_GATEWAYS_DIR . 'authorize-net/lib/autoload.php';

	}
endif;

/**
 * 
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_authorize_net_subscription' ) ) :
	function mywclass_authorize_net_subscription( $data = array(), $plan = array() ) {

		$prefs = mywclass_get_settings();
		$now   = current_time( 'timestamp' );

		$api_key   = $prefs['authorizenet']['live_api'];
		$trans_key = $prefs['authorizenet']['live_key'];
		if ( $prefs['authorizenet']['mode'] == 'test' ) {
			$api_key   = $prefs['authorizenet']['test_api'];
			$trans_key = $prefs['authorizenet']['test_key'];
		}

		define( "AUTHORIZENET_API_LOGIN_ID",    $api_key );
		define( "AUTHORIZENET_TRANSACTION_KEY", $trans_key );

		if ( $prefs['authorizenet']['mode'] == 1 )
			define( "AUTHORIZENET_SANDBOX", true );

		mywclass_load_authorize_net();

		$subscription                           = new AuthorizeNet_Subscription;
		$subscription->name                     = $plan['description'];
		$subscription->trialOccurrences         = 1;
		$subscription->trialAmount              = $plan['trial'];
		$subscription->intervalLength           = 1;
		$subscription->intervalUnit             = 'months';
		$subscription->startDate                = date( 'Y-m-d', $now );
		$subscription->totalOccurrences         = $plan['occurences'];
		$subscription->amount                   = number_format( $plan['cost'], 2, '.', '' );
		$subscription->creditCardCardNumber     = $data['card'];
		$subscription->creditCardExpirationDate = $data['exp_yy'] . '-' . $data['exp_mm'];
		$subscription->creditCardCardCode       = $data['cvv'];
		$subscription->billToFirstName          = $data['first_name'];
		$subscription->billToLastName           = $data['last_name'];

		$subscription->billToAddress = $data['address1'];
		$subscription->billToCity    = $data['city'];
		$subscription->billToZip     = $data['zip'];
		$subscription->billToState   = $data['state'];
		$subscription->billToCountry = 'US';

		$request  = new AuthorizeNetARB;
		$request->setRefId( $data['payment_id'] );

		$response = $request->createSubscription( $subscription );
		if ( $response->isOk() )
			return $response->getSubscriptionId();

		return array(
			'errors' => $response->getErrorMessage()
		);

	}
endif;

/**
 * 
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_authorize_net_charge' ) ) :
	function mywclass_authorize_net_charge( $data = array(), $plan = array() ) {

		$prefs = mywclass_get_settings();
		$now   = current_time( 'timestamp' );

		$api_key   = $prefs['authorizenet']['live_api'];
		$trans_key = $prefs['authorizenet']['live_key'];
		if ( $prefs['authorizenet']['mode'] == 'test' ) {
			$api_key   = $prefs['authorizenet']['test_api'];
			$trans_key = $prefs['authorizenet']['test_key'];
		}

		define( "AUTHORIZENET_API_LOGIN_ID",    $api_key );
		define( "AUTHORIZENET_TRANSACTION_KEY", $trans_key );

		if ( $prefs['authorizenet']['mode'] == 'test' )
			define( "AUTHORIZENET_SANDBOX", true );
		else
			define( "AUTHORIZENET_SANDBOX", false );

		mywclass_load_authorize_net();

		$sale           = new AuthorizeNetAIM;
		$sale->amount   = number_format( $plan['cost'], 2, '.', '' );
		$sale->card_num = $data['card'];
		$sale->exp_date = $data['exp_mm'] . '/' . substr( $data['exp_yy'], 2 );

		$sale->setCustomField( 'payment_id', $data['payment_id'] );

		$response = $sale->authorizeAndCapture();

		if ( $response->approved )
		    return $response->transaction_id;

		return array(
			'errors' => $response->response_reason_text
		);

	}
endif;

/**
 * 
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_authorize_net_cancel_subscription' ) ) :
	function mywclass_authorize_net_cancel_subscription( $subscription_id = NULL ) {

		$prefs = mywclass_get_settings();
		$now   = current_time( 'timestamp' );

		$api_key   = $prefs['authorizenet']['live_api'];
		$trans_key = $prefs['authorizenet']['live_key'];
		if ( $prefs['authorizenet']['mode'] == 'test' ) {
			$api_key   = $prefs['authorizenet']['test_api'];
			$trans_key = $prefs['authorizenet']['test_key'];
		}

		define( "AUTHORIZENET_API_LOGIN_ID",    $api_key );
		define( "AUTHORIZENET_TRANSACTION_KEY", $trans_key );

		if ( $prefs['authorizenet']['mode'] == 'test' )
			define( "AUTHORIZENET_SANDBOX", true );
		else
			define( "AUTHORIZENET_SANDBOX", false );

		mywclass_load_authorize_net();

		$request = new AuthorizeNetARB;

		$cancellation = $request->cancelSubscription( $subscription_id );

		if ( $cancellation->isOk() )
			return true;

		return $cancellation->response;

	}
endif;

?>