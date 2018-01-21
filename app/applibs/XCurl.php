<?php
namespace app\applibs;

class XCurl
{
    private $_ch;
    private $inited = false;
    private $response;
    protected static $instance = null;

    // config from config.php
    public $options;

    // default config
    private $_config = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HEADER         => false,
//        CURLOPT_VERBOSE        => true,
        CURLOPT_AUTOREFERER    => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT        => 5,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Maxthon/4.4.1.2000 Chrome/30.0.1599.101 Safari/537.36'
    );

    private function exec($url)
    {
        $this->setOption(CURLOPT_URL, $url);
        //Yii::log($url, "warning", "curl log");
        $this->response = curl_exec($this->_ch);
        if (!curl_errno($this->_ch)) {
            if ($this->options[CURLOPT_HEADER]) {
                $header_size = curl_getinfo($this->_ch, CURLINFO_HEADER_SIZE);
                return substr($this->response, $header_size);
            }
            return $this->response;
        } else {
            return false;
        }
    }
    
    public static function getInstance($new=false)
    {
        if ($new) {
            $o = new self();
            $o->init();
            return $o;
        } elseif (self::$instance === null) {
            self::$instance = new self();
            self::$instance->init();
        }
        return self::$instance;
    }

    /**
     * 获取url页面内容
     * 
     * @param string $url 网页url
     * @param array $params URL参数
     * @param bool $parseUrl 是否重新拼接url
     */
    public function get($url, $params = array(), $parseUrl=true)
    {
        $this->setOption(CURLOPT_HTTPGET, true);
        if ($parseUrl) {
            $url = $this->buildUrl($url, $params);
        }
        return $this->exec($url);
    }

    public function post($url, $data = array())
    {
        $this->setOption(CURLOPT_POST, true);
        $this->setOption(CURLOPT_POSTFIELDS, $data);

        return $this->exec($url);
    }

    public function put($url, $data, $params = array())
    {
        // write to memory/temp
        $f = fopen('php://temp', 'rw+');
        fwrite($f, $data);
        rewind($f);

        $this->setOption(CURLOPT_PUT, true);
        $this->setOption(CURLOPT_INFILE, $f);
        $this->setOption(CURLOPT_INFILESIZE, strlen($data));

        return $this->exec($this->buildUrl($url, $params));
    }

    public function delete($url, $params = array())
    {
        $this->setOption(CURLOPT_RETURNTRANSFER, true);
        $this->setOption(CURLOPT_CUSTOMREQUEST, 'DELETE');

        return $this->exec($this->buildUrl($url, $params));
    }

    public function buildUrl($url, $data = array())
    {
        $parsed = parse_url($url);
        isset($parsed['query']) ? parse_str($parsed['query'], $parsed['query']) : $parsed['query'] = array();
        $params = isset($parsed['query']) ? array_merge($parsed['query'], $data) : $data;
        $parsed['query'] = ($params) ? '?' . http_build_query($params) : '';
        if (!isset($parsed['path'])) {
            $parsed['path']='/';
        }

        $parsed['port'] = isset($parsed['port'])?':'.$parsed['port']:'';

        return $parsed['scheme'].'://'.$parsed['host'].$parsed['port'].$parsed['path'].$parsed['query'];
    }

    public function setOptions($options = array())
    {
        if (is_array($this->options)) {
            $this->options = array_merge($this->options, $options);
        } else {
            $this->options = $options;
        }
        curl_setopt_array($this->_ch, $options);

        return $this;
    }

    public function setOption($option, $value)
    {
        $this->options[$option] = $value;
        curl_setopt($this->_ch, $option, $value);

        return $this;
    }

    public function setHeaders($header = array(), $assoc = true)
    {
        if ($assoc) {
            $out = array();
            foreach ($header as $k => $v) {
                $out[] = $k .': '.$v;
            }
            $header = $out;
        }

        $this->setOption(CURLOPT_HTTPHEADER, $header);

        return $this;
    }

    public function getError()
    {
        return curl_error($this->_ch);
    }

    public function getInfo()
    {
        return curl_getinfo($this->_ch);
    }

    public function getStatus()
    {
        return curl_getinfo($this->_ch, CURLINFO_HTTP_CODE);
    }

    // initialize curl
    public function init()
    {
        if ($this->inited) return;

        $this->_ch = curl_init();
        $options = is_array($this->options) ? ($this->options + $this->_config) : $this->_config;
        $this->setOptions($options);
        
        $this->inited = true;
    }

    public function getHeaders()
    {
        $headers = array();

        $header_text = substr($this->response, 0, strpos($this->response, "\r\n\r\n"));

        foreach (explode("\r\n", $header_text) as $i => $line) {
            if ($i === 0) {
                $headers['http_code'] = $line;
            } else {
                list ($key, $value) = explode(': ', $line);

                $headers[$key] = $value;
            }
        }

        return $headers;
    }
    
    public function getCookie($name)
    {
        $ret = false;
        $matches = null;
        $res = preg_match("#Set-Cookie: {$name}=([^;]+)#", $this->response, $matches);
        if ($res && ! empty($matches[1])) {
            $ret = trim($matches[1]);
        }
        return $ret;
    }

    public function __destruct()
    {
        if ($this->_ch) {
            curl_close($this->_ch);
        }
    }
    
    public static function getRandUserAgent()
    {
        $tmp = array(
            'Mozilla/5.0 (Linux; Android 4.4.4; HM NOTE 1LTE Build/KTU84P) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/33.0.0.0 Mobile Safari/537.36',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 8_1 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) Version/8.0 Mobile/12B410 Safari/600.1.4',
            'Mozilla/5.0 (Linux; U; Android 4.4.2; zh-cn; GT-I9500 Build/KOT49H) AppleWebKit/537.36 (KHTML, like Gecko)Version/4.0 MQQBrowser/5.0 QQ-Manager Mobile Safari/537.36',
            'Mozilla/5.0 (Linux; U; Android 4.4.2; zh-cn; H30-T10 Build/HuaweiH30-T10) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1',
        );
        $i = array_rand($tmp);
        return $tmp[$i];
    }
}