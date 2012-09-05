<?php

/**
 * Find a value in a string that either contains a
 * Range denoted by 3-6 or a list denoted by 3,4,5
 *
 * @param string "needle" Value to search for.
 * @param string "haystack" String to search.
 * @return boolean true on found, false on not found.
 */
function strlorr($needle,$haystack) {
    // Search for a "-" which means it is a range
    if(preg_match("/-/",$haystack)) {
        // Explode into an array.
        $arr = explode('-',$haystack);
        // Check the range, returning the result.
        return ($needle>=$arr[0] && $needle<=$arr[1]);
    } else {
        // Assuming either a value or a list, strstr
        // works on both.
        return strstr($haystack,$needle)!==false;
    }
}

/**
 * Determine whether or not to return the destination number.
 *
 * @param string Search term, being either a time, zipcode, 
 *     npa or state abbrev.
 * @param integer Integer value of a day of the week.
 * @param string 'caller_value' string from table.
 * @param string 'called_weekday' string from table.
 */
function return_destination_number($search,$day,$cv,$cw) {
    echo "$search is within $cv and $day is within $cw: ";
    // Change "All Days" and "" to the range 0-6
    // They are both treated as every day flags.
    $cw = preg_replace("/(All Days)|^$/","0-6",$cw);
    // Run strlorr on both pairs of values.
    if( strlorr($search,$cv) && strlorr($day,$cw))
        echo "TRUE\n";
    else echo "FALSE\n";
}

echo "TRUE\n";
return_destination_number("754","2","786,754,954,305","1-5");
return_destination_number("44150","6","44131-44190","6");
return_destination_number("11:00:02","1","11:00:01-23:00:00","All Days");
return_destination_number("OH","0","OH,KY,TN","0,3");
return_destination_number("44110","2","44312,44131,44110","");
return_destination_number("44312","3","44312","0,3");
return_destination_number("788","5","788","1-5");

echo "FALSE\n";
return_destination_number("754","6","786,754,954,305","1-5");
return_destination_number("44191","6","44131-44190","6");
return_destination_number("11:00:00","1","11:00:01-23:00:00","All Days");
return_destination_number("OH","2","OH,KY,TN","0,3");
return_destination_number("44111","2","44312,44131,44110","");
return_destination_number("44311","3","44312","0,3");
return_destination_number("788","666666","788","1-5");
