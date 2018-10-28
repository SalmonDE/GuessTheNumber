<?php
declare(strict_types = 1);

namespace SalmonDE\GuessTheNumber;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\utils\TextFormat as TF;
use SalmonDE\GuessTheNumber\Events\NumberGameStartEvent;
use SalmonDE\GuessTheNumber\Events\PlayerAnswerEvent;
use SalmonDE\GuessTheNumber\Events\PlayerWinEvent;
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

    public function setAnswering(string $name, bool $value = true): void{
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
    public function onChat(PlayerChatEvent $event): void{
        if($event->isCancelled()){
            return;
        }

        if($this->owner->isGameRunning()){
            if($this->owner->getCurrentGame()->isValidAnswer($event->getMessage(), $this->owner->getDecimalMark(), $this->owner->getThousandSeparator())){

                if(!$event->getPlayer()->hasPermission($this->owner->getCurrentGame()->getPlayPermission())){
                    $event->getPlayer()->sendMessage(TF::RED.$this->owner->getMessage('general.notAllowedToPlay'));
                    return;
                }

                if($this->isAnswering($event->getPlayer()->getName())){
                    $event->getPlayer()->sendMessage(TF::RED.$this->owner->getMessage('general.alreadyChecking'));
                }else{
                    $this->owner->getServer()->getPluginManager()->callEvent($answerEvent = new PlayerAnswerEvent($this->owner, $this->owner->getCurrentGame(), $event->getPlayer(), $event->getMessage()));

                    if(!$answerEvent->isCancelled()){
                        $this->owner->getScheduler()->scheduleDelayedTask($task = new AnswerCheckTask($this->owner, $event->getPlayer(), $event->getMessage()), $this->owner->getTimer());
                        $this->setAnswering($event->getPlayer()->getName());

                        $event->getPlayer()->sendMessage(TF::GOLD.$this->owner->getMessage('general.checking', TF::AQUA.($this->owner->getTimer() / 20).TF::GOLD).TF::RESET);
                    }
                }

                if($answerEvent ?? false){
                    $event->setCancelled(!$answerEvent->showChatMessage());
                }else{
                    $event->setCancelled();
                }
            }
        }
    }

    /**
     * @priority MONITOR
     */
    public function onJoin(PlayerJoinEvent $event): void{
        if($this->owner->isGameRunning()){
            $this->owner->getCurrentGame()->announceGame($this->owner, [$event->getPlayer()]);
        }
    }

    /**
     * @priority MONITOR
     */
    public function onGameStart(NumberGameStartEvent $event): void{
        if(!$event->isCancelled()){
            $this->owner->getLogger()->notice($event->getGame()->getAnnounceMessage($this->owner));
        }
    }

    /**
     * @priority MONITOR
     * @
     */
    public function onPlayerWin(PlayerWinEvent $event): void{
        if(!$event->isCancelled()){
            $msg = TF::GREEN.$this->owner->getMessage('answer.right', $event->getPlayer()->getDisplayName(), $event->getAnswer());
            $this->owner->getLogger()->notice($msg);
        }
    }
}
