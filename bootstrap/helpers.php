<?php


function secondsToTime($seconds): string
{
    return floor($seconds / 3600).gmdate(":i:s", $seconds % 3600);
}function secondsToTimeForHumans($seconds): string
{
    return \Carbon\Carbon::createFromTime()->addSeconds((int) $seconds)->diff('00:00:00')->forHumans();
}
