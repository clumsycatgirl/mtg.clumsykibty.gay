<?php

namespace App\Commands;
use Lib\Cli\Command;

class MeowCommand extends Command {
    public function call(): void {
        echo 'meow';
    }
}
