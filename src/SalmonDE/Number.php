<?php
namespace SalmonDE;

use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TF;

class Number extends PluginBase implements Listener{
    private $num;
	//Normale Zahl
	private $qnum;
	//Quadratzahl
	private $behavior;
	//Verhalten (Quadrat/Normal)
	private $winner;
	//Congratulations!

	public function onEnable(){//Should save an imbusy.txt file to prevent errors
	    @mkdir($this->getDataFolder());
	    $this->saveResource("config.yml");
	    $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info(TF::GREEN."Enabled!");
	}

	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
		$tempfile = fopen('imbusy.txt','w');
		if($cmd == "guess"){
		 $status = 1;
		 $behavior = 5;
		 $num = mt_rand(1,100);//To-Do: Make it configurable
		 $store = array(
		             'status' => "$status",
					 'num' => "$num",
					 'behavior' => "$behavior"
		 );
		 fwrite($tempfile,serialize($store));
		 $sender->sendMessage("Not finished");
		 $sender->sendMessage((string) $num);//If $sender->hasPermission for this!
		 return true;
		}elseif($cmd == "guessquare"){
		 $status = 1;
		 $behavior = 1350;
		 $qnum = mt_rand(1,20);
		 $numq = $qnum * $qnum;
		 $sender->sendMessage((string) $qnum);
		 $sender->sendMessage((string) $numq);
		}
	}

	public function onChat(PlayerChatEvent $event){
		$player = $event->getPlayer();
		$file = file_get_contents('imbusy.txt');
		$getinfo = unserialize($file);
		extract($info);//Get the information better
		if($status === 1){
			$player->sendMessage("Guessing is activated!");
		}
	}

	public function givePrize(){
	}
}