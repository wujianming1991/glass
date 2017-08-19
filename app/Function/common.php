<?php

use Illuminate\Support\Debug\Dumper;

/**
 * 匹配网址
 * Markdown 作者多年改进的正则匹配
 * 基本能匹配绝大部分网站
 */
if (!function_exists('pregUrl')) {
    function pregUrl($url) {
        $regex = '@(?i)\b((?:[a-z][\w-]+:(?:/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))@';

        return preg_match($regex, $url);
    }
}
/**
 * 匹配手机号
 */
if (!function_exists('pregPhone')) {
    function pregPhone($phone) {
        $regex = '/^13\d{9}$|^14\d{9}$|^15\d{9}$|^17\d{9}$|^18\d{9}$/';
        return preg_match($regex,$phone);
    }
}
/**
 * 匹配邮箱
 */
if (!function_exists('pregEmail')) {
    function pregEmail($email) {
        $regex = "/([a-z0-9]*[-_\.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?/i"; //i 忽略大小写
        return preg_match($regex,$email);
    }
}

/**
 * [laravelCurl 封装curl请求]
 * @author xiaoyin
 * @datetime
 * @param $url 请求地址
 * @param null $data post请求参数
 * @param int $connectTimeout 连接超时时间
 * @param int $readTimeout 保持连接时间
 * @param array $headers 头部参数
 * @param bool $isJson 数据格式化为数组输出
 * @return mixed
 */

if (!function_exists('laravelCurl')) {
    function laravelCurl($url, $data = null, $connectTimeout = 60, $readTimeout = 300,  $headers = [], $isJson = true)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        // https请求,会要求证书,避免报错,设置下面两个参数,规避ssl证书检查
        if(strlen($url) > 5 && strtolower(substr($url,0,5)) == "https" ) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        // 显示HTTP状态码，默认行为是忽略编号小于等于400的HTTP信息。这里需要设置为false
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        // 在发起连接前等待的时间，如果设置为0，则无限等待,连接服务器规定时间内无响应,脚本断开连接
        if ($connectTimeout) {
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $connectTimeout);
        }
        // 设置cURL允许执行的最长秒数。超过连接时间自动断开,例如:下载一个文件规定时间内未下载完,则断开连接
        if ($readTimeout) {
            curl_setopt($curl, CURLOPT_TIMEOUT, $readTimeout);
        }
        // 设置header请求参数
        if (is_array($headers) && 0 < count($headers)) {
            $httpHeader = [];
            foreach ($headers as $k => $v) {
                array_push($httpHeader, $k.":".$v);
            }
            curl_setopt($curl, CURLOPT_HTTPHEADER, $httpHeader);
        }
        // 是否POST传输请求
        if (!empty($data) && is_array($data)) {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        // 执行一个curl请求
        $output = curl_exec($curl);
        // 释放句柄
        curl_close($curl);
        // 转换为数组返回,json_decode第二个参数,影响返回结果是对象还是数组
        if ($isJson) {
            $temp = json_decode($output, true);
            if (null != $temp) {
                $output = $temp;
            }
        }

        return $output;
    }
}

/**
 * 生成ajax返回数据
 */
if (!function_exists('jsonMsg')) {
    function jsonMsg($info = 'ok', $data = []) {
        $json = [
            'info' => $info,
            'data' => $data
        ];

        return response()->json($json);
    }
}


/**
 * 将数组转换为url参数
 * @author zhangyuanhao
 */
if(!function_exists('arrayBuildUrl')) {
    function arrayBuildUrl($array, $url = '')
    {
        if(!$array) {
            $urlString = '';
        } else {
            $urlString = http_build_query($array);
        }

        if($url) {
            $check = strpos($url, '?');
            //如果存在 ?
            if($check !== false) {
                //如果 ? 后面没有参数，如 http://www.baidu.com/index.php?
                if(substr($url, $check+1) == '') {
                    //可以直接加上附加参数
                    $new_url = $url;
                } else {//如果有参数，如：http://www.baidu.com/index.php?ID=12
                    $new_url = $url.'&';
                }
            } else { //如果不存在 ?
                $new_url = $url.'?';
            }
            $urlString = $new_url.$urlString;
        }
        $urlString = trim($urlString, '?');
        $urlString = trim($urlString, '&');
        return $urlString;
    }
}

/**
 * 获取SESSION里面的URL参数
 */
if( !function_exists('getReturnUrlParams')) {
    function getReturnUrlParams(){
        return Session::get('returnUrlParams');
    }
}

/**
 * [trimArray 处理数组，将所有的值trim去除空格]
 * @author xiaoyin
 * @datetime 2016-01-14
 * @param    [type]     $array [待处理的数组]
 * @return   [type]            [description]
 */
