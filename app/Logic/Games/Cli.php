<?php

namespace App\Logic\Games;

class Cli {

    public function input(String $promt = ""): String {
        echo $promt."\n";
        $handle = fopen("php://stdin", "r");
        $output = fgets($handle);
        return trim($output);
    }
}