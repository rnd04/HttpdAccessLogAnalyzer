#!/usr/bin/php
<?php
ini_set('display_errors', true);

require_once './loader.php';

$config = require_once './config.php';

$analyzer_instance_array = Analyzer::createAllAnalyzer($config['analyzer_class_array'], $config['analyzer_config']);

foreach ($config['access_log_file_fullpath_array'] as $access_log_file_fullpath) {
	$h = fopen($access_log_file_fullpath, 'r');
	
	for ($i = 1; false !== ($line = fgets($h)); $i++) {
	    try {
	        $request = HttpRequestFromAccessLogLine::createFromAccessLogLine(trim($line));
    	    foreach ($analyzer_instance_array as $analyzer_instance) {
    	        $analyzer_instance->feed($request);
    	    }
	    } catch (Exception $e) {
	        printf("%s:%d - %s\n", $access_log_file_fullpath, $i, $e->getMessage());
	        printf("%s\n", $line);
	        if ($config['halt_on_exception']) {
    	        break 2;
	        }
	    }
	}
}

foreach ($analyzer_instance_array as $analyzer_instance) {
    print_r($analyzer_instance->getResult());
}

exit;
