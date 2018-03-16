<?php
class QueryUsageOfOneFieldValuePairAnalyzer extends Analyzer
{
    private $path = '';
    private $field = '';
    private $value = '';
    private $query_usage = array();

    public function __construct(array $analyzer_config=array())
    {
        if (isset($analyzer_config['path'])) {
            $this->path = $analyzer_config['path'];
        }
        if (isset($analyzer_config['field'])) {
            $this->field = $analyzer_config['field'];
        }
        if (isset($analyzer_config['value'])) {
            $this->value = $analyzer_config['value'];
        }
    }

    public function feed(HttpRequestFromAccessLogLine $request)
    {
        if ($this->path == '') {
            return;
        }
        if ($this->field == '') {
            return;
        }
        if ($this->value == '') {
            return;
        }
        if (
            $request->path == $this->path
            && isset($request->query[$this->field])
            && $request->query[$this->field] == $this->value
        ) {
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
