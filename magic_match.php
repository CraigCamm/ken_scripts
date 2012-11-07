<?php

function magic_match($template,$haystack) {
    foreach($template as $key=>$val) {
        if(is_array($val)) {
            if(is_array($haystack[$key]) && !magic_match($val,$haystack[$key])) {
                echo "MISMATCH ARRAY";
                return FALSE;
            }
        } else if ($val!=$haystack[$key]) {
            echo "MISMATCH VALUE ({$val}) ";
            return FALSE;
        }
    }
    return TRUE;
}

$a = array('a'=>'test a', 'b'=>array('a','b','c','d','e'));
$b = array('a'=>'test a', 'b'=>array('a','b','c','d','e'), 'c'=>array('f','g','h','i','j'));
echo magic_match($a,$b)?"TRUE":"FALSE"; echo "\n";

$a = array('a'=>'test a', 'b'=>array('a','b','c','d','e'), 'c'=>"test");
$b = array('a'=>'test a', 'b'=>array('a','b','c','d','e'), 'c'=>array('f','g','h','i','j'));
echo magic_match($a,$b)?"TRUE":"FALSE"; echo "\n";

$a = array('a'=>'test a', 'b'=>array('a','b','c','d','e'), 'c'=>array('f','g'));
$b = array('a'=>'test a', 'b'=>array('a','b','c','d','e'), 'c'=>array('f','g','h','i','j'));
echo magic_match($a,$b)?"TRUE":"FALSE"; echo "\n";

$a = array('a'=>'test a', 'b'=>array('a','b','c','d','e'), 'c'=>array('h','i'));
$b = array('a'=>'test a', 'b'=>array('a','b','c','d','e'), 'c'=>array('f','g','h','i','j'));
echo magic_match($a,$b)?"TRUE":"FALSE"; echo "\n";

$a = array('a'=>'test a', 'b'=>array('a','b'), 'c'=>array('f','g','h','i','j'));
$b = array('a'=>'test a', 'b'=>array('a','b','c','d','e'), 'c'=>array('f','g','h','i','j'));
echo magic_match($a,$b)?"TRUE":"FALSE"; echo "\n";