if (!function_exists('trimArray')) {
    function trimArray($array)
    {
        foreach ($array as &$v) {
            if (is_array($v)) {
                $v = trimArray($v);
            }else{
                $v = trim($v);
            }
        }
        return $array;
    }
}

/**
 * 生成api返回数据
 */
if (!function_exists('apiResult')) {
    function apiResult($info = 'ok', $data = [], $code = '200') {
        $result = [
            'status' => 'ok',
            'code'   => '200',
            'data'   => $data
        ];

        if ($info != 'ok') {
            $result = [
                'status' => $info,
                'code'   => $code != '200' ? $code : '-1',
                'data'   => $data
            ];
        }

        return $result;
    }
}

/**
 * 通过信息提示也再跳转
 */
if (!function_exists('showMsg')) {
    function showMsg($url, $msg = '操作成功') {
        return redirect()->route('showMsg',['url'=>$url, 'msg'=>$msg]);
    }
}

/**
 *
 */
if (!function_exists('viewMsg')) {
    function viewMsg($url, $msg = '操作成功', $type = 'success', $btnTitle = '', $time = 3) {
        return view('home.v1.msg',['pageLink'=>$url, 'message'=>$msg, 'type'=>$type, 'btnTitle'=>$btnTitle, 'time'=>$time]);
    }
}

/**
 * 格式化时间 转为 多少前
 */
if (!function_exists('timeFormat')) {
    function timeFormat($time){
        $t = time()-$time;
        $f = [
            '31536000'=>'年',
            '2592000'=>'个月',
            '604800'=>'星期',
            '86400'=>'天',
            '3600'=>'小时',
            '60'=>'分钟',
            '1'=>'秒'
        ];

        foreach ($f as $k=>$v) {
            $c = floor($t/(int)$k);
            if (0 != $c) {
                return $c.$v.'前';
            }
        }

        return '刚刚';
    }
}

/**
 * 会员规则格式化时间 天抓换成月 年
 */
if (!function_exists('daysFormat')) {
    function daysFormat($days){
        $m = $days/30;
        if ($m < 12) {
            return $m.'个月';
        }
        if ($m >= 12) {
            $y = intval($m/12);
            $m1 = $m%12;
            if ($m1) {
                return $y.'年'.$m1.'个月';
            }else{
                return $y.'年';
            }
        }
    }
}

/**
 * [durationFormat 时间格式化]
 * @author xiaoyin
 * @datetime
 * @param $number
 * @return string
 */
if (!function_exists('durationFormat')) {
    function durationFormat($number) {
        if (! $number) {
            return '0分钟';
        }

        $newTime = '';
        if (floor($number/3600) > 0) {
            $newTime .= floor($number/3600).'小时';
            $number = $number%3600;
        }
        if ($number/60 > 0) {
            $newTime .= floor($number/60).'分钟';
            $number = $number%60;
        }
        if ($number < 60) {
            $newTime .= $number.'秒';
        }

        return $newTime;
    }
}

/**
 * 简单合并对象元素
 */
if (!function_exists('objMerge')) {
    function objMerge($arrayOne, $arrayTwo)
    {
        if (count($arrayOne) == 0) {
            return $arrayTwo;
        }
        if (count($arrayTwo) == 0) {
            return $arrayOne;
        }
        $newArray = [];
        foreach ($arrayOne as $v) {
            $newArray[] = $v;
        }
        foreach ($arrayTwo as $v) {
            $newArray[] = $v;
        }

        return $newArray;
    }
}

/**
 * 前台导航的选中状态
 * @param  $controller 控制器
 * @return true,false
 */
if (!function_exists('frontNavChecked')) {
    function frontNavChecked($controller='')
    {
        if ($controller && Route::current()->getAction()['prefix']) {
            list($temp,$currentController) = explode('/',Route::current()->getAction()['prefix']);
            return $currentController == $controller ?true:false;
        }elseif (!$controller &&  NULL == Route::current()->getAction()['prefix']) {
            return true;
        }
        return false;
    }
}
/**
 * 后台左侧导航的选中状态
 * @param  strng/array $url 菜单url
 * @param  int $type 在含有子菜单标签标志是否未子菜单判断
 * @return string 是否选中标志
 */
if (!function_exists('currentChecked')) {
    function currentChecked($url)
    {
        $pathInfo = parse_url($url);
        if ( ! (isset($pathInfo['path']) && $pathInfo['path']))
            return false;

        $currentPath = Request::getPathInfo();
        if (stripos(trim($currentPath, '/'), trim($pathInfo['path'], '/')) === 0)
            return true;

        return false;
    }
}

/**
 * 获取5位的短信验证码
 */
function phoneCode($length = 4)
{
    $phoneCode = '';
    for ($i = 0; $i < $length; $i++)
    {
        $phoneCode .= rand(0, 9);
    }

    return $phoneCode;
}

