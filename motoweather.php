#!/usr/bin/php -q
<?php

function weather($city,$url) {
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    $result = preg_replace("/[\r\n]/","",$result);
    $data = (object)array();
    if(preg_match_all("/color:#; \">([^<:]*):<\/td>[^>]*>([^<]*)/",$result,$matches)) {
        for($i=0;$i<count($matches[0]);$i++) {
            $data->{$matches[1][$i]} = preg_replace("/ &#176; /","",$matches[2][$i]);
        }
    }
    $output = "{$city}\n"."----------------------\n";
    foreach($data as $key=>$val) {
        $output.="{$key}: {$val}\n";
    }
    $output.="\n";
    return $output;
}

//$i="http://www.buckeyetraffic.org/ajax/filter/LoadPointsOfInterest.aspx?filterXml=%3Ctravel_status_filters%3E%3Cbounding_box%20%20%20topLeftLat%3D%2241.41357371997092%22%20%20topLeftLong%3D%22-81.6592311859131%22%20%20bottomRightLat%3D%2241.40543051837934%22%20%20bottomRightLong%3D%22-81.62352561950684%22%2F%3E%3Crwis%20%20type%3D%22all%22%20%2F%3E%3Crwis%20%20type%3D%22ice%22%20%2F%3E%3Crwis%20%20type%3D%22snow%22%20%2F%3E%3Crwis%20%20type%3D%22wet%22%20%2F%3E%3Crwis%20%20type%3D%22dry%22%20%2F%3E%3Crwis%20%20type%3D%22error%22%20%2F%3E%3Crwis%20%20type%3D%22other%22%20%2F%3E%3Crwis%20%20type%3D%22outdated%22%20%2F%3E%3C%2Ftravel_status_filters%3E";
//$b="http://www.buckeyetraffic.org/ajax/filter/LoadPointsOfInterest.aspx?filterXml=%3Ctravel_status_filters%3E%3Cbounding_box%20%20%20topLeftLat%3D%2241.32291103801191%22%20%20topLeftLong%3D%22-81.681547164917%22%20%20bottomRightLat%3D%2241.306600905767006%22%20%20bottomRightLong%3D%22-81.61013603210452%22%2F%3E%3Crwis%20%20type%3D%22all%22%20%2F%3E%3Crwis%20%20type%3D%22ice%22%20%2F%3E%3Crwis%20%20type%3D%22snow%22%20%2F%3E%3Crwis%20%20type%3D%22wet%22%20%2F%3E%3Crwis%20%20type%3D%22dry%22%20%2F%3E%3Crwis%20%20type%3D%22error%22%20%2F%3E%3Crwis%20%20type%3D%22other%22%20%2F%3E%3Crwis%20%20type%3D%22outdated%22%20%2F%3E%3C%2Ftravel_status_filters%3E";
//$m="http://www.buckeyetraffic.org/ajax/filter/LoadPointsOfInterest.aspx?filterXml=%3Ctravel_status_filters%3E%3Cbounding_box%20%20%20topLeftLat%3D%2241.119743376835494%22%20%20topLeftLong%3D%22-81.6870403289795%22%20%20bottomRightLat%3D%2241.10338250538159%22%20%20bottomRightLong%3D%22-81.615629196167%22%2F%3E%3Crwis%20%20type%3D%22all%22%20%2F%3E%3Crwis%20%20type%3D%22ice%22%20%2F%3E%3Crwis%20%20type%3D%22snow%22%20%2F%3E%3Crwis%20%20type%3D%22wet%22%20%2F%3E%3Crwis%20%20type%3D%22dry%22%20%2F%3E%3Crwis%20%20type%3D%22error%22%20%2F%3E%3Crwis%20%20type%3D%22other%22%20%2F%3E%3Crwis%20%20type%3D%22outdated%22%20%2F%3E%3C%2Ftravel_status_filters%3E";

$i="http://www.buckeyetraffic.org/dialogs/rwis/ViewSite.aspx?id=562006";
$b="http://www.buckeyetraffic.org/dialogs/rwis/ViewSite.aspx?id=562010";
$m="http://www.buckeyetraffic.org/dialogs/rwis/ViewSite.aspx?id=612006";
$date = date("Y-m-d H:i:s");
$mail = weather("Montrose",$m).
        weather("Brecksville",$b).
        weather("Independence",$i);
mail("kmitchner@gmail.com","Weather for the ride in ({$date})",$mail);
