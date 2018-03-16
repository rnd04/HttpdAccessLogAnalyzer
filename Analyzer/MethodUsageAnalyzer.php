<?php
class MethodUsageAnalyzer extends Analyzer
{
    public function __construct(array $analyzer_config=array())
    {
        
    }
    
    private $method_count = array();

    public function feed(HttpRequestFromAccessLogLine $request)
    {
        if (isset($this->method_count[$request->method])) {
            $this->method_count[$request->method]+= 1;
        } else {
            $this->method_count[$request->method] = 1;
        }
    }

    public function getResult()
    {
        return $this->method_count;
    }
}
