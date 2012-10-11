#!/usr/bin/php -q
<?php
# Copyright 2012 Kenneth Mitchner
# 
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
# 
# http://www.apache.org/licenses/LICENSE-2.0
# 
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.


/* Process non-positional arguments */
function getopt_loc($args,$config) {
    $options = array();
    // Loop through the options, looking for matching arguments.
    foreach($config as $opt) {
        // Configuration array validation.
        if(!isset($opt['short']) || !isset($opt['long']) || ( !isset($opt['default']) && !isset($opt['required']) )) return NULL;
        // NULL if required
        $options[$opt['long']] = ( isset($opt['required']) && ( $opt['required']===true || $opt['required'] == 'true' ) ) ? NULL : $opt['default'];
        // Loop through the command line arguments to find the current option.
        foreach($args as $arg) {
            if( preg_match("/^--{$opt['long']}=/",$arg) ) {
                // --option=value
                $options[$opt['long']] = preg_replace("/--{$opt['long']}=/","",$arg);
            } else if( preg_match("/^-{$opt['short']}.+/",$arg) ) {
                // -oValue    
                $options[$opt['long']] = preg_replace("/^-{$opt['short']}/","",$arg);
            } else if( preg_match("/^[-]*{$opt['long']}$/",$arg) || preg_match("/^-{$opt['short']}$/",$arg) ) {
                // -option or --option or -o or option
                $options[$opt['long']] = true;
            }
        }
        var_dump($options);
        // Required Value Validation
        if($options[$opt['long']]===NULL) return NULL;
    }
    return $options;
}

// This is to be overloaded with configuration meta-data.
// Below is a sample
$options_configuration = array(
    'apple' => array(
        'short' => 'a',
        'long' => 'apple',
        'required' => true,
    ),  
    'banana' => array(
        'short' => 'b',
        'long' => 'banana',
        'default' => 'peel',
    ),  
    'cherry' => array(
        'short' => 'c',
        'long' => 'cherry',
        'default' => false,
    ),
);

$opts = getopt_loc($argv,$options_configuration);

var_dump($opts);