/**
 * 判断浏览器是否为ie67
 */
if (!function_exists('ie67')) {
    function ie67() {
        if ( ! isset($_SERVER['HTTP_USER_AGENT'])) return false;
        if(false!==strpos($_SERVER['HTTP_USER_AGENT'],'MSIE 6.0') || false!==strpos($_SERVER['HTTP_USER_AGENT'],'MSIE 7.0')) {
            return true;
        }
        return false;
    }
}
/**
 * 判断浏览器是否为ie678
 */
if (!function_exists('ie678')) {
    function ie678() {
        if ( ! isset($_SERVER['HTTP_USER_AGENT'])) return false;
        if(false !== strpos($_SERVER['HTTP_USER_AGENT'],'MSIE 6.0') || false!==strpos($_SERVER['HTTP_USER_AGENT'],'MSIE 7.0') || false!==strpos($_SERVER['HTTP_USER_AGENT'],'MSIE 8.0')) {
            return true;
        }
        return false;
    }
}

/**
 * 判定字符串是否在数组中
 */
if (!function_exists('strInArray')) {
    function strInArray($string, $array){
        if ( ! $string) return false;
        if ( ! $array) return false;

        if (is_array($array) && count($array) > 0) {
            foreach ($array as $v) {
                $status = strInArray($string, $v);
                if ($status)
                    return true;
            }
        } else {
            if ($string == $array) return true;

            $index  = strpos($array, '%');
            $strV   = str_replace('%', '', $array);
            $strLen = strlen($strV);
            if ($string == $strV) return true;

            if ($index === false) return false;

            $currIndex  = strpos($string, $strV);
            $currStrLen = strlen($string);
            if ($currIndex === false) return false;

            if ($index == $strLen && $currIndex == 0) {
                return true;
            }

            if ($index == 0 && $currIndex == $currStrLen-$strLen) {
                return true;
            }
        }

        return false;
    }
}

/**
 * [base64Encode 加密]
 */
if (!function_exists('base64Encode')) {
    function base64Encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}

/**
 * [base64Decode 解密]
 */
if (!function_exists('base64Decode')) {
    function base64Decode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}


if (!function_exists('getSessionId')) {
    /**
     * Get the CSRF token value.
     *
     * @return string
     *
     * @throws RuntimeException
     */
    function getSessionId()
    {
        $session = app('session');

        if (isset($session)) {
            return $session->getId();
        }

        throw new RuntimeException('Application session store not set.');
    }
}

/**
 * dataFormat        课时时间转换，秒数转换成**分钟:秒
 * @param  int $time 课时时长秒
 * @return string    格式化后的课程时长
 */
if (!function_exists('dataFormat')) {
    function dataFormat($time)
    {
        $minute = floor($time/60);
        if(strlen($minute) == 1 ){
            $minute = '0'.$minute;
        }
        $second = floor(($time - 60*$minute)%60);
        if(strlen($second) == 1 ){
            $second = '0'.$second;
        }
        $str = $minute.':'.$second;
        return $str;
    }
}
/**
 * 调试打印数据.
 *
 * @param  mixed
 * @return void
 */
if (!function_exists('vd')) {
    function vd()
    {
        header("Content-type: text/html; charset=utf-8");
        array_map(function ($x) { (new Dumper)->dump($x); }, func_get_args());
    }
}
/**
 * 数据过滤，删除重复的数据
 * @param  array $arr 待处理数据
 * @return array 处理后数组
 */
if (!function_exists('filterArray')) {
    function filterArray($arr)
    {
        $tempArr = [];
        foreach ($arr as $v) {
            if (!in_array($v,$tempArr)) {
                $tempArr[] = $v;
            }
        }
        return $tempArr;
    }
}

/**
 * 获取ip地址
 */
if ( ! function_exists('getClientIp')) {
    function getClientIp() {
        if (!empty($_SERVER["HTTP_CLIENT_IP"]))
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        else if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        else if (!empty($_SERVER["REMOTE_ADDR"]))
            $ip = $_SERVER["REMOTE_ADDR"];
        else
            $ip = "error";
        return $ip;
    }
}

/**
 * 获取标题后缀名称
 */
if ( ! function_exists('getSuffixTitle')) {
    function getSuffixTitle() {

        return "-火星时代online-火星网校-火星在线";
    }
}

/**
 * 获取标题后缀名称
 */
if ( ! function_exists('getSiteUrl')) {
    function getSiteUrl($path = '') {
        $siteUrl = env('SITE_URL', 'http://www.hxsd.tv');
        if ($path) {
            if (stripos($path, '/') !== 0)
                $path = '/'.$path;

            $siteUrl .= $path;
        }

        return $siteUrl;
    }
}

/**
 * 获取标题后缀名称
 */
