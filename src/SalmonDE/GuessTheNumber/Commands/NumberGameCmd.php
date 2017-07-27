<?php
declare(strict_types = 1);

namespace SalmonDE\GuessTheNumber\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use SalmonDE\GuessTheNumber\Main;

class NumberGameCmd extends PluginCommand implements CommandExecutor {

    public function __construct(Main $plugin){
        parent::__construct($plugin);
        $this->setExecutor($this);
        $this->setDescription('Main command of GuessTheNumber');
        $this->setUsage('/numbergame <rand|exp|add|sub|mult|div|fac|solution|abort>');
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, string $args): bool{
        switch($args[0] ?? null){
            
            default:
                return false;
        }
    }
}
