#!/usr/bin/php -q
<?php

// Change this if you want to use a different folder name to store the pointer files.
///////////////////////////////////
// ~/ doesnt seem to work in php.
$parser_home = ".ken_log_parser";
$parser_home = getenv("HOME")."/".$parser_home;
///////////////////////////////////

// If the above can't be found or created we have to stop here.
if(!file_exists($parser_home)) mkdir($parser_home) or die("Folder {$parser_home} could not be created.\n");

// Shows Usage and dies.
function die_with_usage($str=NULL) {
    global $argv;
    if(isset($str)) fwrite(STDERR,"ERROR: {$str}\n\n");
    $usage = "Usage: $argv[0] <filename> <regex search> [options]\n".
             "    --pointer=#       Reset file seek pointer\n".
             "    --lines=#         Limit the number of matches to return.\n".
             "                         Next execution will continue at next match.\n".
             "    --verbose         Shows debug info, including the pointer filename\n".
             "    --help            Shows this message.\n";
    die($usage);
}

// Checks for a command line option, returning stuff.
// Supports --option  --option=value  and  --option="value"
// Returns TRUE if --option, the value if --option=value and FALSE otherwise.
function checkopt($opt) {
    global $argv;
    foreach(array_slice($argv,3) as $argument) {
        if(preg_match("/--{$opt}=/",$argument)) {
            return preg_replace("/^--{$opt}=/","",$argument);
        } else if(preg_match("/^--{$opt}/",$argument)) {
            return TRUE;
        }
    }
    return FALSE;
}

// Writes to STDERR if --verbose has been used.
function vwrite($str) {
    global $verbose;
    if($verbose===TRUE) {
        fwrite(STDERR,$str);
    }
}

// VERIFICATION

// Not enough arguments.
if($argc<2) die_with_usage();
// Asking for help.
if(checkopt("help")===TRUE) die_with_usage();
// Set verbose.
$verbose = checkopt("verbose");
// Set lines.
$lines = checkopt("lines");

// Verify the file exists.
$file = $argv[1];
if(!file_exists($file)) die_with_usage("File {$file} does not exist or cannot be accessed.");

// Setting Search String.
$search = $argv[2];

// Getting hash for pointer file.
$hash = sha1($file.$search);
$ptr_file = $parser_home."/".$hash;

// Seeing if we can either open or create the pointer file.
// Tertiary craziness in the put_contents is just for if you use --pointer=# or not so we can save that default.
$arg_ptr = checkopt("pointer");
if(!file_exists($ptr_file)) file_put_contents($ptr_file,$arg_ptr!==FALSE?$arg_ptr:0) or die("Pointer File {$ptr_file} could not be created.\n");
$ptr = file_get_contents($ptr_file);

vwrite("############################################################\n");
vwrite("######### ALL NON-DATA OUTPUT IS WRITTEN TO STDERR #########\n");
vwrite("############################################################\n");
vwrite("File used to track seek poitners for this search:\n");
vwrite("{$ptr_file}.\n");
vwrite("############################################################\n");
vwrite("Current Seek Pointer: {$ptr}\n");
vwrite("############################################################\n\n");

$f = fopen($file,"r");

fseek($f,(int)$ptr);

$line_buffer = "";
$eol_buffer = "";

$linecnt=0;

// Parse Lines
while(!feof($f)) {
    $chunk = fread($f,4096);
    for($i=0;$i<strlen($chunk);$i++) {
        $char = substr($chunk,$i,1);
        if(!preg_match("/[\r\n]/",$char)) {
            $line_buffer .= $char;
            $eol_buffer="";
        } else {
            // Skipping over \r\n because they pollute our logs.
            if($char=="\n" && !preg_match("/\r/",$eol_buffer)) {
                if(preg_match("/{$search}/",$line_buffer)) {
                    fwrite(STDOUT,$line_buffer."\n");
                }
                $line_buffer="";
                $linecnt++;
                // Parsed our goal number of lines
                if($lines!==FALSE && $linecnt>=$lines) {
                    $ptr-=strlen($chunk)-$i;
                    break 2;
                }
            } else {
                $eol_buffer .= $char;
            }
        }
    }
}

// Rewind seek pointer to beginning of current finished line
// In the case of having a line limit, so we dont miss a line.
$ptr -= strlen($line_buffer)+strlen($eol_buffer);

$ptr = ftell($f);
file_put_contents($ptr_file,$ptr);
vwrite("############################################################\n");
vwrite("Current Seek Pointer:\n");
vwrite("{$ptr}.\n");
vwrite("############################################################\n");
fclose($f);
