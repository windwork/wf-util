<?php
/**
 * Windwork
 * 
 * 一个用于快速开发高并发Web应用的轻量级PHP框架
 * 
 * @copyright Copyright (c) 2008-2017 Windwork Team. (http://www.windwork.org)
 * @license   http://opensource.org/licenses/MIT
 */
namespace wf\util;


/**
 * the PHP http client, implement by socket.
 * 
 * base on Snoopy
 * 
 * @package     wf.http
 * @copyright   Snoopy (http://snoopy.sourceforge.net/)
 * @author      Monte Ohrt <monte@ohrt.com>
 * @since       1.0.0
 */
class Client 
{
    //set these if you like
    public $agent        = 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.87 Safari/537.36';   // User agent
    public $http         = 1.1;  // HTTP version defaults to 1.1
    public $timeout      = 15;   // request timeout (seconds)
    public $cookies      = array();
    public $referer      = '';
    public $maxRedirect  = 3;
    public $maxBodySize  = 0;    // abort if the response body is bigger than this
    public $headerRegexp = '';   // if set this RE must match against the headers, else abort
    public $headers      = array();
    public $debug        = false;
    public $start        = 0;    // (s) for timings

    // don't set these, read on error
    protected $error         = '';
    protected $redirectCount = 0;

    // read these after a successful request
    protected $rspStatus  = 0;
    protected $rspBody    = '';
    protected $rspHeaders = array();

    // set these to do basic authentication
    public $user = '';
    public $pass = '';

    // set these if you need to use a proxy
    public $proxyHost = '';
    public $proxyPort = '';
    public $proxyUser = '';
    public $proxyPass = '';
    public $proxySsl  = false; //boolean set to true if your proxy needs SSL

    public function __construct() {
        $this->headers['Accept-Language'] = 'zh-cn';
        $this->headers['Accept'] = 'text/xml,application/xml,application/xhtml+xml,text/html,text/plain,image/png,image/jpeg,image/gif,*/*';
        
        if (extension_loaded('zlib')) {
            $this->headers['Accept-encoding'] = 'gzip';
        }
    }

    /**
     * fetch the contents of a web page
     *
     * return the get request response text or false;
     *
     * @param  string $url        The URL to fetch
     * @param  bool   $sloppy304  Return body on 304 not modified
     * @return false|string
     */
    public function get($url, $sloppy304=false) 
    {
        if(!$this->sendRequest($url)) {
            return false;
        }
        
        if($this->rspStatus == 304 && $sloppy304) {
            return $this->rspBody;
        }
        
        if($this->rspStatus != 200) {
            return false;
        }
        
        return $this->rspBody;
    }

    /**
     * post request data
     *
     * return the post request response text or false;
     *
     * @param string $url
     * @param array  $data = array()
     * @return false|string
     */
    public function post($url, $data = array()) 
    {
        if(!$this->sendRequest($url, $data, 'POST')) {
            return false;
        }
        
        if($this->rspStatus != 200) {
            return false;
        }
        
        return $this->rspBody;
    }

    /**
     * get error message
     * when $this->sendRequest() return false, the error is not empty
     */
    public function getError() 
    {
        return $this->error;
    }

