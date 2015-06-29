<?php
class Debug{
    
    public static function run($msg) {
        $fh = fopen("debugInfo.txt", 'a+') or die("can't open file");
        fwrite($fh, $msg);
        fwrite($fh, "\n");
        fclose($fh);
    }
}
//Debug::run();
?>