<?php

namespace restartme\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use restartme\utils\Utils;
use restartme\RestartMe;

class RestartMeCommand extends Command{
    /** @var RestartMe */
    private $plugin;
    /**
     * @param RestartMe $plugin
     */
    public function __construct(RestartMe $plugin){
        parent::__construct("restartme", "Shows all RestartMe commands", null, ["rm"]);
        $this->setPermission("restartme.command.restartme");
        $this->plugin = $plugin;
    }
    /** 
     * @param CommandSender $sender 
     */
    private function sendCommandHelp(CommandSender $sender){
        $commands = [
            "add" => "Adds n seconds to the timer",
            "help" => "Shows all RestartMe commands",
            "memory" => "Shows memory usage information",
            "set" => "Sets the timer to n seconds",
            "start" => "Starts the timer",
            "stop" => "Stops the timer",
            "subtract" => "Subtracts n seconds from the timer",
            "time" => "Gets the remaining time until the server restarts"
        ];
        $sender->sendMessage("RestartMe commands:");
        foreach($commands as $name => $description){
            $sender->sendMessage("/restartme $name: $description");
        }
    }
    /**
     * @param CommandSender $sender
     * @param string $label
     * @param string[] $args
     * @return bool
     */
    public function execute(CommandSender $sender, $label, array $args){
        if(!$this->testPermission($sender)){
            return false;
        }
        if(isset($args[0])){
            $timer = $this->plugin->getTimer();
            switch(strtolower($args[0])){
                case "a":
                case "add":
                    if(isset($args[1])){
                        if(is_numeric($args[1])){
                            $time = (int) $args[1];
                            $timer->addTime($time);
                            $sender->sendMessage(TextFormat::GREEN."Added $time to restart timer.");
                        }
                        else{
                            $sender->sendMessage(TextFormat::RED."Time value must be numeric.");
                        } 
                    }
                    else{
                        $sender->sendMessage(TextFormat::RED."Please specify a time value.");
                    }
                    break;
                case "help":
                    $this->sendCommandHelp($sender);
                    break;
                case "m":
                case "memory":
                    $memLimit = $this->plugin->getMemoryLimit();
                    $sender->sendMessage("Bytes: ".memory_get_usage(true)."/".Utils::calculateBytes($memLimit));
                    $sender->sendMessage("Memory-limit: $memLimit");
                    $sender->sendMessage("Overloaded: ".(Utils::isOverloaded($memLimit) ? TextFormat::GREEN."yes" : TextFormat::RED."no"));
                    break;
                case "set":
                    if(isset($args[1])){
                        if(is_numeric($args[1])){
                            $time = (int) $args[1];
                            $timer->setTime($time);
                            $sender->sendMessage(TextFormat::GREEN."Set restart timer to $time.");
                        }
                        else{
                            $sender->sendMessage(TextFormat::RED."Time value must be numeric.");
                        } 
                    }
                    else{
                        $sender->sendMessage(TextFormat::RED."Please specify a time value.");
                    }
                    break;
                case "start":
                    if($timer->isPaused()){
                        $timer->setPaused(false);
                        $sender->sendMessage(TextFormat::YELLOW."Timer is no longer paused.");
                    }
                    else{
                        $sender->sendMessage(TextFormat::RED."Timer is not paused.");
                    }
                    break;
                case "stop":
                    if($timer->isPaused()){
                        $sender->sendMessage(TextFormat::RED."Timer is already paused.");
                    }
                    else{
                        $timer->setPaused(true);
                        $sender->sendMessage(TextFormat::YELLOW."Timer has been paused.");
                    }
                    break;
                case "s":
                case "subtract":
                    if(isset($args[1])){
                        if(is_numeric($args[1])){
                            $time = (int) $args[1];
                            $timer->subtractTime($time);
                            $sender->sendMessage(TextFormat::GREEN."Subtracted $time from restart timer.");
                        }
                        else{
                            $sender->sendMessage(TextFormat::RED."Time value must be numeric.");
                        } 
                    }
                    else{
                        $sender->sendMessage(TextFormat::RED."Please specify a time value.");
                    }
                    break;
                case "t":
                case "time":
                    $sender->sendMessage(TextFormat::YELLOW."Time remaining: ".$timer->getFormattedTime());
                    break;
                default:
                    $sender->sendMessage("Usage: /restartme <sub-command> [parameters]");
                    break;
            }
        }
        else{
            $this->sendCommandHelp($sender);
            return false;
        }
        return true;
    }
}
