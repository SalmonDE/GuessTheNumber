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

	public function onEnable(){//Should delete the last imbusy.txt file to prevent errors
	    @mkdir($this->getDataFolder());
		$dir = $this->getDataFolder();
		if(file_exists($dir."imbusy.txt")){
			unlink($dir."imbusy.txt");
			$this->getLogger()->debug("Deleted temp file!");
		}
	    $this->saveResource("config.yml");
	    $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info(TF::GREEN."Enabled!");
	}

	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
		$dir = $this->getDataFolder();
		$tempfile = fopen($dir.'imbusy.txt','w');
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
		$dir = $this->getDataFolder();
		if(file_exists($dir."imbusy.txt")){
		    $file = file_get_contents($dir.'imbusy.txt');
		    $getinfo = unserialize($file);
		    extract($getinfo);
		    if($status == 1){
			    $player = $event->getPlayer();
			    $message = $event->getMessage();
			    if(is_numeric($message)){
				    if($behavior == 5){
					    if($message == $num){
						
					    }else{
						
					    }
				    }elseif($behavior == 1350){
					    if($message == $qnum){
						
					    }
				    }else{
					    $this->getLogger()->critical(TF::DARK_RED.'Error 1! Not valid behavior: '.TF::AQUA."$behavior");
				    }
			    }else{
				$player->sendMessage('Du musst nur eine numerische Zahl in den Chat schreiben, um beim Ratespiel mitzumachen!');//ToDo: Language file
			    }
		    }
		}
	}

	public function givePrize(){
	}
	
	public function onDisable(){
		$dir = $this->getDataFolder();
		if(file_exists($dir."imbusy.txt")){
			unlink($dir."imbusy.txt");
			$this->getLogger()->debug("Deleted temp file!");
		}
		$this->getLogger()->info("Disabled!");
	}
}