<?php

namespace restartme\utils;

use restartme\event\plugin\PauseTimerEvent;
use restartme\event\plugin\ServerRestartEvent;
use restartme\event\plugin\SetTimeEvent;
use restartme\RestartMe;

class Timer{
    /** @var RestartMe */
    private $plugin;
    /** @var bool|mixed */
    private $time;
    /** @var bool */
    private $paused = false;
    /**
     * @param RestartMe $plugin
     */
    public function __construct(RestartMe $plugin){
        $this->plugin = $plugin;
        $this->time = $plugin->getConfig()->get("restartInterval") * 60;
    }
    /**
     * @return bool|mixed
     */
    public function getTime(){
        return $this->time;
    }
    /**
     * @param int $seconds
     */
    public function setTime($seconds){
        $this->plugin->getServer()->getPluginManager()->callEvent(new SetTimeEvent($this->plugin, $this->getTime(), $seconds));
        $this->time = $seconds;
    }
    /**
     * @param int $seconds
     */
    public function addTime($seconds){
        $this->setTime($this->getTime() + $seconds);
    }
    /**
     * @param int $seconds
     */
    public function subtractTime($seconds){
        $this->setTime($this->getTime() - $seconds);
    }
    /**
     * @param int $mode
     */
    public function initiateRestart($mode){
        $event = new ServerRestartEvent($this->plugin, $mode);
        $this->plugin->getServer()->getPluginManager()->callEvent($event);
        switch($event->getMode()){
            case RestartMe::NORMAL:
                foreach($this->plugin->getServer()->getOnlinePlayers() as $player){
                    $player->kick($this->plugin->getConfig()->get("quitMessage"), false);
                }
                $this->plugin->getServer()->getLogger()->info($this->plugin->getConfig()->get("quitMessage"));
                break;
            case RestartMe::OVERLOADED:
                foreach($this->plugin->getServer()->getOnlinePlayers() as $player){
                    $player->kick($this->plugin->getConfig()->get("overloadQuitMessage"), false);
                }
                $this->plugin->getServer()->getLogger()->info($this->plugin->getConfig()->get("overloadQuitMessage"));
            break;
        }
        $this->plugin->getServer()->shutdown();
    }
    /**
     * @return bool
     */
    public function isPaused(){
        return $this->paused === true;
    }
    /**
     * @param bool $value
     */
    public function setPaused($value){
        $event = new PauseTimerEvent($this->plugin, $value);
        $this->plugin->getServer()->getPluginManager()->callEvent($event);
        $this->paused = $event->getValue();
    }
    /**
     * @return string
     */
    public function getFormattedTime(){
        $time = Utils::toArray($this->getTime());
        return $time[0]." hr ".$time[1]." min ".$time[2]." sec";
    }
    /**
     * @param string $message
     * @param string $messageType
     */
    public function broadcastTime($message, $messageType){
        $time = Utils::toArray($this->getTime());
        $outMessage = str_replace(
            [
                "{RESTART_FORMAT_TIME}",
                "{RESTART_HOUR}",
                "{RESTART_MINUTE}",
                "{RESTART_SECOND}",
                "{RESTART_TIME}"
            ],
            [
                $this->getFormattedTime(),
                $time[0],
                $time[1],
                $time[2],
                $this->getTime()
            ],
            $message
        );
        switch(strtolower($messageType)){
            case "chat":
                $this->plugin->getServer()->broadcastMessage($outMessage);
                break;
            case "popup":
                $this->plugin->getServer()->broadcastPopup($outMessage);
                break;
        }
    }
}
