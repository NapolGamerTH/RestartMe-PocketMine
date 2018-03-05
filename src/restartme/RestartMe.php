<?php

namespace restartme;

use pocketmine\plugin\PluginBase;
use restartme\command\RestartMeCommand;
use restartme\task\AutoBroadcastTask;
use restartme\task\CheckMemoryTask;
use restartme\task\RestartServerTask;
use restartme\utils\Timer;

class RestartMe extends PluginBase{
    const NORMAL = 0;
    const OVERLOADED = 1;
    /** @var Timer */
    private $timer;
    public function onEnable(){
        $this->saveDefaultConfig();
        $this->saveResource("values.txt");
        $this->getServer()->getCommandMap()->register("restartme", new RestartMeCommand($this));
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new AutoBroadcastTask($this), ($this->getConfig()->get("broadcastInterval") * 20));
        if($this->getConfig()->get("restartOnOverload")){
            $this->getServer()->getScheduler()->scheduleRepeatingTask(new CheckMemoryTask($this), 6000);
            $this->getServer()->getLogger()->notice("Memory overload restarts are enabled. If memory usage goes above ".$this->getMemoryLimit().", the server will restart.");
        }
        else{
            $this->getServer()->getLogger()->notice("Memory overload restarts are disabled.");
        }
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new RestartServerTask($this), 20);
        $this->timer = new Timer($this);
    }
    /**
     * @return Timer
     */
    public function getTimer(){
        return $this->timer;
    }
    /**
     * @return string
     */
    public function getMemoryLimit(){
        return strtoupper($this->getConfig()->get("memoryLimit"));
    }
}
