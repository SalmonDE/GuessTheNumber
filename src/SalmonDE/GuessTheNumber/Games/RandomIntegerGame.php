<?php
declare(strict_types = 1);

namespace SalmonDE\GuessTheNumber\Games;

use pocketmine\utils\TextFormat as TF;
use SalmonDE\GuessTheNumber\Main;

class RandomIntegerGame extends NumberGame {

    public function __construct(string $name, array $options, array $prizes, string $playPermission, string $startPermission){
        parent::__construct($name, $prizes, $playPermission, $startPermission);

        $this->firstIntMin = (int) $options['min'];
        $this->firstIntMax = (int) $options['max'];
    }

    public function initGame(): void{
        $this->solution = random_int($this->firstIntMin, $this->firstIntMax);
        $this->firstInt = &$this->solution;
    }

    public function resetGame(): void{
        $this->solution = null;
        $this->firstInt = null;
    }

    public function getExample(): string{
        return (string) random_int($this->firstIntMin, $this->firstIntMax);
    }

    public function getAnnounceMessage(Main $plugin): string{
        $msg = TF::GREEN.TF::BOLD.$plugin->getMessage('general.game.startHeader', TF::GOLD.$this->getName().TF::GREEN).TF::RESET."\n";
        $msg .= TF::GOLD.$plugin->getMessage('general.game.example', TF::DARK_GRAY.$this->getExample().TF::GOLD).TF::RESET."\n";
        $msg .= TF::GOLD.$plugin->getMessage('general.game.howTo').TF::RESET;

        return $msg;
    }
}
