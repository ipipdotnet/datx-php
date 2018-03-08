# datx-php
IPIP.net官方支持的解析datx格式的php代码

## 安装说明
<pre><code>composer require ipip/datx</code></pre>

## 使用示例
<pre>
<code>
require_once __DIR__ . '/vendor/autoload.php';

$bs = new ipip\datx\City("/path/to/mydata4vipday4.datx");
var_export($bs->find("223.220.233.0"));

$bs = new ipip\datx\District("/path/to/quxian.datx");
var_export($bs->find("1.12.29.0"));

$bs = new ipip\datx\BaseStation("/path/to/station_ip.datx");
var_export($bs->find("223.220.233.0"));
</code>
</pre>