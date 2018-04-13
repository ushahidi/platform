<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Twilio SMS Callback Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataProvider\Twilio
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class Controller_Sms_Twilio extends Controller {

	/**
	 * Handle incoming SMS from Twilio
	 */
	public function action_reply()
	{
    //Check if data provider is available
    $providers_available = Kohana::$config->load('features.data-providers');

    if ( !$providers_available['twilio'] )
    {
      throw HTTP_Exception::factory(403, 'The Twilio data source is not currently available. It can be accessed by upgrading to a higher Ushahidi tier.');
    }


		if ($this->request->method() != 'POST')
		{
			// Only POST is allowed
			throw HTTP_Exception::factory(405, 'The :method method is not supported. Supported methods are :allowed_methods', array(
					':method'          => $this->request->method(),
					':allowed_methods' => Http_Request::POST,
				))
				->allowed(Http_Request::POST);
		}

		$provider = DataProvider::factory('twilio');

		// Authenticate the request
		$options = $provider->options();
		if ($this->request->post('AccountSid') !== $options['account_sid'])
		{
			throw HTTP_Exception::factory(403, 'Incorrect or missing AccountSid');
		}

		// Remove Non-Numeric characters because that's what the DB has
		$to = preg_replace("/[^0-9,.]/", "", $this->request->post('To'));
		$from  = preg_replace("/[^0-9,.]/", "", $this->request->post('From'));

		$message_text = $this->request->post('Body');
		$message_sid  = $this->request->post('MessageSid');

		// Check if a form id is already associated with this data provider
		$additional_data = [];
		if (isset($options['form_id'])) {
			$additional_data['form_id'] = $options['form_id'];
			$additional_data['inbound_fields'] = isset($options['inbound_fields']) ? $options['inbound_fields'] : NULL;
		}

		// @todo use other info from twillio, ie: location, media
		$provider->receive(Message_Type::SMS, $from, $message_text, $to, $date = NULL, NULL, $message_sid, $additional_data);

		// If we have an auto response configured, return the response messages
		if (! empty($options['sms_auto_response']))
		{
			$body = View::factory('sms_response')
				->set('response', $options['sms_auto_response'])
				->render();
			// Set the correct content-type header
			$this->response->headers('Content-Type', 'text/xml');
			$this->response->body($body);
		}
	}
}
