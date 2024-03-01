<?php

namespace Drewlabs\Changelog;

class Logger implements LogDriver
{

    /**
     * @var static
     */
    private static $instance;

    /**
     * @var array<string,\Closure():LogDriver>
     */
    private $drivers = [];

    /**
     * Private class constructor
     * 
     * @return void 
     */
    private function __construct()
    {
    }

    public function logChange(string $table, string $instance, string $property, $previous, $actual, string $logBy = null)
    {
        foreach ($this->drivers as $factory) {
            call_user_func($factory, $this)->logChange($table, $instance, $property, $previous, $actual, $logBy);
        }
    }

    /**
     * Get singleton instance
     * 
     * @return Logger 
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static;
        }
        return self::$instance;
    }

    /**
     * Get a driver from the drivers registry
     * 
     * @param string $name 
     * @return null|LogDriver 
     */
    public function driver(string $name): ?LogDriver
    {
        return isset($this->drivers[$name]) ? call_user_func($this->drivers[$name], $this) : null;
    }

    /**
     * Register a driver in the pool of log drivers
     * 
     * @param LogDriver|\Closure():LogDriver $driver
     * 
     * @return void 
     */
    public function registerDriver($driver, string $name = null)
    {
        $driver = is_callable($driver) ? $driver : function () use ($driver) {
            return $driver;
        };
        $name = $name ?? md5(uniqid());
        // We do not register a driver twice if it exists
        if (in_array($name, array_keys($this->drivers)) && isset($this->drivers[$name])) {
            return;
        }

        $this->drivers[$name] = $driver;
    }
}