    /**
     * send the HTTP request
     *
     * This method handles the whole HTTP communication. It respects set proxy settings,
     * builds the request headers, follows redirects and parses the response.
     *
     * Post data should be passed as associative array. When passed as string it will be
     * sent as is. You will need to setup your own Content-Type header then.
     *
     * @param  string $url    - the complete URL
     * @param  mixed  $data   - the post data either as array or raw data
     * @param  string $method - HTTP Method usually GET or POST.
     * @return bool           - true on success
     */
    public function sendRequest($url, $data = '', $method='GET') 
    {
        $this->start   = microtime(1);
        $this->error  = '';
        $this->rspStatus = 0;

        // parse URL into bits
        $uri    = parse_url($url);
        $server = $uri['host'];
        $path   = empty($uri['path']) ? '/' : $uri['path'];

        isset($uri['query']) && $path      .= '?'.$uri['query'];
        isset($uri['user'])  && $this->user = $uri['user'];
        isset($uri['pass'])  && $this->pass = $uri['pass'];
        isset($uri['port'])  && $port       = $uri['port'];

        // proxy setup
        if($this->proxyHost) {
            $reqUrl  = $url;
            $server  = $this->proxyHost;
            $port    = $this->proxyPort;
            empty($port) &&$port = 8080;
        } else {
            $reqUrl  = $path;
            if (empty($port)) {
                $port = ($uri['scheme'] == 'https') ? 443 : 80;
            }
        }

        // add SSL stream prefix if needed - needs SSL support in PHP
        $scheme = ($uri['scheme'] == 'https' || $port == 443 || $this->proxySsl) ? 'ssl' : 'tcp';
        
        // prepare headers
        $headers = array();
        $headers['Host']       = $uri['host'];
        $headers['User-Agent'] = $this->agent;
        $headers['Referer']    = $this->referer;
        $headers['Connection'] = 'Close';
        foreach ($this->headers as $hk => $hv) {
            $headers[$hk] = $hv;
        }

        if($method == 'POST') {
            if(is_array($data)) {
                $headers['Content-Type']   = 'application/x-www-form-urlencoded';
                $data = http_build_query($data);
            }
            $headers['Content-Length'] = strlen($data);
            $rmethod = 'POST';
        } elseif ($method == 'GET') {
            $data = ''; //no data allowed on GET requests
        }
        
        if($this->user) {
            $headers['Authorization'] = 'Basic '.base64_encode($this->user.':'.$this->pass);
        }
        if($this->proxyUser) {
            $headers['Proxy-Authorization'] = 'Basic '.base64_encode($this->proxyUser.':'.$this->proxyPass);
        }

        // start time
        $start = time();

        // open socket
        $socket = @stream_socket_client("{$scheme}://{$server}:{$port}", $errno, $errstr, $this->timeout);
        if (!$socket) {
            $this->rspStatus = -100;
            $this->error = "Could not connect to $server:$port\n$errstr ($errno)";
            return false;
        }
        
        //set non blocking
        stream_set_blocking($socket, 0);

        // build request
        $req  = "$method $reqUrl HTTP/". $this->http. "\r\n";
        $req .= $this->buildHeaders($headers);
        $req .= $this->getCookies();
        $req .= "\r\n";
        $req .= $data;

        if($this->debug) {
            $this->debug('request', $req);
        }

        // send request
        fputs($socket, $req);

        // read headers from socket
        $rHeaders = '';
        do{
            if(feof($socket)) {
                $this->error = 'Premature End of File (socket)';
                return false;
            }
            $rHeaders .= fgets($socket, 1024);
        } while (!preg_match('/\r?\n\r?\n$/', $rHeaders));

        if($this->debug) {
            $this->debug('response headers', $rHeaders);
        }

        // check if expected body size exceeds allowance
        if($this->maxBodySize && preg_match('/\r?\nContent-Length:\s*(\d+)\r?\n/i', $rHeaders, $match)) {
            if($match[1] > $this->maxBodySize) {
                $this->error = 'Reported content length exceeds allowed response size';
                return false;
            }
        }

        // get Status
        if (!preg_match('/^HTTP\/(\d\.\d)\s*(\d+).*?\n/', $rHeaders, $m)) {
            $this->error = 'Server returned bad answer';
            return false;
        }
        $this->rspStatus = $m[2];

        // handle headers and cookies
        $this->rspHeaders = $this->parseHeaders($rHeaders);
        if(isset($this->rspHeaders['set-cookie'])) {
            foreach ((array) $this->rspHeaders['set-cookie'] as $cookie) {
                list($key, $value, $foo) = explode('=', $cookie);
                $this->cookies[$key] = $value;
            }
        }

        if($this->debug) {
            $this->debug('Object headers', $this->rspHeaders);
        }

        // check server status code to follow redirect
        if($this->rspStatus == 301 || $this->rspStatus == 302 ) {
            if (empty($this->rspHeaders['location'])) {
                $this->error = 'Redirect but no Location Header found';
                return false;
            } elseif ($this->redirectCount == $this->maxRedirect) {
                $this->error = 'Maximum number of redirects exceeded';
                return false;
            } else {
                $this->redirectCount++;
                $this->referer = $url;
                if (!preg_match('/^http/i', $this->rspHeaders['location'])) {
                    $this->rspHeaders['location'] = $uri['scheme']. '://'. $uri['host'] . $this->rspHeaders['location'];
                }
                // perform redirected request, always via GET (required by RFC)
                return $this->sendRequest($this->rspHeaders['location'], array(), 'GET');
            }
        }

        // check if headers are as expected
        if($this->headerRegexp && !preg_match($this->headerRegexp, $rHeaders)) {
            $this->error = 'The received headers did not match the given regexp';
            return false;
        }

        //read body (with chunked encoding if needed)
        $rBody = '';
        if(preg_match('/transfer\-(en)?coding:\s*chunked\r\n/i', $rHeaders)) {
            do {
                $chunkSize = hexdec(fgets($socket, 16)); // read chunk-size line
                
                if ($chunkSize) {
                    $thisChunk = fread($socket, $chunkSize);
                    $rBody    .= $thisChunk;

                    $tmpSize = strlen($thisChunk);
                    while($tmpSize < $chunkSize) {
                        $thisChunk = fread($socket, $chunkSize - $tmpSize);
                        $rBody    .= $thisChunk;
                        $tmpSize  += strlen($thisChunk);
                    }
                    
                    fgets($socket, 16); // skip \r\n                    
                }

                if($this->maxBodySize && strlen($rBody) > $this->maxBodySize) {
                    $this->error = 'Allowed response size exceeded';
                    return false;
                }
            } while (!feof($socket));
        } else {
            // read entire socket
            while (!feof($socket)) {                
                $rBody .= fread($socket,4096);
                $rSize  = strlen($rBody);
                if($this->maxBodySize && $rSize > $this->maxBodySize) {
                    $this->error = 'Allowed response size exceeded';
                    return false;
                }
                
                if($this->rspHeaders['content-length'] && empty($this->rspHeaders['transfer-encoding'])     && $this->rspHeaders['content-length'] == $rSize) {
                    // we read the content-length, finish here
                    break;
                }
            }
        }

        // close socket
        $status = socket_get_status($socket);
        fclose($socket);

        // decode gzip if needed
        if(@$this->rspHeaders['content-encoding'] == 'gzip') {
            $this->rspBody = gzinflate(substr($rBody, 10));
        } else {
            $this->rspBody = $rBody;
        }

        if($this->debug) {
            $this->debug('response body', $this->rspBody);
        }
        
        return true;
    }

