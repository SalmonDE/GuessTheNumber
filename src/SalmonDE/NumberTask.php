<?php
namespace SalmonDE;

use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\PluginTask;

class NumberTask extends PluginTask{

	public function __construct(Plugin $owner, Player $player, $message){
		parent::__construct($owner);
		$this->player = $player;
		$this->message = $message;
	}

	public function onRun($currentTick){
		$lang = $this->getConfig()->get("Language");
		include($this->getDataFolder().$lang.".php");
		$min = $this->getConfig()->get('Minimum');
		$max = $this->getConfig()->get('Maximum');
		$information = json_decode(file_get_contents($this->getDataFolder().'currentgame.json'), true);
		if($information[behavior] == 5){
			if($message == $num){
				$this->getOwner()->givePrize($player);
		   }elseif($message > $max){
				$player->sendMessage(TF::RED.$numtoohigh);
		   }else{
				$player->sendMessage(TF::GOLD.$notright);
				$player->getLevel()->addSound(new AnvilFallSound($player->getPosition()));
		   }
	   }elseif($information[behavior] == 1350){
			if($message == $information[numq]){
				$winner = $player;
				$this->getOwner()->givePrize($player);
		   }else{
				$player->sendMessage(TF::GOLD.$qnotright);
				$player->getLevel()->addSound(new AnvilFallSound($player->getPosition()));
		   }
	   }else{
			$this->getLogger()->critical(TF::DARK_RED.'Error 1! Not valid behavior: '.TF::AQUA.$information[behavior]);
	   }
	    $this->killTask();
	}

	public function killTask(){
		$this->getHandler()->cancel();
	}
}