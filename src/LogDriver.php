<?php

namespace Drewlabs\Changelog;

interface LogDriver
{
    /**
     * Logs table attribute changes 
     * 
     * @param string $table 
     * @param string $instance 
     * @param string $property 
     * @param mixed $previous 
     * @param mixed $actual 
     * @param null|string $logBy Unit that logs the table attribute change
     * @return mixed 
     */
    public function logChange(string $table, string $instance, string $property, $previous, $actual, ?string $logBy = null);
}
