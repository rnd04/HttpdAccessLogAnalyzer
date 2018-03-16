<?php
class HttpRequestFromAccessLogLine
{
    public $parsed = false;
    public $raw;
    public $remote_host;
    public $remote_logid;
    public $remote_user;
    public $time;
    public $first_line_of_request;
    public $status;
    public $response_size;
    public $method;
    public $url;
    public $protocol;
    public $path;
    public $query_string;
    public $query;

    public static function createFromAccessLogLine($access_log_line)
    {
        $request = new HttpRequestFromAccessLogLine($access_log_line);
        $request->parse();
        return $request;
    }

    public function __construct($access_log_line)
    {
        $this->raw = $access_log_line;
    }

    public function parse()
    {
        /*
         * LogFormat "%h %l %u %t \"%r\" %>s %b" common
         */
        $ipv4_regex = '[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}';
        $ipv6_regex = '[a-zA-Z0-9:]{2,39}';
        $remote_host_regex = "{$ipv4_regex}|{$ipv6_regex}";
        $remote_logid_regex = '-';
        $remote_user_regex = '-';
        $time_regex = '\[[0-9]{2,2}\/[a-zA-Z]{3,3}\/[0-9]{4,4}:[0-9]{2,2}:[0-9]{2,2}:[0-9]{2,2} [+-][0-9]{4,4}\]';
        $request_regex = '.+';
        $status_regex = '[0-9]{3,3}';
        $response_size_regex = '[0-9]+|-';
        $access_log_regex = "/^({$remote_host_regex}) ({$remote_logid_regex}) ({$remote_user_regex}) ({$time_regex}) \"({$request_regex})\" ({$status_regex}) ({$response_size_regex})$/";

        $count = preg_match($access_log_regex, $this->raw, $matches);
        if ($count === 0) {
            throw new Exception('does NOT matches access_log regex');
        }
         
        list(
            ,
            $this->remote_host,
            $this->remote_logid,
            $this->remote_user,
            $this->time,
            $this->first_line_of_request,
            $this->status,
            $this->response_size
        ) = $matches;

        $this->parseFirstLineOfRequest();
        $this->parsed = true;
    }

    private function parseFirstLineOfRequest()
    {
        $http_method_regex = 'GET|HEAD|POST|PUT|DELETE|TRACE|OPTIONS|CONNECT|PATCH';
        $webdav_method_regex = 'PROPFIND|PROPPATCH|MKCOL|COPY|MOVE|LOCK|UNLOCK';
        $method_regex = "{$http_method_regex}|{$webdav_method_regex}";
        $general_url_regex = '\/[^ ]*';
        $options_method_url_regex = '\*';
        $url_regex = "{$general_url_regex}|{$options_method_url_regex}";
        $protocol_regex = 'HTTP\/1\.[01]';
        $request_regex = "/^({$method_regex})*[ ]*({$url_regex})[ ]*({$protocol_regex})*$/";

        $count = preg_match($request_regex, $this->first_line_of_request, $matches);
        if ($count === 0) {
            throw new Exception('does NOT matches request regex');
        }

        list(
            ,
            $this->method,
            $this->url,
            $this->protocol
        ) = $matches;

        $this->parseUrl();
    }

    private function parseUrl()
    {
        $query_starts = strpos($this->url, '?');

        if ($query_starts === false) {
            $this->path = $this->url;

        } elseif ($query_starts === 0) {
            $this->query_string = substr($this->url, 1);
            parse_str($this->query_string, $this->query);

        } else {
            $this->path = substr($this->url, 0, $query_starts);
            $this->query_string = substr($this->url, ($query_starts - strlen($this->url) + 1));
            parse_str($this->query_string, $this->query);
        }
    }
}
