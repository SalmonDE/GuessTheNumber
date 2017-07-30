<?php
declare(strict_types = 1);

namespace SalmonDE\GuessTheNumber\Tasks;

use pocketmine\Player;
use pocketmine\scheduler\PluginTask;
use SalmonDE\GuessTheNumber\Main;

class AnswerCheckTask extends PluginTask {

    private $player;
    private $number;

    public function __construct(Main $owner, Player $player, string $answer){
        parent::__construct($owner);
        $this->player = $player;
        $this->answer = $answer;
    }

    public function onRun(int $currentTicks){
        if($this->player->isOnline()){
            $this->getOwner()->getCurrentGame()->checkAnswer($this->answer, $this->player, $this->getOwner());
        }
    }
}
