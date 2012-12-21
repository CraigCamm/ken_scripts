<?php

function magic_match($template,$haystack) {
    foreach($template as $key=>$val) {
        if(isset($haystack[$key])) {
            if(is_array($val)) {
                if(is_array($haystack[$key]) && !magic_match($val,$haystack[$key])) {
                    echo "MISMATCH ARRAY";
                    return FALSE;
                }
            } else if ($val!=$haystack[$key]) {
                echo "MISMATCH VALUE ({$val}) ";
                return FALSE;
            }
        } else {
            echo "MISMATCH EXIST ";
            return FALSE;
        }
    }
    echo "SUCCESS ";
    return TRUE;
}

$a = array('0'=>array('insert'=>array('0'=>'cdr_rated')),'1'=>array('columns'=>array('0'=>array('0'=>'cdr_rated_code','1'=>'cdr_entry_date','2'=>'account_code','3'=>'campaign_code','4'=>'incoming_number','5'=>'did_number','6'=>'destination_number','7'=>'status','8'=>'message','9'=>'session_uniqueid','10'=>'keywords','11'=>'keyword_type','12'=>'callrate_charged','13'=>'charges_call','14'=>'charges_addresslookup','15'=>'call_duration','16'=>'duration_in_mins','17'=>'incoming_caller_name','18'=>'caller_state','19'=>'caller_areacode','20'=>'caller_zip','21'=>'caller_timezone','22'=>'route_type','23'=>'keyword_source','24'=>'promocode','25'=>'rings_count','26'=>'recording_completed','27'=>'recording'))),'2'=>array('values'=>array('0'=>array('0'=>'88895b340f8b63e3c8c0ecb8d27106a75cbf1e06','2'=>'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa','3'=>'cccccccccccccccccccccccccccccccc','4'=>'2222222222','5'=>'3333333333','6'=>'4444444444','7'=>'1','8'=>'NOT RELEVANT','9'=>'1234567','10'=>'','11'=>'','12'=>'0.795','13'=>'0.795','14'=>'0.25','15'=>'300','16'=>'5','17'=>' ','18'=>'OH','19'=>'222','20'=>'44131','21'=>'EST','22'=>'default','23'=>'mongoosemetrics.com','24'=>'','25'=>'-4','26'=>'Y','27'=>'http://recordings.mongoosemetrics.com/unprocessed/88895b340f8b63e3c8c0ecb8d27106a75cbf1e06.wav'))));
$b = array(array('insert'=>array('cdr_rated')),array('columns'=>array(array('cdr_rated_code','cdr_entry_date','account_code','campaign_code','incoming_number','did_number','destination_number','status','message','session_uniqueid','keywords','keyword_type','callrate_charged','charges_call','charges_addresslookup','call_duration','duration_in_mins','incoming_caller_name','caller_state','caller_areacode','caller_zip','caller_timezone','route_type','keyword_source','promocode','rings_count','recording_completed','recording'))),array('values'=>array(array('88895b340f8b63e3c8c0ecb8d27106a75cbf1e06','FROM_UNIXTIME(1352327553)','aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa','cccccccccccccccccccccccccccccccc','2222222222','3333333333','4444444444','1','NOT RELEVANT','1234567','','','0.795','0.795','0.25','300','5',' ','OH','222','44131','EST','default','mongoosemetrics.com','','-4','Y','http://recordings.mongoosemetrics.com/unprocessed/88895b340f8b63e3c8c0ecb8d27106a75cbf1e06.wav'))));

magic_match($a,$b);