if ( ! function_exists('getPassportUrl')) {
    function getPassportUrl($path = '') {
        $siteUrl = env('PASSPORT_URL');
        if ($path) {
            if (stripos($path, '/') !== 0)
                $path = '/'.$path;

            $siteUrl .= $path;
        }

        return $siteUrl;
    }
}


/**
 * 获取标题后缀名称
 */
if ( ! function_exists('getJiuyeUrl')) {
    function getJiuyeUrl($path = '') {
        $siteUrl = env('ZHIYE_URL');
        if ($path) {
            if (stripos($path, '/') !== 0)
                $path = '/'.$path;

            $siteUrl .= $path;
        }

        return $siteUrl;
    }
}

if (! function_exists('stringCut')) {
    /**
     * [stringCut 字符串截取，过长的截取一下]
     * @param  string $str [待操作字符串]
     * @param  string $length 截取长度
     * @return [string]      [截取后字符串]
     */
    function stringCut($str = '',$length = 30)
    {
        //字符串截取开始
        $start = 0;
        if ($str) {
            return mb_strlen($str,'UTF-8')>$length?mb_substr($str,$start,$length,'UTF-8').'…':$str;
        }else{
            return '';
        }
    }
}

/**
 * 检查缓存是否是第三方
 */
if (! function_exists('checkThirdCache')) {
    function checkThirdCache() {
        if ('ThirdMemcached' == config('cache.default')) {
            return true;
        }

        return false;
    }
}
if (! function_exists('checkCurrentDomain')) {
    function checkCurrentDomain()
    {
        if (isset($_SERVER['HTTP_HOST']) && ('v.hxsd.cn' == $_SERVER['HTTP_HOST'] || 'vhxsd.cn' == $_SERVER['HTTP_HOST'])) {
            header("Location: ".getSiteUrl($_SERVER['REQUEST_URI']));
            die();
        }
    }
}

/**
 * [urlAddParams 函数解释]
 * @author xiaoyin
 * @datetime
 * @param $url 原url
 * @param array $params 待拼接参数
 * @return string
 */
if ( ! function_exists('urlAddParams')) {
    function urlAddParams($url, $params = []) {
        $url = urldecode(rtrim($url));
        $url = rtrim($url, '&?');
        if (stripos($url, '?') !== false) {
            $url = $url.'&';
        } else {
            $url .= '?';
        }

        if ($params) {
            foreach ($params as $key => $value) {
                if (is_array($value)){
                    foreach($value as $k=>$v){
                        $url .= $key.'['.$k.']='.urlencode($v).'&';
                    }
                } else {
                    $url .= $key.'='.urlencode($value).'&';
                }
            }
        }

        return rtrim($url, '&');
    }
}

/**
 * 修改url参数
 */
if ( ! function_exists('modifyUrlParam')) {
    function modifyUrlParam($url, $params){
        if ( ! $url) return $url;
        if ( ! $params) return $url;
        // 获取url的参数
        $queryInfo = parse_url($url, PHP_URL_QUERY);
        if ($queryInfo) {
            $queryInfo = convertUrlQuery($queryInfo);
        }
        if ($queryInfo) {
            // 追加不存在参数
            foreach($params as $k=>$v){
                if ( !in_array($k, array_keys($queryInfo)) ) {
                    $queryInfo[$k] = $v;
                }
            }
            // 如果参数相同，则修改值
            foreach($queryInfo as $key => &$value) {
                if ( isset($params[$key]) && $params[$key]) {
                    $value = $params[$key];
                } elseif (isset($params[$key])) {
                    unset($queryInfo[$key]);
                }
            }
            unset($value);
        } else {
            $queryInfo = $params;
            foreach($queryInfo as $k => $v) {
                if (!$v) {
                    unset($queryInfo[$k]);
                }
            }
        }
        $host = explode('?', $url)[0];
        if ($queryInfo) {
            return $host.'?'.http_build_query($queryInfo);
        }

        return $host;
    }
}

/**
 * [unsetUrlParam 函数解释]
 * @author xiaoyin
 * @datetime
 * @param $param 需要删除的参数
 * @param $url 待处理的url
 * @return mixed|string
 */
if ( ! function_exists('unsetUrlParam')) {
    function unsetUrlParam($param, $url) {
        $url = preg_replace(
            array("/{$param}=[^&]*/i", '/[&]+/', '/\?[&]+/', '/[?&]+$/',),
            array('', '&', '?', '',),
            $url
        );
        return $url;
    }
}

/**
 * query  转数组
 */
if ( ! function_exists('convertUrlQuery')) {
    function convertUrlQuery($query){
        $queryParts = explode('&', $query);
        $params = array();
        foreach ($queryParts as $param) {
            $item = explode('=', $param);
            $params[$item[0]] = $item[1];
        }
        return $params;
    }
}

