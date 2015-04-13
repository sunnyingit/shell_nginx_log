<?php

// 生成sitemap文件
class Sitemap
{
    protected static $instance;

    // 日志文件
    protected $config = array(
        'r' => 'seed/r.txt',
        'm' => 'seed/m.txt'
    );

    // xml配置
    protected $xmlConfig = array(
        'lastmod' => '',
        'changefreq' => 'weekly',
        'priority' => '0.8'
    );

    const HOST = "http://m.ele.me/place/";

    // 请求的方法
    protected $method = '';

    protected function __construct()
    {
        if (! defined('DS')) {
            define('DS', DIRECTORY_SEPARATOR);
        }

        $this->method = trim($_SERVER['REQUEST_URI'], '/');

        if (! in_array($this->method, array('r', 'm'))) {
            throw new Exception("请求方法只能是r,m", 1);
        }
     }

    public static function getInstance()
    {
        if (! self::$instance && ! is_object(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function run()
    {
        $this->getSeed();
    }

    private function getSeed() 
    {
        $filePath = APP_PATH . $this->config[$this->method];

        $tplPath = APP_PATH . 'view/' .  $this->method . '.xml';
        $handle = fopen($filePath, 'r');  
        if ($handle) {  
            while(! feof($handle)) {  
                $buffer = trim(fgets($handle));
                $data[] = $buffer;  
            }  
        }

        $items = array_chunk($data, 50000); 

        foreach ($items as $key => $item) {
            $xml = $this->buildXml($item);

            file_put_contents('data/sitemap'. $key .'xml', $xml);
        }

        fclose($handle); 
    }


    private function buildXml($data)
    {
        $string = <<<XML
<?xml version='1.0'?> 
<urlset>
</urlset>
XML;
        $xml = simplexml_load_string($string);
         
        foreach ($data as $val) {
            $item = $xml->addChild('url');

            $item->addChild('loc', self::HOST . $val);

            foreach ($this->xmlConfig as $field => $value) {

                $value = $value ?: date('Y-m-d');
                $node = $item->addChild($field, $value);
            }
        }

        return $xml->asXML();
    }

    public function genaratexml()
    {

    }

    public function genamrateSitemapIndex()
    {

    }
}
