<?php
declare(strict_types = 1);

namespace SalmonDE\GuessTheNumber\Games;

use pocketmine\level\sound\ClickSound;
use pocketmine\level\sound\FizzSound;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;
use SalmonDE\GuessTheNumber\Main;

class RandomIntegerGame extends NumberGame {

    public function __construct(string $name, array $options, array $prizes, string $playPermission, string $startPermission){
        parent::__construct($name, $prizes, $playPermission, $startPermission);

        $this->firstIntMin = (int) $options['min'];
        $this->firstIntMax = (int) $options['max'];
    }

    public function initGame(){
        $this->solution = random_int($this->firstIntMin, $this->firstIntMax);
        $this->firstInt = &$this->solution;
    }

    public function resetGame(){
        $this->solution = null;
        $this->firstInt = null;
    }

    public function getExample(): string{
        return (string) random_int($this->firstIntMin, $this->firstIntMax);
    }

    public function announceGame(Main $plugin, array $recipients = null){
        $msg = TF::GREEN.TF::BOLD.$plugin->getMessage('general.game.startHeader', TF::GOLD.$this->getName().TF::GREEN).TF::RESET."\n";
        $msg .= TF::GOLD.$plugin->getMessage('general.game.example', TF::AQUA.$this->getExample().TF::GOLD).TF::RESET."\n";
        $msg .= TF::GOLD.$plugin->getMessage('general.game.howTo').TF::RESET;


        $sound = new ClickSound(new Vector3());

        foreach($recipients ?? $plugin->getServer()->getOnlinePlayers() as $player){
            $sound->x = $player->z;
            $sound->y = $player->z;
            $sound->z = $player->z;

            $player->getLevel()->addSound($sound, [$player]);
            $player->addTitle('', TF::GOLD.$this->getName(), 10, 40, 20);
            $player->sendMessage($msg);
        }
    }

    protected function broadcastWinner(Player $player, string $answer, Main $plugin){
        $msg = TF::GREEN.$plugin->getMessage('answer.right', $player->getDisplayName(), $answer);

        $sound = new FizzSound(new Vector3());

        foreach($plugin->getServer()->getOnlinePlayers() as $p){
            $sound->x = $p->x;
            $sound->y = $p->y;
            $sound->z = $p->z;

            $p->sendMessage($msg);
            $p->getLevel()->addSound($sound, [$p]);
        }
    }
}