/**
 * 获取url query
 */
if (!function_exists('getUrlQuery')) {

    function getUrlQuery($url)
    {
        $url = urldecode($url);
        $urlArr = parse_url($url);
        if ( ! ($urlArr && isset($urlArr['query']) && $urlArr['query']))
            return [];

        $params = [];
        $queryParts = explode('&', $urlArr['query']);
        if ( ! $queryParts)
            return [];
        foreach ($queryParts as $param) {
            $item = explode('=', $param);

            if ( ! $item)
                continue;

            $params[$item[0]] = isset($item[1]) ? $item[1] : '';
        }

        return $params;
    }
}

/**
 * [getRandomPrize 经典的概率算法，概率计算函数，传入奖品处理后的概率数组，返回中奖i]
 * @author xiaoyin
 * @datetime 2016-01-28
 * @param    [type]     $proArr [中奖概率数组]
 * @return   [type]             [description]
 */
if (! function_exists('getRandomPrize')) {
    function getRandomPrize($proArr)
    {
        $res = '';
        //概率数组的总概率精度
        $proSum = array_sum($proArr);
        //把奖品列表按着数量从小到大排序
        asort($proArr);
        //概率数组循环
        foreach ($proArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $proCur) {
                $res = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset ($proArr);
        return $res;
    }
}
/**
 * [mbSubstrReplcae 解决中文截取乱码问题]
 * @author xiaoyin
 * @datetime 2016-01-29
 * @param    [type]     $str     [description]
 * @param    string     $replace [description]
 * @return   [type]              [description]
 */
if (!function_exists('mbSubstrReplcae')) {
    function mbSubstrReplcae($str,$replace = '***')
    {
        $len = mb_strlen($str,'utf-8');
        if($len >= 6){
            $str1 = mb_substr($str,0,2,'utf-8');
            $str2 = mb_substr($str,$len-2,2,'utf-8');
        }
        else{
            $str1 = mb_substr($str,0,1,'utf-8');
            $str2 = mb_substr($str,$len-1,1,'utf-8');
        }
        return $str1.$replace.$str2;
    }
}


/**
 * 重构view
 */
if (!function_exists('cacheView')) {
    function cacheView($view = null, $data = [], $mergeData = []) {
        return new Core\ThirdExt\View\NewView($view, $data, $mergeData);
    }
}

/**
 * 可逆加密
 * @auhor lulijuan
 * @param type $data
 * @param type $key
 * @return type
 */
if (!function_exists('myEncrypt')) {
    function myEncrypt($data, $key)
    {
        $data = (string)$data;
        $key = (string)$key;
        $key = md5($key);
        $x = 0;
        $len = strlen($data);
        $l = strlen($key);
        $char = '';
        for ($i = 0; $i < $len; $i++)
        {
            if ($x == $l)
            {
                $x = 0;
            }
            $char .= $key{$x};
            $x++;
        }
        $str ='';
        for ($i = 0; $i < $len; $i++)
        {
            $str .= chr(ord($data{$i}) + (ord($char{$i})) % 256);
        }
        return str_replace('=','',base64_encode($str));
    }
}

/**
 * 可逆解密
 * @auhor lulijuan
 * @param type $data
 * @param type $key
 * @return type
 */
if (!function_exists('myDecrypt')) {
    function myDecrypt($data, $key)
    {
        $key = md5($key);
        $x = 0;
        $data = base64_decode($data);
        $len = strlen($data);
        $l = strlen($key);
        $char ='';
        for ($i = 0; $i < $len; $i++)
        {
            if ($x == $l)
            {
                $x = 0;
            }
            $char .= substr($key, $x, 1);
            $x++;
        }
        $str ='';
        for ($i = 0; $i < $len; $i++)
        {
            if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1)))
            {
                $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
            }
            else
            {
                $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
            }
        }
        return $str;
    }
}
/**
 * getRandCode 生成随机码
 * @author xiaoyin
 * @date 2016-07-04
 * @param boolean $status
 * @return string
 */
if (!function_exists('getRandCode')) {
    function getRandCode($status = false, $length = 6)
    {
        // 待取值数组序列
        $arr = [
            '0', '1', '2', '3', '4', '5',
            '6', '7', '8', '9', 'A', 'B',
            'C', 'D', 'E', 'F', 'G', 'H',
            'I', 'J', 'K', 'L', 'M', 'N',
            'O', 'P', 'Q', 'R', 'S', 'T',
            'U', 'V', 'W', 'X', 'Y', 'Z'
        ];
        $randCode = ''; // 初始化随机码
        // 根据状态判断是随机生成兑换码的批次段,还是生成随机段
        if ($status) {
            $i = 0;
            while ($i < $length) {
                $randCode .= $arr[mt_rand(10, 35)];
                $i++;
            }
        } else {
            $i = 0;
            while ($i < $length) {
                $randCode .= $arr[mt_rand(0, 35)];
                $i++;
            }
        }
        return $randCode;
    }
}
/**
 * getCdKey 生成兑换码,如果arr存在值,随机值又存在于arr中将递归重复生成,直到唯一
 * @author xiaoyin
 * @date 2016-07-04
 * @param string $batchCode 批次吗
 * @param array $arr 数组集合
 * @return string
 */
