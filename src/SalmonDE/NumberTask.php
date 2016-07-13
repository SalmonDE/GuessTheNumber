<?php
namespace SalmonDE;

use pocketmine\level\sound\AnvilFallSound;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat as TF;

class NumberTask extends PluginTask{

	public function __construct(Plugin $owner, Player $player, $message){
		parent::__construct($owner);
		$this->player = $player;
		$this->MSG = $message;
	}

	public function onRun($currentTick){
		$lang = $this->getOwner()->getConfig()->get("Language");
		include($this->getOwner()->getDataFolder().$lang.".php");
		$min = $this->getOwner()->getConfig()->get('Minimum');
		$max = $this->getOwner()->getConfig()->get('Maximum');
		$information = json_decode(file_get_contents($this->getOwner()->getDataFolder().'currentgame.json'), true);
		if($information[behavior] == 5){
			if(MSG == $num){
				$this->getOwner()->givePrize($this->player);
		   }elseif(MSG > $max){
				$this->player->sendMessage(TF::RED.$numtoohigh);
		   }else{
				$this->player->sendMessage(TF::GOLD.$notright);
				$this->player->getLevel()->addSound(new AnvilFallSound($this->player->getPosition()));
		   }
	   }elseif($information[behavior] == 1350){
			if(MSG == $information[numq]){
				$this->getOwner()->givePrize($this->player);
		   }else{
				$this->player->sendMessage(TF::GOLD.$qnotright);
				$player->getLevel()->addSound(new AnvilFallSound($this->player->getPosition()));
		   }
	   }else{
			$this->getLogger()->critical(TF::DARK_RED.'Error 1! Not valid behavior: '.TF::AQUA.$information[behavior]);
	   }
	}
}
