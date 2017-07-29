<?php
declare(strict_types = 1);

namespace SalmonDE\GuessTheNumber\Tasks;

use pocketmine\level\sound\AnvilFallSound;
use pocketmine\level\sound\FizzSound;
use pocketmine\Player;
use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat as TF;
use SalmonDE\GuessTheNumber\Main;

class AnswerCheckTask extends PluginTask {

    private $player;
    private $number;

    public function __construct(Main $owner, Player $player, float $number){
        parent::__construct($owner);
        $this->player = $player;
        $this->number = $number;
    }

    public function onRun(int $currentTicks){
        if($this->getOwner()->isGameRunnnig() && $this->player->isOnline()){
            if($this->getOwner()->getCurrentGame()->isSolution($number)){
                $msg = TF::GREEN.$this->getOwner()->getMessage('answer.right', $number);

                foreach($this->getOwner()->getServer()->getOnlinePlayers() as $player){
                    $player->sendMessage($msg);
                    $player->getLevel()->addSound(new FizzSound($player), [$player]);
                }

                $this->getOwner()->getCurrentGame()->givePrizes($this->player, $this->getOwner());
            }else{
                $this->player->sendMessage(TF::RED.$this->getOwner()->getMessage('answer.wrong'));
                $this->player->getLevel()->addSound(new AnvilFallSound($this->player), [$this->player]);
            }
        }
    }

}
