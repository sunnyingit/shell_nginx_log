<?php
// 生成sitemap
define('APP_PATH', dirname(__FILE__) . '/');

require APP_PATH . 'src/Sitemap.php';

// 执行生成sitemap程序
Sitemap::getInstance()->run();
