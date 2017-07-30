<?php
namespace SalmonDE\GuessTheNumber;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\utils\TextFormat as TF;
use SalmonDE\GuessTheNumber\Events\PlayerAnswerEvent;
use SalmonDE\GuessTheNumber\Main;
use SalmonDE\GuessTheNumber\Tasks\AnswerCheckTask;

class MainListener implements Listener {

    private $owner;
    private $answeringPlayers = [];

    public function __construct(Main $owner){
        $this->owner = $owner;
    }

    public function getOwner(): Main{
        return $this->owner;
    }

    public function isAnswering(string $name): bool{
        return $this->answeringPlayers[strtolower($name)] ?? false;
    }

    public function setAnswering(string $name, bool $value = true){
        $this->answeringPlayers[strtolower($name)] = $value;

        if(!$value){
            unset($this->answeringPlayers[strtolower($name)]);
        }
    }

    public function getAnsweringPlayers(): array{
        return $this->answeringPlayers;
    }

    /**
     * @priority MONITOR
     */
    public function onChat(PlayerChatEvent $event){
        if($this->owner->isGameRunning()){
            if($this->owner->getCurrentGame()->isValidAnswer($event->getMessage(), $this->owner->getDecimalMark(), $this->owner->getThousandSeparator())){

                if(!$event->getPlayer()->hasPermission($this->owner->getCurrentGame()->getPlayPermission())){
                    $event->getPlayer()->sendMessage(TF::RED.$this->owner->getMessage('general.notAllowedToPlay'));
                    return;
                }

                if($this->isAnswering($event->getPlayer()->getName())){
                    $event->getPlayer()->sendMessage(TF::RED.$this->owner->getMessage('general.alreadyChecking'));
                }else{
                    $this->getServer()->getPluginManager()->callEvent($event = new PlayerAnswerEvent($this, $this->owner->getCurrentGame(), $event->getPlayer(), $event->getMessage()));

                    if(!$event->isCancelled()){
                        $this->owner->getServer()->getScheduler()->scheduleDelayedTask($task = new AnswerCheckTask($this->owner, $event->getPlayer(), $event->getMessage()), $this->owner->getTimer());
                        $this->setAnswering($event->getPlayer()->getName());

                        $event->getPlayer()->sendMessage('general.checking', $this->timer / 20);
                    }
                }

                if($event ?? false){
                    $event->setCancelled(!$event->showChatMessage());
                }else{
                    $event->setCancelled();
                }
            }
        }
    }

    public function onJoin(PlayerJoinEvent $event){
        if($this->owner->isGameRunning()){
            $this->owner->getCurrentGame()->announceGame($this->owner, [$event->getPlayer()]);
        }
    }

}
