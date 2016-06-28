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
	private $winner;

	public function onEnable(){//Should delete the last imbusy.txt file to prevent errors
	    @mkdir($this->getDataFolder());
		$dir = $this->getDataFolder();
		if(file_exists($dir.'imbusy.txt')){
			unlink($dir.'imbusy.txt');
			$this->getLogger()->debug('Deleted temp file!');
		}
	    $this->saveResource('config.yml');
	    $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info(TF::GREEN.'Enabled!');
	}

	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
		$dir = $this->getDataFolder();
		$tempfile = fopen($dir.'imbusy.txt','w');
		if(file_exists($dir.'imbusy.txt')){
			$sender->sendMessage(TF::RED.'Geblockt! Ratespiel schon im Gange!');
		}else{
		    if($cmd == 'guess'){
		        $status = 1;
		        $behavior = 5;
		        $num = mt_rand(1,100);//To-Do: Make it configurable
		        $store = array(
		                    'status' => "$status",
					        'num' => "$num",
					        'behavior' => "$behavior"
		        );
		        fwrite($tempfile,serialize($store));
		        $sender->sendMessage(TF::AQUA'Nearly finished! :D');
		        $sender->sendMessage((string) $num);//If $sender->hasPermission for this!
		        return true;
		    }elseif($cmd == 'guessquare'){
		        $status = 1;
		        $behavior = 1350;
		        $qnum = mt_rand(1,20);
		        $numq = $qnum * $qnum;
				$store = array(
		                    'status' => "$status",
							'qnum' => "$qnum";
					        'numq' => "$numq",
					        'behavior' => "$behavior"
		        );
		        fwrite($tempfile,serialize($store));
		        $sender->sendMessage((string) $qnum);
		        $sender->sendMessage((string) $numq);//If $sender->hasPermission for this!
		    }
		}
	}

	public function onChat(PlayerChatEvent $event){
		$dir = $this->getDataFolder();
		if(file_exists($dir.'imbusy.txt')){
		    $file = file_get_contents($dir.'imbusy.txt');
		    $getinfo = unserialize($file);
		    extract($getinfo);
		    if($status == 1){
			    $player = $event->getPlayer();
			    $message = $event->getMessage();
			    if(is_numeric($message)){
					$player->sendMessage('In 2 Sekunden erfährst du, ob es richtig ist!');//Configurable
					sleep(2);//Configurable
				    if($behavior == 5){
					    if($message == $num){
							$player->sendMessage(TF::GREEN.TF::BOLD.'Herzlichen Glückwunsch! Du hast gewonnen!');//Write it better in a language file
							$winner = $player;
							$this->givePrize($winner);
					    }else{
						    $player->sendMessage(TF::GOLD.'Leider ist dies nicht die gesuchte Zahl! ;(');
					    }
				    }elseif($behavior == 1350){
					    if($message == $qnum){
							$player->sendMessage(TF::GREEN.TF::BOLD.'Herzlichen Glückwunsch! Du hast gewonnen!');
							$winner = $player;
							$this->givePrize($winner);
					    }else{
							$player->sendMessage(TF::GOLD.'Leider ist dies nicht die gesuchte Zahl! ;(');
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

	public function givePrize($winner){
		$dir = $this->getDataFolder();
		$file = file_get_contents($dir.'imbusy.txt');
		$getinfo = unserialize($file);
		extract($getinfo);
		unlink($dir.'imbusy.txt');
		$name = $winner->getDisplayName();
		if($behavior == 5){
			$winner->sendMessage("Herzlichen Glückwunsch, $name!");
			$winner->sendMessage("Die gesuchte Zahl war: $num");
		}elseif($behavior == 1350){
			$winner->sendMessage("Herzlichen Glückwunsch, $name!");
			$winner->sendMessage("Die gesuchte Quadratzahl von $qnum war $numq");
		}
	}
	
	public function onDisable(){
		$dir = $this->getDataFolder();
		if(file_exists($dir.'imbusy.txt')){
			unlink($dir.'imbusy.txt');
			$this->getLogger()->debug('Deleted temp file!');
		}
		$this->getLogger()->info('Disabled!');
	}
}