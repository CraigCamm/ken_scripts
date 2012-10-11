#!/usr/bin/php
<?php
# Copyright [2012] [Kenneth Mitchner]
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

declare(ticks = 1);

function sig_handler($signo)
{
    system('stty echo');
    exit();
}

pcntl_signal(SIGTERM, "sig_handler");
pcntl_signal(SIGHUP,  "sig_handler");

$iphp = new Interactive_PHP();
$iphp->run();

class Interactive_PHP {
    public function __construct() {
        $this->history = array("");
        $this->cursor_pos = 0;
    }
    protected $history;
    protected $cursor_pos;

    /*
        Command Methods
     */
    public function __get($name) {
        switch($name) {
        case "current":
            return current($this->history);
            break;
        }
    }

    public function __set($name, $value) {
        switch($name) {
        case "current":
            $this->clear_line(FALSE);
            $this->history[key($this->history)] = $value;
            break;
        }
    }

    public function __isset($name) {
        switch($name)
        {
        case "current":
            $this->current===FALSE?FALSE:TRUE;
            break;
        }
    }

    public function first($data=NULL) {
        if(isset($data))
            $this->history[0] = $data;
        return $this->history[0];
    }

    public function last($data=NULL) {
        if(isset($data))
            $this->history[count($this->history)-1] = $data;
        return $this->history[count($this->history)-1];
    }

    public function next() {
        if(key($this->history)!=count($this->history)-1) {
            $this->clear_line(FALSE);
            return next($this->history);
        }
    }

    public function prev() {
        if(key($this->history)!=0) {
            $this->clear_line(FALSE);
            return prev($this->history);
        }
    }

    public function reset() {
        $this->clear_line(FALSE);
        return reset($this->history);
    }

    public function end() {
        $this->clear_line(FALSE);
        return end($this->history);
    }

    public function bump() {
        $this->clear_line(FALSE);
        if($this->current!=$this->last()) { // History Execution
            $this->last($this->current);
        }
        array_push($this->history,"");
        return $this->end();
    }

    /*
        TTY Methods
     */
    protected function cursor_right($shift,$update_cursor_data=FALSE) {
        if($this->cursor_pos<strlen($this->current) && $shift>0) {
            echo "\e[".$shift."C";
            if($update_cursor_data) $this->cursor_pos += $shift;
        }
    }

    protected function cursor_left($shift,$update_cursor_data=FALSE) {
        if($this->cursor_pos>0 && $shift>0) {
            echo "\e[".$shift."D";
            if($update_cursor_data) $this->cursor_pos -= $shift;
        }
    }

    public function clear_line($show_prompt=TRUE) {
        echo "\e[2K";
        $len = strlen($this->current)+($show_prompt?2:0);
        $this->cursor_left($len);
        if($show_prompt)
            echo "> ";
    }

    public function refresh_line($show_prompt=TRUE) {
        $this->clear_line($show_prompt);
        echo $this->current;
        $this->cursor_pos=strlen($this->current)+($show_prompt?2:0);
    }

    public function add_to_line($str) {
        $curr_pos = $this->cursor_pos;
        $curr_len = strlen($this->current);
        $this->current = substr($this->current,0,$curr_pos-2).$str.substr($this->current,$curr_pos-2);
        $this->refresh_line(TRUE);
        $this->cursor_left($curr_len+2-$curr_pos,TRUE);
    }

    public function delete_from_line($bs=TRUE) {
        $curr_pos = $this->cursor_pos;
        $curr_len = strlen($this->current);
        if($bs) {
            $this->current = substr($this->current,0,$curr_pos-3).substr($this->current,$curr_pos-2);
            $this->refresh_line(TRUE);
            $this->cursor_left($curr_len+2-$curr_pos,TRUE);
        } else {
            $this->current = substr($this->current,0,$curr_pos-2).substr($this->current,$curr_pos-1);
            $this->refresh_line(TRUE);
            $this->cursor_left(strlen($this->current)+2-$curr_pos,TRUE);
        }
    }

    public function history_up() {
        $this->prev();
        $this->refresh_line(TRUE);
    }

    public function history_down() {
        $this->next();
        $this->refresh_line(TRUE);
    }

    /*
        Main Loop
     */
    public function run() {
        echo "Interactive mode enabled\n";
        system('stty -icanon -echo');
        $this->refresh_line(TRUE);
        while( ($c = fread(STDIN,4)) !== FALSE ) {
            try {
                switch ($c) {
                case hex2bin("1b5b41"): // Up
                    $this->history_up();
                    break;
                case hex2bin("1b5b42"): // Down
                    $this->history_down();
                    break;
                case hex2bin("1b5b43"): // Right
                    $this->cursor_right(1,TRUE);
                    break;
                case hex2bin("1b5b44"): // Left
                    $this->cursor_left(1,TRUE);
                    break;
                case hex2bin("7f"): // Backspace
                    $this->delete_from_line();
                    break;
                case hex2bin("1b5b337e"): // Delete
                    $this->delete_from_line(FALSE);
                    break;
                case "\n": // Enter
                    $str = $this->current;
                    if($str=="export" || preg_match("/exit(.*?)/",$str)) {
                        echo "\n";
                        echo "\n";
                        echo "<?php\n";
                        foreach($this->history as $command) {
                            if($command!="export")
                                echo $command."\n";
                        }
                        echo "?>\n";
                        echo "\n";
                        system('stty echo');
                        exit();
                    }
                    if(trim($str)!="") {
                        echo "\n";
                        ob_start();
                        eval($str);
                        $ob = ob_get_contents();
                        ob_end_clean();
                        $this->bump();
                        if($ob!="") {
                            echo $ob."\n";
                        }
                        $this->refresh_line(TRUE);
                    }
                    break;
                default: // Valid Characters
                    if(preg_match("/[a-zA-Z0-9 \!\@\#\$\%\^\&\*\(\)\-\_\=\+\`\~\[\]\{\}\\\|\'\"\;\:\/\?\.\>\,\<]/",$c)) {
                        $this->add_to_line($c);
                    } else echo "\nUnknown Character: ".bin2hex($c)."\n";
                    break;
                }
            } catch(Exception $e) {}
        }
    }
}