if (!function_exists('getCdKey')) {
    function getCdKey($batchCode = '', $arr = [])
    {
        if (!$batchCode) {
            $batchCode = getRandCode(true);
        }
        $randCode = getRandCode();
        $cdKey = $batchCode . '-' . substr($randCode, 0, 4) . '-' . substr($randCode, 4);
        if (in_array($cdKey, $arr)) {
            $cdKey = getCdKey($batchCode, $arr);
        }

        return $cdKey;
    }
}
/**
 * getBatchCdKey 批量生成为重复的兑换码,本地php服务运行生成数量
 * 超过30000会运行超时,10000内效率正常,建议一次生成不要超过1w
 * @author xiaoyin
 * @date 2016-07-04
 * @param string $batchCode 批次吗
 * @param int $num 生成个数
 * @return array
 */
if (!function_exists('getBatchCdKey')) {
    function getBatchCdKey($batchCode = '', $num = 0)
    {
        $batchArr = [];
        $i = 0;
        while ($i < $num) {
            $batchArr[] = getCdKey($batchCode, $batchArr);
            $i++;
        }
        return $batchArr;
    }
}
/**
 * getBatchCode 生成批次码,生成与arr集合中不同的批次码
 * @author xiaoyin
 * @date 2016-07-04
 * @param array $arr 批次码集合
 * @return string
 */
if (!function_exists('getBatchCode')) {
    function getBatchCode($arr = [],$length = 6)
    {
        $batchCode = getRandCode(true,$length);
        if (in_array($batchCode, $arr)) {
            $batchCode = getBatchCode($arr);
        }
        return $batchCode;
    }
}

/**
 * getUniqueRandomCode 生成唯一随机码,生成与arr集合中不同的随机码
 * @author xubs
 * @date 2017-06-21
 * @param array     $arr     已存在随机码集合
 * @param int       $length  随机码长度
 * @param boolean   $type    默认false 数字大写字母组合  true  纯大写字母组合
 * @return string
 */
if (!function_exists('getUniqueRandomCode')) {
    function getUniqueRandomCode($arr = [],$length = 16,$type = false)
    {
        $randomCode = getRandCode($type,$length);
        if (in_array($randomCode, $arr)) {
            $randomCode = getUniqueRandomCode($arr,$length,$type);
        }
        return $randomCode;
    }
}

/**
 * [closeDebug 测试系统，部分情况下关闭debug插件]
 * @author xiaoyin
 * @datetime
 * @return bool
 */
if (!function_exists('closeDebug')){
    function closeDebug(){
        if (env("APP_DEBUG") && env('APP_ENV') == 'local') {
            Debugbar::disable();
        }

        return true;
    }
}

/**
 * 获取版本url
 */
if (!function_exists('urlVersion')){
    function urlVersion($url,  $version = 'v1'){
        if (env("WEB_VERSION") == $version)
            return url($url);

        return url($version.'/'.$url);
    }
}

/**
 * 是否移动端访问访问
 *
 * @return bool
 */
if (!function_exists('isMobile')) {
    function isMobile()
    {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return true;
        }
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset ($_SERVER['HTTP_VIA'])) {
            // 找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        // 脑残法，判断手机发送的客户端标志,兼容性有待提高
        if (isset ($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array('nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile',
                'juc',
                'iuc',
                'fennec',
                'ios',
                'ipad',
                'iphone',
                'ipaq',
                'ipod',
                'windows ce',
                'acer',
                'anywhereyougo.com',
                'asus',
                'audio',
                'blackberry',
                'blazer',
                'coolpad',
                'dopod',
                'etouch',
                'hitachi',
                'htc',
                'huawei',
                'jbrowser',
                'lenovo',
                'lg',
                'lg-',
                'lge-',
                'lge',
                'mobi',
                'moto',
                'nokia',
                'phone',
                'samsung',
                'sony',
                'symbian',
                'tablet',
                'tianyu',
                'xda',
                'xde',
                'zte',
                'mqqbrowser',
                'opera mobi',
                'MicroMessenger'


            );
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }
        // 协议法，因为有可能不准确，放到最后判断
        if (isset ($_SERVER['HTTP_ACCEPT'])) {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }
        return false;
    }
}

