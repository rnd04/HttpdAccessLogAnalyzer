<?php
class PathUsageAnalyzer extends Analyzer
{
    private $path_usage = array();

    public function __construct(array $analyzer_config=array())
    {
    
    }
    
    public function feed(HttpRequestFromAccessLogLine $request)
    {
        if (isset($this->path_usage[$request->path])) {
            $this->path_usage[$request->path]+= 1;
        } else {
            $this->path_usage[$request->path] = 1;
        }
    }
    
    public function getResult()
    {
        ksort($this->path_usage);
        return $this->path_usage;
    }
}