    /**
     * print debug info
     *
     * @param string $info
     * @param mixed $var
     */
    protected function debug($info, $var = null) 
    {
        if(!$this->debug) {
            return;
        }
        
        print '<b>'.$info.'</b> '.(microtime(1) - $this->start).'s<br />' . "\r\n";
        
        if(!is_null($var)) {
            $content = var_export($var, 1);
            print "<pre>\r\n$content\r\n</pre>\r\n";
        }
    }

    /**
     * convert given header string to Header array
     *
     * All Keys are lowercased.
     *
     * @param string $string
     * @return array
     */
    protected function parseHeaders($string) 
    {
        $headers = array();
        $lines = explode("\r\n", trim($string));
        foreach($lines as $line) {
            @list($key, $val) = explode(':', $line, 2);
            $key = strtolower(trim($key));
            $val = trim($val);
            if(empty($val)) continue;
            if(isset($headers[$key])) {
                if(is_array($headers[$key])) {
                    $headers[$key][] = $val;
                } else {
                    $headers[$key] = array($headers[$key], $val);
                }
            } else {
                $headers[$key] = $val;
            }
        }
        return $headers;
    }

    /**
     * convert given header array to header string
     *
     * @param array $headers
     * @return string
     */
    protected function buildHeaders($headers) 
    {
        $string = '';
        foreach($headers as $key => $value) {
            if(empty($value)) continue;
            $string .= $key. ': '. $value. "\r\n";
        }
        return $string;
    }

    /**
     * get cookies as http header string
     *
     * @return string
     */
    protected function getCookies() 
    {
        $headers = '';
        foreach ($this->cookies as $key => $val) {
            if ($headers) $headers .= '; ';
            $headers .= $key. '='. $val;
        }

        if ($headers) {
            $headers = "Cookie: $headers"."\r\n";
        }

        return $headers;
    }
}
