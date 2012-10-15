<?php

class Services_Twilio_Rest_Calls
    extends Services_Twilio_ListResource
{

    public static function isApplicationSid($value)
    {
        return strlen($value) == 34
            && !(strpos($value, "AP") === false);
    }

    public function create($from, $to, $url, array $params = array())
    {
        $params["From"] = $from;
        $params["To"] = $to;

        if (self::isApplicationSid($url))
            $params["ApplicationSid"] = $url;
        else
            $params["Url"] = $url;

        return parent::_create($params);
    }
}
