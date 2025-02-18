<?php

namespace Drewlabs\Changelog;

use Exception;

class Logger implements LogDriver
{
    /**  @var static */
    private static $instance;

    /**  @var array<string,\Closure():LogDriver> */
    private $drivers = [];

    /**
     * Private class constructor
     * 
     * @return void 
     */
    private function __construct() {}

    public function logChange(string $table, string $instance, string $property, $previous, $actual, ?string $logBy = null)
    {
        foreach ($this->drivers as $factory) {
            $this->tryCatch(function () use ($factory, $table, $instance, $property, $previous, $actual, $logBy) {
                return call_user_func($factory, $this)->logChange($table, $instance, $property, $previous, $actual, $logBy);
            }, [Exception::class]);
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
    public function registerDriver($driver, ?string $name = null)
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

    private function tryCatch(\Closure $callback, array $exceptions = [], $returns = null)
    {
        try {
            return call_user_func($callback);
        } catch (\Throwable $e) {
            if (!empty($exceptions) && (is_subclass_of($e, Exception::class) || in_array(get_class($e), $exceptions))) {
                return is_callable($returns) ? call_user_func($returns, $e) : $returns;
            }

            throw $e;
        }
    }
}
