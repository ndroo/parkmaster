<?php

include "config.php";
include "Twilio/Twilio.php";

//timezone
date_default_timezone_set(TIME_ZONE);

/***************************/
//STREET PARKING RULES (Ellsworth ave Toronto) RULES
/***************************/
//South side
//NO PARKING
//16th to end of month - april 1st to nov 30th
//CAN PARK ALL OTHER TIMES
/***************************/
//North side 
//NO PARKING
//dec 1st to march 31st
//1st to 15th of each month - april 1 - nov 30th
//CAN PARK ALL OTHER TIMES
/***************************/

//get day and month so we can determine if parking is allowed
@$day = date("d",time());
@$month = date("m",time());

//assume we have no idea where to park
$sides = array();
$reasons = array();

//NORTH CONDITIONS
//NO PARKING: Dec 1st to March 31st
if($month >= 12 && $month <= 3)
{
	$sides['NORTH']['reason'] = "No parking Dec 1 to March 31";
	$sides['NORTH']['state'] = "NO_PARKING";
}
//NO PARKING: 1st to 15th of the month, April 1 - Nov 30th
//cant park on the north side
if(($day >= 1 && $day <= 15) && ($month >= 4 && $month <= 11))
{
	$sides['NORTH']['reason'] = "No parking 1st to 15th of each month, April 1 to Nov 30";
	$sides['NORTH']['state'] = "NO_PARKING";
}

//positive can park case (if canpark is null, then we can assume its safe to park there)
if($sides['NORTH']['state'] != "NO_PARKING")
{
	//no rule match, can park!
	$sides['NORTH']['reason'] = "No restructions during this time.";
	$sides['NORTH']['state'] = "UNRESTRICTED";
}

//SOUTH CONDITIONS
//NO PARKING: 16th to end of month, April 1 - Nov 30th?
if(($day >= 16) && ($month >= 4 && $month <= 11))
{
	$sides['SOUTH']['reason'] = "No parking 16th to end of month, April 1 to Nov 30";
	$sides['SOUTH']['state'] = "NO_PARKING";
}


//positive can park case (if canpark is null, then we can assume its safe to park there)
if($sides['SOUTH']['state'] != "NO_PARKING")
{
	$sides['SOUTH']['reason'] = "No restrictions during this time.";
	$sides['SOUTH']['state'] = "UNRESTRICTED";
}

//json format result
$result = json_encode($sides);
print_r($sides);

//message those who want to be messaged!
$provider = "TWILIO";
$client = new Services_Twilio(TWILIO_SID, TWILIO_TOKEN);
$from = TWILIO_NUMBER;
$send_to[] = "+1-647-225-4909";

//start to build out the message
$not_allowed = "";
$allowed = "";
if($sides['SOUTH']['state'] == "UNRESTRICTED")
{
	$allowed .= "- South\n";
}
else
{
	$not_allowed .= "- South (" . $sides['SOUTH']['reason'] . ")\n";
}

if($sides['NORTH']['state'] == "UNRESTRICTED")
{
	$allowed .= "- North\n";
}
else
{
	$not_allowed .= "- North (" . $sides['NORTH']['reason'] . ")\n";
}

//construct the entire message
@$date = date("n/M/Y",time());
$body = "TicketHero reminder: $date\n\nAllowed:\n$allowed\nNOT allowed:\n$not_allowed\nHappy Parking!";
$count = strlen($body);
echo "Message Length: $count\n";
echo "*****************************************\n";
echo $body . "\n";
echo "*****************************************\n";

//send the messsages!
echo "MESSAGE SENDING STARTED\n";
foreach($send_to as $to)
{
	$client->account->sms_messages->create($from, $to, $body);
	echo "sent to: $to\n";
}
echo "MESSAGE SENDING COMPLETED\n";
echo "SHUTTING DOWN\n";
