<?php
interface AnalyzerInterface
{
    public function __construct(array $analyzer_config=array());
    public function feed(HttpRequestFromAccessLogLine $request);
    public function getResult();
}
