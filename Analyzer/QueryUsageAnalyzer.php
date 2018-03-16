<?php
class QueryUsageAnalyzer extends Analyzer
{
    private $path = '';
    private $query_usage = array();

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
        if ($request->path == $this->path) {
            if (is_array($request->query)) {
                foreach ($request->query as $field => $value) {
                    if (isset($this->query_usage[$field])) {
                        if (isset($this->query_usage[$field][$value])) {
                            $this->query_usage[$field][$value]+= 1;
                        } else {
                            $this->query_usage[$field][$value] = 1;
                        }
                    } else {
                        $this->query_usage[$field] = array();
                    }
                }
            }
        }
    }

    public function getResult()
    {
        ksort($this->query_usage);
        return $this->query_usage;
    }
}
