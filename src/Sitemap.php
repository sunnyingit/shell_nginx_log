<?php

// 生成sitemap文件
class Sitemap
{
    protected static $instance;

    // 日志文件
    protected $config = array(
        'r' => 'shell/r.access.log',
        'm' => 'shell/m.access.log'
    );

    // xml配置
    protected $xmlConfig = array(
        'lastmod'    => '',
        'changefreq' => 'daily',
        'priority'   => '0.8'
    );

    protected $hostConfig = array(
        'r' => "http://m.ele.me/",
        'm' => "http://m.ele.me/place/"
    );

    protected $method = '';

    protected function __construct()
    {
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

        $handle = fopen($filePath, 'r');  
        if ($handle) {  
            while(! feof($handle)) {  
                $buffer = trim(fgets($handle));
                // 去重处理
                $data[md5($buffer)] = $buffer;  
            }  
        }

        $items = array_chunk($data, 50000); 

        $sitemapIndex = count($items);

        $string = '';
        for ($i = 0; $i < $sitemapIndex; $i++) {
            $string .= "Sitemap: http://m.ele.me/sitemap" . $i . ".xml" . "\r\n";
        }

        file_put_contents('data/' . $this->method . '/roots.txt', $string);

        foreach ($items as $key => $item) {
            $xml = $this->buildXml($item);

            file_put_contents('data/'. $this->method . '/sitemap'. $key .'.xml', $xml);
        }

        echo "DONE";

        fclose($handle); 
    }


    private function buildXml($data)
    {
        $string = <<<XML
<?xml version='1.0' encoding='utf-8'?> 
<urlset>
</urlset>
XML;
        $xml = simplexml_load_string($string);

        $host = $this->hostConfig[$this->method];
         
        foreach ($data as $val) {
            $item = $xml->addChild('url');

            $item->addChild('loc',  $host . $val);

            foreach ($this->xmlConfig as $field => $value) {

                $value = $value ?: date('Y-m-d');
                $node = $item->addChild($field, $value);
            }
        }

        return $xml->asXML();
    }
}
