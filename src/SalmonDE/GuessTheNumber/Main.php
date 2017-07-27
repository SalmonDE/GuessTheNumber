<?php
declare(strict_types = 1);

namespace SalmonDE\GuessTheNumber;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\lang\BaseLang;

class Main extends PluginBase implements Listener {

    private $currentGame = null;
    private $timer = 60;
    private $baseLang;

    public function onEnable(){
        $this->saveResource('config.yml');
        $this->saveResource($this->getConfig()->get('language'));

        $this->timer = (float) $this->getConfig()->get('timer') * 20;
        $this->baseLang = new BaseLang($this->getConfig()->get('language'), $this->getDataFolder());

        $this->decimalMark = $this->baseLang->get('decimalMark');

        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }
    
    public function startGame(NumberGame $game): bool{
        if($this->isGameRunning()){
            return false;
        }

        $this->currentGame = $game;

        return true;
    }

    public function stopGame(bool $broadcast = true): bool{
        if($this->isGameRunning()){

            if($broadcast){
                $this->getServer()->broadcastMessage($this->getMessage('game.stop'));
            }

            $this->currentGame = null;

            return true;
        }

        return false;
    }

    public function getGame(): NumberGame{
        if(!$this->isGameRunning()){
            throw new \InvalidStateException('There isn\'t any game running!');
        }

        return $this->currentGame;
    }

    public function isGameRunning(): bool{
        return $this->currentGame instanceof NumberGame;
    }

    public function getTimer(): float{
        return $this->timer;
    }

    public function getMessage(string $index): string{
        $this->baseLang->get($index);
    }

    public function onChat(PlayerChatEvent $event){
        if($this->isGameRunning() && $event->getMessage()){
            // check decimal mark
        }
    }

}