/**
 * 导出excel
 * $excelSet (参考：config('statistics.excel.teacher_homework_summary');)
 * data数组格式 (参考：[[1,'卢立娟','13161902969'],[2,'尹萌','13161902969'],[…]])
 */
if( !function_exists('excelExport')) {
    function excelExport($excelSet, $data){

        Excel::create($excelSet['sheet_name'], function($excel) use($excelSet,$data)
        {
            //生成
            $excel->sheet('datas', function($sheet) use($excelSet, $data){
                $cellsStyle= $excelSet['cell_title_style'];//配置
                //标题行
                $sheet->rows($excelSet['col_title']);
                //标题行的宽度
                $sheet->setWidth($excelSet['col_width']);
                //设置标题格式
                $sheet->cells($cellsStyle['range'], function ($cells) use($cellsStyle) {
                    $cells->setFont($cellsStyle['font']);
                    $cells->setAlignment($cellsStyle['cell_alignment']);
                    $cells->setValignment($cellsStyle['cell_valignment']);
                });
                //数组形式(注意格式)
                foreach ($data as $v)
                {
                    $sheet->rows([$v]);
                }
            });
        }
        )->export($excelSet['sheet_extension']);
    }
}

/**
 * 自动跳转URL过滤(根据条件判断是否跳转)
 * @return bool
 */
if (!function_exists('adaptiveUrlFilter')) {
    function adaptiveUrlFilter(){
        if (!isset($_SERVER['REQUEST_URI'])){
            return true;
        }
        $pathInfo = $_SERVER['REQUEST_URI'];
        $pathArr = explode('/',$pathInfo);
        $urlFilter = config('url_filter'); // 获取不跳转的关键字

        // 如果不在
        if (array_intersect($pathArr, $urlFilter['skip_url_filter'])){
            return false;
        }

        return true;
    }
}

/**
 * [zhiyeUrl 获取职业班网址]
 * @author xiaoyin
 * @datetime
 * @param string $url
 * @return string
 */
if (!function_exists('zhiyeUrl')) {
    function zhiyeUrl($url = '')
    {
        $realmName = env('ZHIYE_URL','http://zhiye.hxsd.tv');
        $url = ltrim($url,'/');
        $realmName = ltrim($realmName,'/');
        return $realmName.'/'.$url;
    }
}

/**
 * [bbsUrl 获取BBS网址]
 * @author xiaoyin
 * @datetime
 * @param string $url
 * @return string
 */
if (!function_exists('bbsUrl')) {
    function bbsUrl($url = '')
    {
        $realmName = env('BBS_URL','http://bbs.hxsd.tv');
        $url = ltrim($url,'/');
        $realmName = ltrim($realmName,'/');
        return $realmName.'/'.$url;
    }
}

/**
 * [passportUrl 获取用户中心网址]
 * @author xiaoyin
 * @datetime
 * @param string $url
 * @return string
 */
if (!function_exists('passportUrl')) {
    function passportUrl($url = '')
    {
        $realmName = env('PASSPORT_URL', 'http://passport.hxsd.com');
        $url = ltrim($url,'/');
        $realmName = ltrim($realmName,'/');
        return $realmName.'/'.$url;
    }
}

if (!function_exists('is_weixin'))
{
    function is_weixin()
    {
        if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
            return true;
        }
        return false;
    }
}

if(!function_exists('check_mobile'))
{
    function check_mobile(){
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $mobile_agents = array("240x320","acer","acoon","acs-","abacho","ahong","airness","alcatel","amoi","android","anywhereyougo.com","applewebkit/525","applewebkit/532","asus","audio","au-mic","avantogo","becker","benq","bilbo","bird","blackberry","blazer","bleu","cdm-","compal","coolpad","danger","dbtel","dopod","elaine","eric","etouch","fly ","fly_","fly-","go.web","goodaccess","gradiente","grundig","haier","hedy","hitachi","htc","huawei","hutchison","inno","ipad","ipaq","ipod","jbrowser","kddi","kgt","kwc","lenovo","lg ","lg2","lg3","lg4","lg5","lg7","lg8","lg9","lg-","lge-","lge9","longcos","maemo","mercator","meridian","micromax","midp","mini","mitsu","mmm","mmp","mobi","mot-","moto","nec-","netfront","newgen","nexian","nf-browser","nintendo","nitro","nokia","nook","novarra","obigo","palm","panasonic","pantech","philips","phone","pg-","playstation","pocket","pt-","qc-","qtek","rover","sagem","sama","samu","sanyo","samsung","sch-","scooter","sec-","sendo","sgh-","sharp","siemens","sie-","softbank","sony","spice","sprint","spv","symbian","tablet","talkabout","tcl-","teleca","telit","tianyu","tim-","toshiba","tsm","up.browser","utec","utstar","verykool","virgin","vk-","voda","voxtel","vx","wap","wellco","wig browser","wii","windows ce","wireless","xda","xde","zte");
        $is_mobile = false;
        foreach ($mobile_agents as $device) {
            if (stristr($user_agent, $device)) {
                $is_mobile = true;
                break;
            }
        }
        return $is_mobile;
    }
}

