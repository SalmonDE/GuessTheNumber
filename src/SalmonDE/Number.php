<?php
namespace SalmonDE;

use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\level\sound\AnvilFallSound;
use pocketmine\level\sound\FizzSound;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TF;

class Number extends PluginBase implements Listener{

	private $winner;

	public function onEnable(){
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
		if(file_exists($dir.'imbusy.txt')){
			$sender->sendMessage(TF::RED.'Geblockt! Ratespiel schon im Gange!');
		}else{
		    $tempfile = fopen($dir.'imbusy.txt','w');
		    if($cmd == 'guessgame' || $cmd == 'Guessgame'){
				$min = $this->getConfig()->get('Minimum');
				$max = $this->getConfig()->get('Maximum');
		        $status = 1;
		        $behavior = 5;
		        $num = mt_rand($min,$max);
		        $store = array(
		                    'status' => "$status",
					        'num' => "$num",
					        'behavior' => "$behavior"
		        );
		        fwrite($tempfile,serialize($store));
				$this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD.'\n');
				$this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD.'\n');
				$this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD.'*-------Zahlenquiz-------*');
				$this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD.'Schreibe in den Chat eine');
				$this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD."Zahl zwischen§d $min §bund§d $max".'§b,');
				$this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD.'wenn es die Zahl ist, die gesucht');
				$this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD.'wird, gewinnst du etwas! :D');
				$this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD.'*-------Zahlenquiz-------*');
				$this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD.'\n');
				$this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD.'\n');
				if($sender->hasPermission('guessthenumber.solution')){
					$sender->sendMessage(TF::BLUE.'Gesuchte Zahl: '.(string) $num);
				}
		        return true;
		    }elseif($cmd == 'guessgamesquare' || $cmd == 'Guessgamesquare'){
		        $status = 1;
		        $behavior = 1350;
		        $qnum = mt_rand(1,20);
		        $numq = $qnum * $qnum;
				$store = array(
		                    'status' => "$status",
							'qnum' => "$qnum",
					        'numq' => "$numq",
					        'behavior' => "$behavior"
		        );
				fwrite($tempfile,serialize($store));
				$this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD.'\n');
				$this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD.'\n');
				$this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD.'*---Quadratzahlenquiz---*');
				$this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD.'Schreibe in den Chat die');
				$this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD."Quadratzahl von $qnum".'§b,');
				$this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD.'wenn es die Zahl ist, die gesucht');
				$this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD.'wird, gewinnst du etwas! :D');
				$this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD.'*---Quadratzahlenquiz---*');
				$this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD.'\n');
				$this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD.'\n');
				if($sender->hasPermission('guessthenumber.solution')){
					$sender->sendMessage(TF::BLUE.'Die Quadratzahl von '.(string) $qnum.' ist: '.(string) $numq);
				}
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
					$player->sendMessage(TF::LIGHT_PURPLE.'In 5 Sekunden erfährst du, ob es richtig ist!');
					sleep(5);
				    if($behavior == 5){
					    if($message == $num){
							$winner = $player;
							$this->givePrize($winner);
					    }else{
						    $player->sendMessage(TF::GOLD.'Leider ist dies nicht die gesuchte Zahl! ;(');
							$player->getLevel()->addSound(new AnvilFallSound($player->getPosition()));
					    }
				    }elseif($behavior == 1350){
					    if($message == $numq){
							$player->sendMessage(TF::GREEN.'Du hast gewonnen!');
							$winner = $player;
							$this->givePrize($winner);
					    }else{
							$player->sendMessage(TF::GOLD.'Leider ist dies nicht die gesuchte Zahl! ;(');
						}
				    }else{
					    $this->getLogger()->critical(TF::DARK_RED.'Error 1! Not valid behavior: '.TF::AQUA."$behavior");
				    }
			    }else{
				    $player->sendMessage(TF::RED.'Du musst eine numerische Zahl in den Chat schreiben, damit du beim Quiz mitmachen kannst!');
					$player->getLevel()->addSound(new AnvilFallSound($player->getPosition()));
			    }
				$event->setCancelled();
		    }
		}
	}

	public function givePrize($winner){
		$dir = $this->getDataFolder();
		$file = file_get_contents($dir.'imbusy.txt');
		$getinfo = unserialize($file);
		extract($getinfo);
		$name = $winner->getDisplayName();
		sleep(1);
		$this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD."Das Quiz ist vorbei ...");
		sleep(4);
		unlink($dir.'imbusy.txt');
		if($behavior == 5){
			foreach($this->getServer()->getOnlinePlayers() as $players){
				$players->getLevel()->addSound(new FizzSound($players->getPosition()));
			}
			$this->getServer()->broadcastMessage(TF::GREEN.TF::BOLD."Herzlichen Glückwunsch, $name!\n");
			$this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD."Die gesuchte Zahl war:§b $num"."§d.");
		}elseif($behavior == 1350){
			foreach($this->getServer()->getOnlinePlayers() as $players){
				$players->getLevel()->addSound(new FizzSound($players->getPosition()));
			}
			$this->getServer()->broadcastMessage(TF::GREEN.TF::BOLD."Herzlichen Glückwunsch, $name!");
			$this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD."Die gesuchte Quadratzahl von§9 $qnum war§b $numq"."§d.");
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