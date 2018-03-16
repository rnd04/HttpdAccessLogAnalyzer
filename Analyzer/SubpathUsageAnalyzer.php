<?php
class SubpathUsageAnalyzer extends Analyzer
{
    private $path = '';
    private $path_usage = array();
    
    public function __construct(array $analyzer_config=array())
    {
        if (isset($analyzer_config['path'])) {
            $this->path = $analyzer_config['path'];
        }
    }
    
    public function feed(HttpRequestFromAccessLogLine $request)
    {
        if ($this->path == '') {
            return;
        }
        if (strpos($request->path, $this->path) === 0) {
            if (isset($this->path_usage[$request->path])) {
                $this->path_usage[$request->path]+= 1;
            } else {
                $this->path_usage[$request->path] = 1;
            }
        }
    }
    
    public function getResult()
    {
        ksort($this->path_usage);
        return $this->path_usage;
    }
}
