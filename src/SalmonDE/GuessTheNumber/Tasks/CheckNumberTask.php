<?php
namespace SalmonDE\GuessTheNumber\Tasks;

use pocketmine\level\sound\AnvilFallSound;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat as TF;

class CheckNumberTask extends PluginTask
{

    public function __construct(Plugin $owner, Player $player, $message){
		    parent::__construct($owner);
		    $this->player = $player;
		    $this->msg = $message;
		    $this->lang = $owner->getMessages();
		    $this->information = $owner->information;
        $this->min = $owner->min;
        $this->max = $owner->max;
	  }

	  public function onRun($currentTick){
		    if($this->msg == $this->information['solution']){
				    $this->getOwner()->givePrize($this->player);
			  }else{
            $this->player->getLevel()->addSound(new AnvilFallSound($this->player->getPosition()), [$this->player]);
            if($this->information['behavior'] === 1 && $this->msg > $this->max){
				        $this->player->sendMessage(TF::RED.str_ireplace(['{min}', '{max}'], [$this->min, $this->max],$this->lang['numtoohigh']));
            }else{
				        $this->player->sendMessage(TF::GOLD.$this->lang['notright']);
            }
		    }
        unset($this->getOwner()->queue[$this->player->getName()]);
	  }
}
