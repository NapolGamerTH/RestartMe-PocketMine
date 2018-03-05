<?php

namespace restartme\event\plugin;

use restartme\RestartMe;

class ServerRestartEvent extends RestartMeEvent{
    /** @var \pocketmine\event\HandlerList */
    public static $handlerList = null;
    /** @var int */
    private $mode;
    /**
     * @param RestartMe $plugin
     * @param int $mode
     */
    public function __construct(RestartMe $plugin, $mode){
        parent::__construct($plugin);
        $this->mode = (int) $mode;
    }
    /**
     * @return int
     */
    public function getMode(){
        return $this->mode;
    }
    /**
     * @param int $mode
     */
    public function setMode($mode){
        $this->mode = (int) $mode;
    }
}
