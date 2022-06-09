<?php


class gn
{


    /*
 *把数代入返回一个需要保留的几位小数的数
 *@param float 需要四舍五入保留的数
 *@param integer 保留几位
 *@return float 四舍五入保留后的数
 */
    function sswr($number,$point = 3)
    {
        $format='%.'.$point.'f';
        return isset($number) ? sprintf($format, $number) : 0.0000;
    }

    /*
*返回参数计算的sign
*@param string 需要加密的数组
*@param string 加密的key
*@return string 加密后的sign
*/
    function sign($data,$hjykey){
        ksort($data); //重新排序$data数组
        reset($data); //内部指针指向数组中的第一个元素
        $sign = ''; //初始化需要签名的字符为空
        $urls = ''; //初始化URL参数为空
        foreach ($data AS $key => $val) { //遍历需要传递的参数
            if ($val == '' || $key == 'sign') continue; //跳过这些不参数签名
            if ($sign != '') { //后面追加&拼接URL
                $sign .= "&";
                $urls .= "&";
            }

            $sign .= "$key=$val"; //拼接为url参数形式
        }
        return md5($sign . $hjykey);
    }
    /*
*返回过滤后的字符串
*@param string 需要过滤的字符串
*@return string 返回过滤后的字符串
*/
    function htmlencode($str) {
        if(empty($str)) return;
        if($str=="") return $str;
        $str=trim($str);
        $str=str_replace("&","&amp;",$str);
        $str=str_replace(">","&gt;",$str);
        $str=str_replace("<","&lt;",$str);
        $str=str_replace(chr(32),"&nbsp;",$str);
        $str=str_replace(chr(9),"&nbsp;",$str);
        $str=str_replace(chr(34),"&",$str);
        $str=str_replace(chr(39),"&#39;",$str);
        $str=str_replace(chr(13),"<br />",$str);
        $str=str_replace("'","''",$str);
        $str=str_replace("select","sel&#101;ct",$str);
        $str=str_replace("join","jo&#105;n",$str);
        $str=str_replace("union","un&#105;on",$str);
        $str=str_replace("where","wh&#101;re",$str);
        $str=str_replace("insert","ins&#101;rt",$str);
        $str=str_replace("delete","del&#101;te",$str);
        $str=str_replace("update","up&#100;ate",$str);
        $str=str_replace("like","lik&#101;",$str);
        $str=str_replace("drop","dro&#112;",$str);
        $str=str_replace("create","cr&#101;ate",$str);
        $str=str_replace("modify","mod&#105;fy",$str);
        $str=str_replace("rename","ren&#097;me",$str);
        $str=str_replace("alter","alt&#101;r",$str);
        $str=str_replace("cast","ca&#115;",$str);
        return $str;
    }
    /*
*返回过滤后的字符串转换回来的字符串
*@param string 需要过滤的字符串
*@return string 返回过滤后的字符串
*/
    function htmldecode($str) {
        if(empty($str)) return;
        if($str=="") return $str;
        $str=str_replace("sel&#101;ct","select",$str);
        $str=str_replace("jo&#105;n","join",$str);
        $str=str_replace("un&#105;on","union",$str);
        $str=str_replace("wh&#101;re","where",$str);
        $str=str_replace("ins&#101;rt","insert",$str);
        $str=str_replace("del&#101;te","delete",$str);
        $str=str_replace("up&#100;ate","update",$str);
        $str=str_replace("lik&#101;","like",$str);
        $str=str_replace("dro&#112;","drop",$str);
        $str=str_replace("cr&#101;ate","create",$str);
        $str=str_replace("mod&#105;fy","modify",$str);
        $str=str_replace("ren&#097;me","rename",$str);
        $str=str_replace("alt&#101;r","alter",$str);
        $str=str_replace("ca&#115;","cast",$str);
        $str=str_replace("&amp;","&",$str);
        $str=str_replace("&gt;",">",$str);
        $str=str_replace("&lt;","<",$str);
        $str=str_replace("&nbsp;",chr(32),$str);
        $str=str_replace("&nbsp;",chr(9),$str);
        $str=str_replace("&",chr(34),$str);
        $str=str_replace("&#39;",chr(39),$str);
        $str=str_replace("<br />",chr(13),$str);
        $str=str_replace("''","'",$str);
        return $str;
    }
    /*
*随机数
*@param string 随机数
*@return string 返回随机数
*/
    function generate_code($length = 1) {
        return rand(pow(10,($length-1)), pow(10,$length)-1);
    }
    /*
*生成订单号
*@return string 生成订单号
*/
    function create_order_no() {
        $order_no = date('Ymd').substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(1000, 9999));
        return $order_no;
    }
    //返回当前的毫秒时间戳
    function get_msectime()
    {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        return $msectime;

    }

    /**
     *时间戳 转   日期格式 ： 精确到毫秒，x代表毫秒
     */
    function get_microtime_format($time)
    {
        if (strstr($time, '.')) {
            sprintf("%01.3f", $time); //小数点。不足三位补0
            list($usec, $sec) = explode(".", $time);
            $sec = str_pad($sec, 3, "0", STR_PAD_RIGHT); //不足3位。右边补0
        } else {
            $usec = $time;
            $sec = "000";
        }
        $date = date("Y-m-d H:i:s.x", $usec);
        return str_replace('x', $sec, $date);
    }

    /** 时间日期转时间戳格式，精确到毫秒，
     *
     */
    function get_data_format($time)
    {
        list($usec, $sec) = explode(".", $time);
        $date = strtotime($usec);
        $return_data = str_pad($date . $sec, 13, "0", STR_PAD_RIGHT); //不足13位。右边补0
        return $return_data;
    }

    function http_post($sUrl, $aHeader, $aData){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $sUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($ch, CURLOPT_POSTFIELDS, $aData); // Post提交的数据包
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($ch, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        //curl_setopt($ch, CURLOPT_HEADER, 1); //取得返回头信息

        $sResult = curl_exec($ch);
        if($sError=curl_error($ch)){
            die($sError);
        }
        curl_close($ch);
        return $sResult;
    }

    function http_get($sUrl, $aHeader){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $sUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($ch, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // 从证书中检查SSL加密算法是否存在
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        //curl_setopt($ch, CURLOPT_HEADER, 1); //取得返回头信息

        $sResult = curl_exec($ch);
        if($sError=curl_error($ch)){
            die($sError);
        }
        curl_close($ch);
        return $sResult;
    }
    /**
     * 返回两个时间的相距时间，*年*月*日*时*分*秒
     * @param int $one_time 时间一
     * @param int $two_time 时间二
     * @param int $return_type 默认值为0，0/不为0则拼接返回，1/*秒，2/*分*秒，3/*时*分*秒/，4/*日*时*分*秒，5/*月*日*时*分*秒，6/*年*月*日*时*分*秒
     * @param array $format_array 格式化字符，例，array('年', '月', '日', '时', '分', '秒')
     * @return String or false
     */
    function getRemainderTime($one_time, $two_time, $return_type=0, $format_array=array('年', '月', '日', '时', '分', '秒'))
    {
        if ($return_type < 0 || $return_type > 6) {
            return false;
        }
        if (!(is_int($one_time) && is_int($two_time))) {
            return false;
        }
        $remainder_seconds = abs($one_time - $two_time);
        //年
        $years = 0;
        if (($return_type == 0 || $return_type == 6) && $remainder_seconds - 31536000 > 0) {
            $years = floor($remainder_seconds / (31536000));
        }
        //月
        $monthes = 0;
        if (($return_type == 0 || $return_type >= 5) && $remainder_seconds - $years * 31536000 - 2592000 > 0) {
            $monthes = floor(($remainder_seconds - $years * 31536000) / (2592000));
        }
        //日
        $days = 0;
        if (($return_type == 0 || $return_type >= 4) && $remainder_seconds - $years * 31536000 - $monthes * 2592000 - 86400 > 0) {
            $days = floor(($remainder_seconds - $years * 31536000 - $monthes * 2592000) / (86400));
        }
        //时
        $hours = 0;
        if (($return_type == 0 || $return_type >= 3) && $remainder_seconds - $years * 31536000 - $monthes * 2592000 - $days * 86400 - 3600 > 0) {
            $hours = floor(($remainder_seconds - $years * 31536000 - $monthes * 2592000 - $days * 86400) / 3600);
        }
        //分
        $minutes = 0;
        if (($return_type == 0 || $return_type >= 2) && $remainder_seconds - $years * 31536000 - $monthes * 2592000 - $days * 86400 - $hours * 3600 - 60 > 0) {
            $minutes = floor(($remainder_seconds - $years * 31536000 - $monthes * 2592000 - $days * 86400 - $hours * 3600) / 60);
        }
        //秒
        $seconds = $remainder_seconds - $years * 31536000 - $monthes * 2592000 - $days * 86400 - $hours * 3600 - $minutes * 60;
        $return = false;
        switch ($return_type) {
            case 0:
                if ($years > 0) {
                    $return = $years . $format_array[0] . $monthes . $format_array[1] . $days . $format_array[2] . $hours . $format_array[3] . $minutes . $format_array[4] . $seconds . $format_array[5];
                } else if ($monthes > 0) {
                    $return = $monthes . $format_array[1] . $days . $format_array[2] . $hours . $format_array[3] . $minutes . $format_array[4] . $seconds . $format_array[5];
                } else if ($days > 0) {
                    $return = $days . $format_array[2] . $hours . $format_array[3] . $minutes . $format_array[4] . $seconds . $format_array[5];
                } else if ($hours > 0) {
                    $return = $hours . $format_array[3] . $minutes . $format_array[4] . $seconds . $format_array[5];
                } else if ($minutes > 0) {
                    $return = $minutes . $format_array[4] . $seconds . $format_array[5];
                } else {
                    $return = $seconds . $format_array[5];
                }
                break;
            case 1:
                $return = $seconds . $format_array[5];
                break;
            case 2:
                $return = $minutes . $format_array[4] . $seconds . $format_array[5];
                break;
            case 3:
                $return = $hours . $format_array[3] . $minutes . $format_array[4] . $seconds . $format_array[5];
                break;
            case 4:
                $return = $days . $format_array[2] . $hours . $format_array[3] . $minutes . $format_array[4] . $seconds . $format_array[5];
                break;
            case 5:
                $return = $monthes . $format_array[1] . $days . $format_array[2] . $hours . $format_array[3] . $minutes . $format_array[4] . $seconds . $format_array[5];
                break;
            case 6:
                $return = $years . $format_array[0] . $monthes . $format_array[1] . $days . $format_array[2] . $hours . $format_array[3] . $minutes . $format_array[4] . $seconds . $format_array[5];
                break;
            default:
                $return = false;
        }
        return $return;
    }
    
    /*
    清除所有空格 换行 
    */
    function DeleteHtml($str) 
{ 
/* http://www.manongjc.com/article/1592.html */
$str = str_replace("<br/>","",$str);
$str = str_replace("\t","",$str); 
$str = str_replace("\r\n","",$str); 
$str = str_replace("\r","",$str); 
$str = str_replace("\n","",$str); 

return trim($str); 
}

/*


*/

function log($str,$file="log/log.log"){
                     //$file = "log/" . $ddh . 'log.txt';
                    $fp = fopen($file, 'a');
                    fwrite($fp,"日记记录时间为:".date('Y-m-d H:i:s', time())."\r\n内容为:". $str. "\r\n\r\n");
                    fclose($fp);
    
    
}
}