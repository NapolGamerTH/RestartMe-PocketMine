<?php

namespace restartme\utils;

class Utils{
    /**
     * Calculates the memory usage threshold from a string
     * @param string $toCheck
     * @return int
     */
    public static function calculateBytes($toCheck){
        $byteLimit = (int) substr(trim($toCheck), 0, 1);
        //Should I add support for both types of suffixes (G and GB) in the future?
        switch(strtoupper(substr($toCheck, -1))){
            case "P": //petabyte
                return $byteLimit * pow(1024, 5);
            case "T": //terabyte
                return $byteLimit * pow(1024, 4);
            case "G": //gigabyte
                return $byteLimit * pow(1024, 3);
            case "M": //megabyte
                return $byteLimit * pow(1024, 2);
            case "K": //kilobyte
                return $byteLimit * 1024;
            case "B": //byte
                return $byteLimit;
            default:
                return $byteLimit;
        }
    }
    /**
     * Returns true if $toCheck is greater than the current memory usage
     * @param string $toCheck
     * @return bool
     */
    public static function isOverloaded($toCheck){
        return memory_get_usage(true) > self::calculateBytes($toCheck);
    }
    /**
     * Returns 0 => hours, 1 => minutes, 2 => seconds, calculated from $time
     * @param int $time
     * @return array
     * @throws \InvalidArgumentException
     */
    public static function toArray($time){
        if(is_int($time)){
            return [
                floor($time / 3600), //hour
                floor(($time / 60) - (floor($time / 3600) * 60)), //minute
                floor($time % 60) //second
            ];
        }
        else{
            throw new \InvalidArgumentException("Expected integer, ".gettype($time)." given.");
        }
    }
}