/**
 * 使用live的网址
 */
if (!function_exists('liveUrl')) {
    function liveUrl($url = '')
    {
        $realmName = env('LIVE_URL', 'http://live.hxsd.com');
        $url = ltrim($url,'/');
        $realmName = ltrim($realmName,'/');
        return $realmName.'/'.$url;
    }
}

/**
 * 使用socket的地址
 */
if (!function_exists('socketUrl')) {
    function socketUrl()
    {
        $url = env('SOCKET_URL');
        $port = env('SOCKET_PORT');
        return $url.':'.$port;
    }
}

/**
 * 获取公共url
 * @return bool
 */
if (!function_exists('publicUrl')) {
    function publicUrl($url){
        return trim(asset($url), '/');
    }
}

/**
 * [getCachePrefix 获取缓存前缀,项目可能存在多个域名,hxsd.tv,vhxsd.cn]
 * 存在
 * @author xiaoyin
 * @datetime
 * @return string
 */
if (!function_exists('getCachePrefix')){
    function getCachePrefix()
    {
        if (isset($_SERVER['HTTP_HOST']) && stripos($_SERVER['HTTP_HOST'], 'hxsd.tv')) {
            return 'tv_';
        } elseif(isset($_SERVER['HTTP_HOST']) && stripos($_SERVER['HTTP_HOST'], 'hxsd.com')) {
            return 'com_';
        } elseif(isset($_SERVER['HTTP_HOST']) && stripos($_SERVER['HTTP_HOST'], 'vhxsd.cn')) {
            return 'cn_';
        } else {
            return 'local_';
        }
    }
}

/**
 * 生成唯一订单号
 */
if (!function_exists('buildOrderNo')) {
    function buildOrderNo()
    {
        return date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }
}

/**
 * getRandomCode 获取随机数
 * @author xiaoyin
 * @date 2016-07-04
 * @param boolean $status
 * @return string
 */
if (!function_exists('getRandomCode')) {
    function getRandomCode($length = 5)
    {
        // 待取值数组序列
        $arr = [
            '0', '1', '2', '3', '4', '5',
            '6', '7', '8', '9', 'a', 'b',
            'c', 'd', 'e', 'f', 'g', 'h',
            'i', 'j', 'k', 'l', 'm', 'n',
            'o', 'p', 'q', 'r', 's', 't',
            'u', 'v', 'w', 'x', 'y', 'z'
        ];
        $randCode = ''; // 初始化随机码
        // 根据状态判断是随机生成兑换码的批次段,还是生成随机

        $i = 0;
        while ($i < $length) {
            $randCode .= $arr[mt_rand(0, 35)];
            $i++;
        }

        return $randCode;
    }
}

/**
 * 替换URL中的指定参数
 */
if(!function_exists('urlReplaceParams')) {
    function urlReplaceParams($url, $pullParam = [], $unsetParam = []) {
        $params = isset($_SERVER["QUERY_STRING"]) ? $_SERVER["QUERY_STRING"] : '';
        // 如果有参数则进行处理
        if($params) {
            // 按&分割参数
            $paramsArr = explode('&', $params);
            $tmpArr = [];
            // 按=分割值
            foreach ($paramsArr as $k => $v) {
                if(strpos($v, '=') !== false) {
                    $a = explode('=', $v);
                    // 如果unset参数,则删掉
                    if(in_array($a[0], $unsetParam))
                        continue;
                    // 组成新的数组
                    $tmpArr[$a[0]] = $a[1];
                }
            }
            $tmpArr = array_merge($tmpArr, $pullParam);
            // 生成新的url参数
            $newParams = http_build_query($tmpArr);
        } else {
            $newParams = http_build_query($pullParam);
        }
        $newUrl = $newParams ? $url.'?'.$newParams : $url;
        return urldecode($newUrl);
    }
}


/**
 * [scoreFormat 分数格式化]
 * @author xiaoyin
 * @datetime
 * @param $score
 * @return string
 */
if(!function_exists('scoreFormat')) {
    function scoreFormat($score)
    {
        $score = intval($score);
        if ($score >= 90) {
            return 'A';
        }

        if ($score >= 80) {
            return 'B';
        }
        if ($score >= 70) {
            return 'C';
        }
        if ($score >= 60) {
            return 'D';
        }

        return 'E';
    }
}

/**
 * 隐藏部分用户手机号
 */
if(!function_exists('hideMobile')) {
    function hideMobile($mobile) {
        return substr_replace($mobile, '****', 3, 4);
    }
}