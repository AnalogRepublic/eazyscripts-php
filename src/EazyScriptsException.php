<?php 

namespace EazyScripts;

use Exception;

/**
 * Represents any errors thrown when we're interacting
 * with the EazyScripts API.
 */
class EazyScriptsException extends Exception
{
    public function __construct() 
    {
        parent::__construct();
    }
}