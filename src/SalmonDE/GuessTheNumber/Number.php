<?php
namespace SalmonDE\GuessTheNumber;

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
use SalmonDE\GuessTheNumber\Tasks\CheckNumberTask;

class Number extends PluginBase implements Listener
{

	public function onEnable(){
	    @mkdir($this->getDataFolder());
	    $this->saveResource('config.yml');
			if(!file_exists($this->getDataFolder().'messages.ini')){
		      $this->saveResource($this->getConfig()->get('Language').'.ini');
			    rename($this->getDataFolder().$this->getConfig()->get('Language').'.ini', $this->getDataFolder().'messages.ini');
		  }
			$this->min = $this->getConfig()->get('Min');
			$this->max = $this->getConfig()->get('Max');
		  $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $updateManager = new \SalmonDE\Updater\UpdateManager($this);
	}

  public function getMessages(){
		  if(file_exists($this->getDataFolder().'messages.ini')){
			    return parse_ini_file($this->getDataFolder().'messages.ini', true);
			}
	}

	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
		  if(count($args) === 1){
		      if(strtolower($args[0]) == 'solution'){
              if($sender->hasPermission('guessthenumber.solution')){
				          if(isset($this->information)){
					            $sender->sendMessage(TF::BLUE.str_ireplace('{value}', $this->information['solution'], $this->getMessages()['solution']));
				          }else{
					            $sender->sendMessage(TF::RED.$this->getMessages()['nogameactive']);
				          }
			        }
			        return true;
		      }elseif(strtolower($args[0]) == 'abort'){
				      if($sender->hasPermission('guessthenumber.abort')){
					        if(isset($this->information)){
					            unset($this->information);
				              $this->getServer()->broadcastMessage(TF::RED.TF::BOLD.$this->getMessages()['gameaborted']);
					        }else{
						          $sender->sendMessage(TF::GOLD.$this->getMessages()['nogameactive']);
					        }
				        }else{
					          $sender->sendMessage(TF::GOLD.$this->getMessages()['nogameactive']);
				        }
			                return true;
		      }elseif(isset($this->information)){
			        $sender->sendMessage(TF::RED.$this->getMessages()['gamealreadyactive']);
							return true;
		      }else{
		          if(strtolower($args[0]) == 'normal'){
                  $this->information = ['behavior' => 1, 'solution' => mt_rand($this->min, $this->max)];
				          $this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD.$this->getMessages()['header']);
				          $this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD.str_replace(['{min}', '{max}'], [$this->min, $this->max], $this->getMessages()['firstline']));
				          $this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD.str_replace(['{min}', '{max}'], [$this->min, $this->max], $this->getMessages()['secondline']));
				          $this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD.str_replace(['{min}', '{max}'], [$this->min, $this->max], $this->getMessages()['thirdline']));
				          $this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD.str_replace(['{min}', '{max}'], [$this->min, $this->max], $this->getMessages()['fourthline']));
				          $this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD.$this->getMessages()['bottom']);
				          $this->getServer()->broadcastMessage(TF::RED.$this->getMessages()['advice']);
		              return true;
		          }elseif(strtolower($args[0]) == 'square'){
		              $num = mt_rand(1, 20);
                  $this->information = ['behavior' => 2, 'solution' => $num * $num];
				          $this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD.$this->getMessages()['Square']['header']);
				          $this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD.str_replace('{value}', $num, $this->getMessages()['Square']['firstline']));
				          $this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD.str_replace('{value}', $num, $this->getMessages()['Square']['secondline']));
				          $this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD.str_replace('{value}', $num, $this->getMessages()['Square']['thirdline']));
				          $this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD.str_replace('{value}', $num, $this->getMessages()['Square']['fourthline']));
				          $this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD.$this->getMessages()['Square']['bottom']);
				          $this->getServer()->broadcastMessage(TF::RED.$this->getMessages()['advice']);
					        return true;
		          }elseif(strtolower($args[0]) == 'plus'){
								  $num = mt_rand(1, 1000);
									$num2 = mt_rand(1, 1000);
								  $this->information = ['behavior' => 3, 'solution' => $num + $num2, 'num1' => $num, 'num2' => $num2]; # Idea by adalin3 (Anastasia)
								  $this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD.$this->getMessages()['Plus']['header']);
								  $this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD.str_replace(['{value}', '{value2}'], [$num, $num2], $this->getMessages()['Plus']['firstline']));
								  $this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD.str_replace(['{value}', '{value2}'], [$num, $num2], $this->getMessages()['Plus']['secondline']));
								  $this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD.str_replace(['{value}', '{value2}'], [$num, $num2], $this->getMessages()['Plus']['thirdline']));
								  $this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD.str_replace(['{value}', '{value2}'], [$num, $num2], $this->getMessages()['Plus']['fourthline']));
								  $this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD.$this->getMessages()['Plus']['bottom']);
								  $this->getServer()->broadcastMessage(TF::RED.$this->getMessages()['advice']);
								  return true;
							}elseif(strtolower($args[0]) == 'minus'){
								  $num = mt_rand(1, 2000);
								  $num2 = mt_rand(1, 1000);
								  $this->information = ['behavior' => 4, 'solution' => $num - $num2, 'num1' => $num, 'num2' => $num2];
								  $this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD.$this->getMessages()['Minus']['header']);
								  $this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD.str_replace(['{value}', '{value2}'], [$num, $num2], $this->getMessages()['Minus']['firstline']));
								  $this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD.str_replace(['{value}', '{value2}'], [$num, $num2], $this->getMessages()['Minus']['secondline']));
								  $this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD.str_replace(['{value}', '{value2}'], [$num, $num2], $this->getMessages()['Minus']['thirdline']));
								  $this->getServer()->broadcastMessage(TF::AQUA.TF::BOLD.str_replace(['{value}', '{value2}'], [$num, $num2], $this->getMessages()['Minus']['fourthline']));
								  $this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD.$this->getMessages()['Minus']['bottom']);
								  $this->getServer()->broadcastMessage(TF::RED.$this->getMessages()['advice']);
								  return true;
							}else{
								  return false;
							}
		      }
		  }else{
			    return false;
		  }
	}

	public function onChat(PlayerChatEvent $event){
		  if(isset($this->information)){
			    if(is_numeric($event->getMessage())){
						  if(!isset($this->queue[$event->getPlayer()->getName()])){
						      $event->getPlayer()->sendMessage(TF::LIGHT_PURPLE.str_ireplace('{value}', $this->getConfig()->get('Timer'), $this->getMessages()['timer']));
									$task = new CheckNumberTask($this, $event->getPlayer(), $event->getMessage());
					        $this->getServer()->getScheduler()->scheduleDelayedTask($task, $this->getConfig()->get('Timer') * 20);
									$this->queue[$event->getPlayer()->getName()] = $task->getTaskId();
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
			 	      $player->sendMessage(TF::GOLD.TF::BOLD.$this->getMessages()['header']);
				      $player->sendMessage(TF::AQUA.TF::BOLD.str_ireplace(['{min}', '{max}'], [$this->min, $this->max], $this->getMessages()['firstline']));
				      $player->sendMessage(TF::AQUA.TF::BOLD.str_ireplace(['{min}', '{max}'], [$this->min, $this->max], $this->getMessages()['secondline']));
				      $player->sendMessage(TF::AQUA.TF::BOLD.str_ireplace(['{min}', '{max}'], [$this->min, $this->max], $this->getMessages()['thirdline']));
				      $player->sendMessage(TF::AQUA.TF::BOLD.str_ireplace(['{min}', '{max}'], [$this->min, $this->max], $this->getMessages()['fourthline']));
				      $player->sendMessage(TF::GOLD.TF::BOLD.$this->getMessages()['bottom']);
				      $player->sendMessage(TF::RED.$this->getMessages()['advice']);
			    }elseif($this->information['behavior'] == 2){
				      $player->sendMessage(TF::GOLD.TF::BOLD.$this->getMessages()['Square']['header']);
				      $player->sendMessage(TF::AQUA.TF::BOLD.str_ireplace('{value}', sqrt($this->information['solution']), $this->getMessages()['Square']['firstline']));
				      $player->sendMessage(TF::AQUA.TF::BOLD.str_ireplace('{value}', sqrt($this->information['solution']), $this->getMessages()['Square']['secondline']));
				      $player->sendMessage(TF::AQUA.TF::BOLD.str_ireplace('{value}', sqrt($this->information['solution']), $this->getMessages()['Square']['thirdline']));
				      $player->sendMessage(TF::AQUA.TF::BOLD.str_ireplace('{value}', sqrt($this->information['solution']), $this->getMessages()['Square']['fourthline']));
				      $player->sendMessage(TF::GOLD.TF::BOLD.$this->getMessages()['Square']['bottom']);
				      $player->sendMessage(TF::RED.$this->getMessages()['advice']);
			    }elseif($this->information['behavior'] == 3){
						  $player->sendMessage(TF::GOLD.TF::BOLD.$this->getMessages()['Plus']['header']);
						  $player->sendMessage(TF::AQUA.TF::BOLD.str_replace(['{value}', '{value2}'], [$this->information['num1'], $this->information['num2']], $this->getMessages()['Plus']['firstline']));
						  $player->sendMessage(TF::AQUA.TF::BOLD.str_replace(['{value}', '{value2}'], [$this->information['num1'], $this->information['num2']], $this->getMessages()['Plus']['secondline']));
						  $player->sendMessage(TF::AQUA.TF::BOLD.str_replace(['{value}', '{value2}'], [$this->information['num1'], $this->information['num2']], $this->getMessages()['Plus']['thirdline']));
						  $player->sendMessage(TF::AQUA.TF::BOLD.str_replace(['{value}', '{value2}'], [$this->information['num1'], $this->information['num2']], $this->getMessages()['Plus']['fourthline']));
						  $player->sendMessage(TF::GOLD.TF::BOLD.$this->getMessages()['Plus']['bottom']);
						  $player->sendMessage(TF::RED.$this->getMessages()['advice']);
					}elseif($this->information['behavior'] == 4){
						  $player->sendMessage(TF::GOLD.TF::BOLD.$this->getMessages()['Minus']['header']);
						  $player->sendMessage(TF::AQUA.TF::BOLD.str_replace(['{value}', '{value2}'], [$this->information['num1'], $this->information['num2']], $this->getMessages()['Minus']['firstline']));
						  $player->sendMessage(TF::AQUA.TF::BOLD.str_replace(['{value}', '{value2}'], [$this->information['num1'], $this->information['num2']], $this->getMessages()['Minus']['secondline']));
						  $player->sendMessage(TF::AQUA.TF::BOLD.str_replace(['{value}', '{value2}'], [$this->information['num1'], $this->information['num2']], $this->getMessages()['Minus']['thirdline']));
						  $player->sendMessage(TF::AQUA.TF::BOLD.str_replace(['{value}', '{value2}'], [$this->information['num1'], $this->information['num2']], $this->getMessages()['Minus']['fourthline']));
						  $player->sendMessage(TF::GOLD.TF::BOLD.$this->getMessages()['Minus']['bottom']);
						  $player->sendMessage(TF::RED.$this->getMessages()['advice']);
					}
		  }
	}

	public function givePrize(Player $winner){
		  foreach($this->queue as $taskid){
				  $this->getServer()->getScheduler()->cancelTask($taskid);
			}
			unset($this->queue);
		  $name = $winner->getDisplayName();
			foreach($this->getServer()->getOnlinePlayers() as $player){
					$player->getLevel()->addSound(new FizzSound($player->getPosition()), [$player]);
			}
			$this->getServer()->broadcastMessage(TF::GREEN.TF::BOLD.str_replace('{value}', $name, $this->getMessages()['congratulation']));
		  if($this->information['behavior'] == 1){
			    $this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD.str_replace('{value}', $this->information['solution'], $this->getMessages()['rightnumber']));
					unset($this->information);
			    $item = explode(':', $this->getConfig()->get('Item'));
			    $itemname = Item::get($item[0])->getName();
		  }elseif($this->information['behavior'] == 2){
			    $this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD.str_replace(['{num}', '{solution}'], [sqrt($this->information['solution']), $this->information['solution']], $this->getMessages()['Square']['rightnumber']));
					unset($this->information);
			    $item = explode(':', $this->getConfig()->get('SquareItem'));
			    $itemname = Item::get($item[0])->getName();
      }elseif($this->information['behavior'] == 3){
				  $this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD.str_replace(['{num}', '{num2}', '{solution}'], [$this->information['num1'], $this->information['num2'], $this->information['solution']], $this->getMessages()['Plus']['rightnumber']));
				  unset($this->information);
				  $item = explode(':', $this->getConfig()->get('PlusItem'));
				  $itemname = Item::get($item[0])->getName();
			}elseif($this->information['behavior'] == 4){
				  $this->getServer()->broadcastMessage(TF::GOLD.TF::BOLD.str_replace(['{num}', '{num2}', '{solution}'], [$this->information['num1'], $this->information['num2'], $this->information['solution']], $this->getMessages()['Minus']['rightnumber']));
			  	unset($this->information);
				  $item = explode(':', $this->getConfig()->get('MinusItem'));
				  $itemname = Item::get($item[0])->getName();
			}else{
	 		   $item = explode(':', '0:0:0');
	 		   $itemname = Item::get($item[0])->getName();
			}
			$winner->getInventory()->addItem(new Item($item[0], $item[1], $item[2]));
			$winner->sendMessage(TF::GREEN.TF::BOLD.str_replace(['{count}', '{itemname}'], [$item[2], $itemname], $this->getMessages()['winnermessage']));
	}
}
