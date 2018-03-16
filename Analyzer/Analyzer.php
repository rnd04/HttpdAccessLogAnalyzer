<?php
abstract class Analyzer implements AnalyzerInterface
{
    public static function createAllAnalyzer(array $analyzer_class_array, array $analyzer_config)
    {
        $analyzer_array = array();
        
        foreach ($analyzer_class_array as $analyzer_class) {
            if (isset($analyzer_config[$analyzer_class])) {
                $analyzer_array[]= new $analyzer_class($analyzer_config[$analyzer_class]);
            } else {
                $analyzer_array[]= new $analyzer_class();
            }
        }
        
        return $analyzer_array;
    }
}
