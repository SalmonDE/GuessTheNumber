<?php
namespace SalmonDE;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\item\Item;
use pocketmine\level\sound\FizzSound;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat as TF;
use pocketmine\utils\Utils;
use SalmonDE\Tasks\CheckNumberTask;
use SalmonDE\Updater\CheckVersionTask;
use SalmonDE\Updater\UpdaterTask;

class Number extends PluginBase implements Listener
{

	public function onEnable(){
		  $this->getServer()->getScheduler()->scheduleAsyncTask(new CheckVersionTask($this));
	    @mkdir($this->getDataFolder());
	    $this->saveResource('config.yml');
			if(!file_exists($this->getDataFolder().'messages.ini')){
		      $this->saveResource($this->getConfig()->get('Language').'.ini');
			    rename($this->getDataFolder().$this->getConfig()->get('Language').'.ini', $this->getDataFolder().'messages.ini');
		  }
			$this->min = $this->getConfig()->get('Min');
			$this->max = $this->getConfig()->get('Max');
		  $this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

  public function getMessages(){
		  if(file_exists($this->getDataFolder().'messages.ini')){
			    return parse_ini_file($this->getDataFolder().'messages.ini', true);
			}
	}

	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
		  if(strtolower($cmd->getName()) == 'guessgamesolution'){
          if($sender->hasPermission('guessthenumber.solution')){
				      if(isset($this->information)){
					        $sender->sendMessage(TF::BLUE.str_ireplace('{value}', $this->information['solution'], $this->getMessages()['solution']));
				      }else{
					        $sender->sendMessage(TF::RED.$this->getMessages()['nogameactive']);
				      }
			      }
		  }elseif(strtolower($cmd->getName()) == 'guessgameabort'){
				  if($sender->hasPermission('guessthenumber.abort')){
					    if(isset($this->information)){
					        unset($this->information);
				          $this->getServer()->broadcastMessage(TF::RED.TF::BOLD.$this->getMessages()['gameaborted']);
						      return true;
					    }else{
						      $sender->sendMessage(TF::GOLD.$this->getMessages()['nogameactive']);
						      return true;
					    }
				    }else{
					    $sender->sendMessage(TF::GOLD.$this->getMessages()['nogameactive']);
					    return true;
				    }
		  }elseif(isset($this->information)){
			    $sender->sendMessage(TF::RED.$this->getMessages()['gamealreadyactive']);
		  }else{
		      if(strtolower($cmd->getName()) == 'guessgame'){
              $this->information = ['behavior' => 1, 'solution' => mt_rand($this->min, $this->max)];
				      $this->getServer()->broadcastMessage("\n");
				      $this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD.$this->getMessages()['header']);
				      $this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD.str_ireplace(['{min}', '{max}'], [$this->min, $this->max], $this->getMessages()['firstline']));
				      $this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD.str_ireplace(['{min}', '{max}'], [$this->min, $this->max], $this->getMessages()['secondline']));
				      $this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD.str_ireplace(['{min}', '{max}'], [$this->min, $this->max], $this->getMessages()['thirdline']));
				      $this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD.str_ireplace(['{min}', '{max}'], [$this->min, $this->max], $this->getMessages()['fourthline']));
				      $this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD.$this->getMessages()['bottom']);
				      $this->getServer()->broadcastMessage("\n");
				      $this->getServer()->broadcastMessage(TF::RED.$this->getMessages()['advice']);
		          return true;
		      }elseif(strtolower($cmd->getName()) == 'guessgamesquare'){
		          $num = mt_rand(1, 20);
              $this->information = ['behavior' => 2, 'num' => $num, 'solution' => $num * $num];
				      $this->getServer()->broadcastMessage("\n");
				      $this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD.$this->getMessages()['Square']['header']);
				      $this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD.str_ireplace('{value}', $this->information['num'], $this->getMessages()['Square']['firstline']));
				      $this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD.str_ireplace('{value}', $this->information['num'], $this->getMessages()['Square']['secondline']));
				      $this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD.str_ireplace('{value}', $this->information['num'], $this->getMessages()['Square']['thirdline']));
				      $this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD.str_ireplace('{value}', $this->information['num'], $this->getMessages()['Square']['fourthline']));
				      $this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD.$this->getMessages()['Square']['bottom']);
				      $this->getServer()->broadcastMessage("\n");
				      $this->getServer()->broadcastMessage(TF::RED.$this->getMessages()['advice']);
					    return true;
		      }
		  }
	}

	public function onChat(PlayerChatEvent $event){
		  if(isset($this->information)){
			    if(is_numeric($event->getMessage())){
						  if(!isset($this->queue[$event->getPlayer()->getName()])){
						      $this->queue[$event->getPlayer()->getName()] = 1;
						      $event->getPlayer()->sendMessage(TF::LIGHT_PURPLE.str_ireplace('{value}', $this->getConfig()->get('Timer'), $this->getMessages()['timer']));
					        $this->getServer()->getScheduler()->scheduleDelayedTask(new CheckNumberTask($this, $event->getPlayer(), $event->getMessage()), $this->getConfig()->get('Timer') * 20);
						  }else{
								  $event->getPlayer()->sendMessage(TF::RED.$this->getMessages()['inqueue']);
							}
							$event->setCancelled();
			    }
		  }
  }

	public function onJoin(PlayerJoinEvent $event){
		  if(isset($this->information)){
			    $player = $event->getPlayer();
			    if($this->information['behavior'] == 1){
				      $player->sendMessage("\n");
			 	      $player->sendMessage(TF::GOLD.TF::BOLD.$this->getMessages()['header']);
				      $player->sendMessage(TF::AQUA.TF::BOLD.str_ireplace(['{min}', '{max}'], [$this->min, $this->max], $this->getMessages()['firstline']));
				      $player->sendMessage(TF::AQUA.TF::BOLD.str_ireplace(['{min}', '{max}'], [$this->min, $this->max], $this->getMessages()['secondline']));
				      $player->sendMessage(TF::AQUA.TF::BOLD.str_ireplace(['{min}', '{max}'], [$this->min, $this->max], $this->getMessages()['thirdline']));
				      $player->sendMessage(TF::AQUA.TF::BOLD.str_ireplace(['{min}', '{max}'], [$this->min, $this->max], $this->getMessages()['fourthline']));
				      $player->sendMessage(TF::GOLD.TF::BOLD.$this->getMessages()['bottom']);
              $player->sendMessage("\n");
				      $player->sendMessage(TF::RED.$this->getMessages()['advice']);
			    }elseif($this->information['behavior'] == 2){
				      $player->sendMessage(TF::GOLD.TF::BOLD."\n");
				      $player->sendMessage(TF::GOLD.TF::BOLD.$this->getMessages()['Square']['header']);
				      $player->sendMessage(TF::AQUA.TF::BOLD.str_ireplace('{value}', $this->information['num'], $this->getMessages()['Square']['firstline']));
				      $player->sendMessage(TF::AQUA.TF::BOLD.str_ireplace('{value}', $this->information['num'], $this->getMessages()['Square']['secondline']));
				      $player->sendMessage(TF::AQUA.TF::BOLD.str_ireplace('{value}', $this->information['num'], $this->getMessages()['Square']['thirdline']));
				      $player->sendMessage(TF::AQUA.TF::BOLD.str_ireplace('{value}', $this->information['num'], $this->getMessages()['Square']['fourthline']));
				      $player->sendMessage(TF::GOLD.TF::BOLD.$this->getMessages()['Square']['bottom']);
              $player->sendMessage("\n");
				      $player->sendMessage(TF::RED.$this->getMessages()['advice']);
			    }
		  }
	}

	public function givePrize(Player $winner){
		  $name = $winner->getDisplayName();
		  if($this->information['behavior'] == 1){
			    foreach($this->getServer()->getOnlinePlayers() as $player){
				      $player->getLevel()->addSound(new FizzSound($player->getPosition()));
			    }
			    $this->getServer()->broadcastMessage(TF::GREEN.TF::BOLD.str_ireplace('{value}', $name, $this->getMessages()['congratulation']));
			    $this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD.str_ireplace('{value}', $this->information['solution'], $this->getMessages()['rightnumber']));
					unset($this->information);
			    $item = explode(':', $this->getConfig()->get('Item'));
			    $itemname = Item::get($item[0])->getName();
			    $winner->getInventory()->addItem(new Item($item[0], $item[1], $item[2]));
		      $winner->sendMessage(TF::GREEN.TF::BOLD.str_ireplace(['{count}', '{itemname}'], [$item[2], $itemname], $this->getMessages()['winnermessage']));
		  }elseif($this->information['behavior'] == 2){
			    foreach($this->getServer()->getOnlinePlayers() as $player){
				      $player->getLevel()->addSound(new FizzSound($player->getPosition()));
			    }
			    $this->getServer()->broadcastMessage(TF::GREEN.TF::BOLD.str_ireplace('{value}', $name, $this->getMessages()['congratulation']));
			    $this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD.str_ireplace(['{num}', '{solution}'], [$this->information['num'], $this->information['solution']], $this->getMessages()['Square']['rightnumber']));
					unset($this->information);
			    $item = explode(':', $this->getConfig()->get('SquareItem'));
			    $itemname = Item::get($item[0])->getName();
			    $winner->getInventory()->addItem(new Item($item[0], $item[1], $item[2]));
		      $winner->sendMessage(TF::LIGHT_PURPLE.TF::BOLD.str_ireplace(['{count}', '{itemname}'], [$item[2], $itemname], $this->getMessages()['winnermessage']));
      }
	}

	public function update(){
			$this->getServer()->getScheduler()->scheduleTask(new UpdaterTask($this, $this->getDescription()->getVersion()));
	}
}
