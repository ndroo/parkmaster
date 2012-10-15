<?php

class Services_Twilio_Rest_SmsMessages
extends Services_Twilio_ListResource
{
	public function getSchema()
	{
		return array(
				'class' => 'Services_Twilio_Rest_SmsMessages',
				'basename' => 'SMS/Messages',
				'instance' => 'Services_Twilio_Rest_SmsMessage',
				'list' => 'sms_messages',
				);
	}

	function create($from, $to, $body, $provider = "TWILIO", array $params = array())
	{
		if($provider == "TWILIO")
		{
			//custom verelo code by a.mcgrath to ensure we try Twilio first then use tropo for SMS
			try
			{
				return $this->sendTwilio($from, $to, $body, $params);
			}
			catch(Exception $ex)
			{
				if($this->sendTropo($from,$to,$body,$params))
					return "OK";
				else
					throw new Exception("Failed to send SMS");
			}

		}
		else if($provider == "TROPO")
		{
			//try tropo first
			if($this->sendTropo($from,$to,$body,$params))
			{
				return "OK";
			}
			else
			{
				try
				{
					return $this->sendTwilio($from, $to, $body, $params);
				}
				catch(Exception $ex)
				{
					throw new Exception("Failed to send SMS");

				}
			}

		}
	}

	private function sendTropo($from,$to,$body, array $params = array())
	{
		$result = false;
		try
		{
			//deliver by tropo
			$tropo_body = urlencode($body);
			//tropo doesnt want +'s in front of everything
			//$tropo_to = str_replace("+","",$to);
			//hard coded the token as this function is used by the api and app, didnt want to spread it in more than 1 place
			$tropo_to = $to;
			$tropo_to = str_replace("-","",$tropo_to); //remove -'s
			$tropo_to = str_replace(" ","",$tropo_to); //remove spaces
			$tropo_to = urlencode($tropo_to);
			$url = "https://api.tropo.com/1.0/sessions?action=create&token=1292bc767d51f64ba0dff8d6d118fa2a344a1a3cda36da702bf08bdf3294f9169a9d66581305d7f1f948fc63&numberToDial=$tropo_to&msg=$tropo_body";
			$result = @file_get_contents($url);
		}
		catch(Exception $ex)
		{
			//will return false;
		}

		return $result;
	}

	private function sendTwilio($from, $to, $body, array $params = array())
	{
		return parent::_create(array(
					'From' => $from,
					'To' => $to,
					'Body' => $body
					) + $params);
	}
}
