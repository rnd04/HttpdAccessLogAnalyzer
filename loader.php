<?php
spl_autoload_register('autoload_analyze_access');
function autoload_analyze_access($classname)
{
    if (!is_string($classname)) {
        throw new Exception('fail to autoload');
    }
    
    if ($classname == '') {
        throw new Exception('fail to autoload');
    }
    
    if (is_file("./Analyzer/{$classname}.php")) {
        include "./Analyzer/{$classname}.php";
        
    } elseif(is_file("./{$classname}.php")) {
        include "./{$classname}.php";
        
    } else {
        throw new Exception('fail to autoload');
    }
}
