<?php
namespace SalmonDE\Tasks;

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
	  }

	  public function onRun($currentTick){
		    $min = $this->getOwner()->getConfig()->get('Minimum');
		    $max = $this->getOwner()->getConfig()->get('Maximum');
		    if($this->information['behavior'] == 5){
			      if($this->msg == $this->information['num']){
				    $this->getOwner()->givePrize($this->player);
			      }elseif($this->msg > $max){
				        $this->player->sendMessage(TF::RED.$numtoohigh);
		        }else{
				        $this->player->sendMessage(TF::GOLD.$notright);
				        $this->player->getLevel()->addSound(new AnvilFallSound($this->player->getPosition()));
		        }
	      }elseif($this->information['behavior'] == 1350){
			      if($this->msg == $this->information['numq']){
				    $this->getOwner()->givePrize($this->player);
		        }else{
				        $this->player->sendMessage(TF::GOLD.$qnotright);
				        $player->getLevel()->addSound(new AnvilFallSound($this->player->getPosition()));
		        }
	     }else{
		       $this->getOwner()->getLogger()->critical(TF::DARK_RED.'Error! Not valid behavior: '.TF::AQUA.$this->information['behavior']);
	     }
	  }
}
