<?php
declare(strict_types = 1);

namespace SalmonDE\GuessTheNumber\Tasks;

use pocketmine\Player;
use pocketmine\scheduler\Task;
use SalmonDE\GuessTheNumber\Main;

class AnswerCheckTask extends Task {

    private $owner;
    private $player;
    private $number;

    public function __construct(Main $owner, Player $player, string $answer){
        $this->owner = $owner;
        $this->player = $player;
        $this->answer = $answer;
    }

    public function onRun(int $currentTick): void{
        if($this->player->isOnline()){
            $this->owner->getListener()->setAnswering($this->player->getName(), false);
            $this->owner->getCurrentGame()->checkAnswer($this->answer, $this->player, $this->owner);
        }
    }
}
