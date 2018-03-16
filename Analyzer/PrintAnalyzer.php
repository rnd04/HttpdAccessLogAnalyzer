<?php
class PrintAnalyzer extends Analyzer
{
    public function __construct(array $analyzer_config=array())
    {
        
    }
    
    public function feed(HttpRequestFromAccessLogLine $request)
    {
        print_r($request);
    }

    public function getResult()
    {
        return null;
    }
}
