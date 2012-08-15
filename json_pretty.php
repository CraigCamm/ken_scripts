<?php

function json_beautifier($ugly_json)
{
    $ugly_json = json_minifier($ugly_json);
    $TAB='    ';
    $ugly_json = preg_replace("/([{\[])/","$1\n",$ugly_json);
    $ugly_json = preg_replace("/([\]}])/","\n$1",$ugly_json);
    $ugly_json = preg_replace("/,/",",\n",$ugly_json);
    $depth=0;
    $ugly_array = explode("\n",$ugly_json);
    foreach($ugly_array as $index=>$line) {
        if(preg_match("/[]}]/",$line)) $depth--;
        if($depth>0) $tabs=str_repeat($TAB,$depth);
        else $tabs='';
        $ugly_array[$index] = $tabs.$line;
        if(preg_match("/[{[]/",$line)) $depth++;
    }
    $pretty_json = implode("\n",$ugly_array);
    return $pretty_json;
}

function json_minifier($pretty_json)
{
    $ugly_json = preg_replace("/\n */",'',$pretty_json);
    $isstr=false;
    $len=mb_strlen($ugly_json);
    for($ptr=0;$ptr<$len;$ptr++) {
        $char = mb_substr($ugly_json,$ptr,1);
        if($char=='"') {
            $isstr=!$isstr;
        } else if(!$isstr && $char==' ') {
            $ugly_json = (mb_substr($ugly_json,0,$ptr).mb_substr($ugly_json,$ptr+1));
            $ptr--;
        }
    }
    return $ugly_json;
}


$sample = "  {\"id\"     :     \"df166d934c2149c9023e6a5bfbc3e78e\",      \"version\"  :   2,\"application\":\"custom\",\"contexts\":{\"context_1\":{\"actions\":{\"1\":{\"type\":\"MENU\",\"results\":{\"default\":2,\"1\":3}},\"2\":{\"type\":\"HANGUP\"},\"3\":{\"type\":\"DIAL\",\"destination_numbers\":[\"2169206478\"],\"dispatch\":[\"context_2\"]}}},\"context_2\":{\"actions\":{\"2\":{\"type\":\"GETDIGITS\",\"filename\":\"ivr_postcall_enterdigits\",\"results\":{\"FAILURE\":2,\"TIMEOUT\":2}},\"3\":{\"type\":\"ANNOUNCE\",\"filename\":\"ivr_postcall_entered\"},\"4\":{\"type\":\"MENU\",\"filename\":\"ivr_postcall_confirm\",\"results\":{\"1\":1,\"default\":2}},\"5\":{\"type\":\"GETDIGITS\",\"filename\":\"ivr_postcall_enterdigits\",\"results\":{\"FAILURE\":5,\"TIMEOUT\":5}},\"6\":{\"type\":\"ANNOUNCE\",\"filename\":\"ivr_postcall_entered\"},\"7\":{\"type\":\"MENU\",\"filename\":\"ivr_postcall_confirm\",\"results\":{\"1\":1,\"default\":5}},\"8\":{\"type\":\"GETDIGITS\",\"filename\":\"ivr_postcall_enterdigits\",\"results\":{\"FAILURE\":8,\"TIMEOUT\":8}},\"9\":{\"type\":\"ANNOUNCE\",\"filename\":\"ivr_postcall_entered\"},\"10\":{\"type\":\"MENU\",\"filename\":\"ivr_postcall_confirm\",\"results\":{\"1\":1,\"default\":8}},\"11\":{\"type\":\"GETDIGITS\",\"filename\":\"ivr_postcall_enterdigits\",\"results\":{\"FAILURE\":11,\"TIMEOUT\":11}},\"12\":{\"type\":\"ANNOUNCE\",\"filename\":\"ivr_postcall_entered\"},\"13\":{\"type\":\"MENU\",\"filename\":\"ivr_postcall_confirm\",\"results\":{\"1\":1,\"default\":11}},\"14\":{\"type\":\"HANGUP\"},\"1\":{\"type\":\"Menu\",\"filename\":\"ivr_postcall_enterdigits\",\"results\":{\"1\":2,\"2\":5,\"3\":8,\"4\":14}}}}}}     ";

$pretty = json_beautifier($sample);

echo $pretty;

echo "\n\n";

$ugly = json_minifier($pretty);

echo $ugly;
