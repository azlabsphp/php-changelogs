<?php

namespace Drewlabs\Changelog;

interface Loggable
{
    /**
     * returns the name used for loging current table changes
     * 
     * @return string 
     */
    public function getLogTableName(): string;
}