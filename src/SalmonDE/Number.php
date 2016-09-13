<?php
namespace SalmonDE;

use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\item\Item;
use pocketmine\level\sound\FizzSound;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat as TF;
use SalmonDE\Tasks\CheckNumberTask;

class Number extends PluginBase implements Listener{

	public function onEnable(){
	    @mkdir($this->getDataFolder());
	    $this->saveResource('config.yml');
			if(!file_exists($this->getDataFolder().'messages.ini')){
		      $this->saveResource($this->getConfig()->get('Language').'.ini');
			    rename($this->getDataFolder().$this->getConfig()->get('Language').'.ini', $this->getDataFolder().'messages.ini');
		  }
		  $this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
		  if(strtolower($cmd->getName()) == 'guessgamesolution'){
          if($sender->hasPermission('guessthenumber.solution')){
				  if(isset($this->information)){
				      if($this->information['behavior'] == 5){
					        $sender->sendMessage(TF::BLUE.$normalsolution.(string) $this->information['num']);
				      }elseif($this->information['behavior'] == 1350){
					        $sender->sendMessage(TF::BLUE.$squaresolution.(string) $this->information['numq']);
				      }
				  }else{
					    $sender->sendMessage(TF::RED.'No Game Active!');
				  }
			  }
		  }elseif(strtolower($cmd->getName()) == 'guessgameabort'){
				  if($sender->hasPermission('guessthenumber.abort')){
					  if(isset($this->information)){
					      unset($this->information);
				        $this->getServer()->broadcastMessage(TF::RED.TF::BOLD.$gameaborted);
						    return true;
					  }else{
						    $sender->sendMessage(TF::GOLD.$nogameactive);
						    return true;
					  }
				  }else{
					  $sender->sendMessage(TF::GOLD.$nopermission);
					  return true;
				  }
		  }elseif(isset($this->information)){
			    $sender->sendMessage(TF::RED.$gamealreadyactive);
		  }else{
		      if(strtolower($cmd->getName()) == 'guessgame'){
				  $min = $this->getConfig()->get('Minimum');
				  $max = $this->getConfig()->get('Maximum');
          $this->information = ['behavior' => 5, 'num' => mt_rand($min, $max)];
				  $firstlinec = str_ireplace($replace, $replaced, $firstline);
				  $this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD."\n");
				  $this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD.$header);
				  $this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD.$firstlinec);
				  $this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD.$secondline);
				  $this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD.$thirdline);
				  $this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD.$fourthline);
				  $this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD.$bottom);
				  $this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD."\n");
				  $this->getServer()->broadcastMessage(TF::RED.$advice);
		      return true;
		    }elseif(strtolower($cmd->getName()) == 'guessgamesquare'){
		        $num = mt_rand(1,20);
            $this->information = ['behavior' => 1350, 'qnum' => $num, 'numq' => $num * $num];
				    $this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD."\n");
				    $this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD.$qheader);
				    $this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD.$qfirstline);
				    $this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD.$qsecondline);
				    $this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD.$qthirdline);
				    $this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD.$qfourthline);
				    $this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD.$qbottom);
				    $this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD."\n");
				    $this->getServer()->broadcastMessage(TF::RED.$advice);
					  return true;
		    }
		}
	}

	public function onChat(PlayerChatEvent $event){
		  if(isset($this->information)){
				  $min = $this->getConfig()->get('Minimum');
				  $max = $this->getConfig()->get('Maximum');
			    $player = $event->getPlayer();
			    $message = $event->getMessage();
			    if(is_numeric($message)){
					    $time = $this->getConfig()->get('Timer') * 20;
					    $player->sendMessage(TF::LIGHT_PURPLE.'In '.$this->getConfig()->get('Timer').' Sekunden erfährst du, ob es richtig ist!');
					    $task = new CheckNumberTask($this, $player, $message);
					    $this->getServer()->getScheduler()->scheduleDelayedTask($task, $time);
					    $event->setCancelled();
			    }
		  }
  }

	public function onJoin(PlayerJoinEvent $event){
		  if(isset($this->information)){
			    $player = $event->getPlayer();
			    if($this->information['behavior'] == 5){
				      $player->sendMessage(TF::GOLD.TF::BOLD."\n");
			 	      $player->sendMessage(TF::GOLD.TF::BOLD.$header);
				      $player->sendMessage(TF::AQUA.TF::BOLD.$firstline);
				      $player->sendMessage(TF::AQUA.TF::BOLD.$secondline);
				      $player->sendMessage(TF::AQUA.TF::BOLD.$thirdline);
				      $player->sendMessage(TF::AQUA.TF::BOLD.$fourthline);
				      $player->sendMessage(TF::GOLD.TF::BOLD.$bottom);
              $player->sendMessage(TF::GOLD.TF::BOLD."\n");
				      $player->sendMessage(TF::RED.$advice);
			    }elseif($this->information['behavior'] == 1350){
				      $player->sendMessage(TF::GOLD.TF::BOLD."\n");
				      $player->sendMessage(TF::GOLD.TF::BOLD.$qheader);
				      $player->sendMessage(TF::AQUA.TF::BOLD.$qfirstline);
				      $player->sendMessage(TF::AQUA.TF::BOLD.$qsecondline);
				      $player->sendMessage(TF::AQUA.TF::BOLD.$qthirdline);
				      $player->sendMessage(TF::AQUA.TF::BOLD.$qfourthline);
				      $player->sendMessage(TF::GOLD.TF::BOLD.$qbottom);
              $player->sendMessage(TF::GOLD.TF::BOLD."\n");
				      $player->sendMessage(TF::RED.$advice);
			    }
		  }
	}

	public function givePrize($winner){
		  $name = $winner->getDisplayName();
		  if($information['behavior'] == 5){
			    foreach($this->getServer()->getOnlinePlayers() as $player){
				      $player->getLevel()->addSound(new FizzSound($player->getPosition()));
			    }
			    unset($this->information);
			    $this->getServer()->broadcastMessage(TF::GREEN.TF::BOLD.$congratulation);
			    $this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD.$rightnumber);
			    $item = explode(':', $this->getConfig()->get('Item'));
			    $itemname = Item::get($data[0])->getName();
			    $winner->getInventory()->addItem(new Item($item[0], $item[1], $item[2]));
		      $winner->sendMessage(TF::GREEN.TF::BOLD.$winnermessage);
		  }elseif($behavior == 1350){
			    foreach($this->getServer()->getOnlinePlayers() as $player){
				      $player->getLevel()->addSound(new FizzSound($player->getPosition()));
			    }
			    unset($this->information);
			    $this->getServer()->broadcastMessage(TF::GREEN.TF::BOLD.$qcongratulation);
			    $this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD.$qrightnumber);
			    $item = explode(':', $this->getConfig()->get('SquareItem'));
			    $itemname = Item::get($item[0])->getName();
			    $winner->getInventory()->addItem(new Item($item[0], $item[1], $item[2]));
		      $winner->sendMessage(TF::LIGHT_PURPLE.TF::BOLD.$qwinnermessage);
      }
	}
}
