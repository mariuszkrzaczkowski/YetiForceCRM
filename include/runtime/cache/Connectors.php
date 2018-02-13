<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Vtiger_Cache_Connector_Memory
{
    public function set($key, $value)
    {
        $this->$key = $value;
    }

    public function get($key)
    {
        return isset($this->$key) ? $this->$key : false;
    }

    public function flush()
    {
        return true;
    }
}
