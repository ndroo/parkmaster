<?php


/***************************/
//STREET PARKING RULES (Ellsworth street Toronto) RULES
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
$sides = array("SOUTH" => null,"NORTH" => null);
$reasons = array();

//NORTH CONDITIONS
//NO PARKING: Dec 1st to March 31st
if($month >= 12 && $month <= 3)
{
	$sides['NORTH']['reason'] = "No parking Dec 1st to March 31st";
	$sides['NORTH']['parking_allowed'] = false;
}
//NO PARKING: 1st to 15th of the month, April 1 - Nov 30th
//cant park on the north side
if(($day >= 1 && $day <= 15) && ($month >= 4 && $month <= 11))
{
	$sides['NORTH']['reason'] = "No parking 1st to 15th of each month, April 1 to Nov 30th";
	$side['NORTH']['parking_allowed'] = false;
}

//positive can park case (if canpark is null, then we can assume its safe to park there)
if($sides['NORTH'] == null)
{
	//no rule match, can park!
	$sides['NORTH']['parking_allowed'] = true;
	$sides['NORTH']['reason'] = "No restructions during this time.";
}

//SOUTH CONDITIONS
//NO PARKING: 16th to end of month, April 1 - Nov 30th?
if(($day >= 16) && ($month >= 4 && $month <= 11))
{
	$sides['SOUTH']['parking_allowed'] = false;
	$sides['SOUTH']['reason'] = "No parking 16th to end of month, April 1 to Nov 30th";
}

//positive can park case (if canpark is null, then we can assume its safe to park there)
if($sides['SOUTH'] == null)
{
	$sides['SOUTH']['parking_allowed'] = true;
	$sides['SOUTH']['reason'] = "No restrictions during this time.";
}

$result = json_encode($sides);
print_r($sides);

