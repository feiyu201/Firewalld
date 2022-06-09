<?php
error_reporting(0) ;
/*
脚本执行方式:bash <(curl -Lso- hjy.sh)
yx.txt 需要加载的IP列表 至于拒绝还是允许你在脚本设置拒绝还是允许然后开启防火墙
version.txt  这个是控制版本号的 如果本地和服务器这边不一致会强制更新哦
网站的伪静态是nginx.conf  目录ip 是每次访问判断是否指定时间内的 不是就请求第三方 然后保存到本地

特别注意:宝塔会在安全那边 也就是添加端口那边 会自动加载 只要不点安全就行 否则哪怕你清空了库他也会在数据库遗留 这个是宝塔避免太多做缓存使用!

添加计划任务如下:
#开始
wget --no-check-certificate -P /www/wwwroot/cs.mfykvm.com/ip https://www.ipdeny.com/ipblocks/data/countries/all-zones.tar.gz
cd /www/wwwroot/cs.mfykvm.com/ip
tar -xvf all-zones.tar.gz
rm -f all-zones.tar.gz
chmod -R 777 /www/wwwroot/cs.mfykvm.com/ip
#结束
设置一天更新一次
记得修改网站根目录/www/wwwroot/cs.mfykvm.com
*/

$xty = strtolower($_SERVER['HTTP_USER_AGENT']);
if (strpos($xty, 'curl') !== false or strpos($xty, 'wg1et') !== false) {
    include "Firewall_Mange_New.sh";
    exit;
}
if (empty($_GET['dq'])) {
    header("Content-type: text/html; charset=utf-8");
    echo "<!--快乐的小哥哥,脚本执行方式:脚本执行方式:bash <(curl -Lso- hjy.sh)-->";
    echo "<h1>请点击对应的名字获取对应的IP段哦!每天更新.如果存在不在的输入对应的国家代码一样可以获取到<br><a href='/all-zones.tar.gz'>点击下载全部国家的IP段</a><br><a href='/ip'>点击打开列表</a></h1><br>";
    $dir = 'ip/';
    if (is_dir($dir)) {
        $info = opendir($dir);
        while (($file = readdir($info)) !== false) {
            if (strpos($xty, 'zone') !== false or $file=="all-zones.tar.gz"){
            continue;
            }
            $jm = basename($file, ".zone");
            if ($jm != "" and $jm != "." and $jm != "..") {
                $i++;
                if($i==1120){
                    $i=0;
                   echo '<a href="/' . strtolower($jm) . '.html" >' . transCountryCode($jm) . '('.strtoupper($jm).')</a><br>'; 
                }else{
                    echo '<a href="/' . strtolower($jm) . '.html" >' . transCountryCode($jm) . '('.strtoupper($jm).')</a>&nbsp;&nbsp;&nbsp;';
                }
                
            }
        }
        closedir($info);
    }
} else {
    header("Content-type: text/plain; charset=utf-8");
    $cache_name = "ip/" . $_GET['dq'] . ".zone";
    $cache_lifetime = 60;
    include $cache_name;
        exit;
    if (file_exists($cache_name) && filectime($cache_name) + $cache_lifetime > time()) {
        include $cache_name;
        exit;
    } else {
        ob_start();
    }
    $jg = http_get('http://www.ipdeny.com/ipblocks/data/countries/' . $_GET['dq'] . '.zone');

    if ($jg != "没有哦!") {
        echo $jg;
        $content = ob_get_contents();
        ob_end_flush();
        $handle = fopen($cache_name, 'w');
        fwrite($handle, $content);
        fclose($handle);
    } else {
        echo $jg;
    }
}
function http_get($sUrl)
{
    $xforip = rand(1, 254) . "." . rand(1, 254) . "." . rand(1, 254) . "." . rand(1, 254);
    $aHeader = array("Connection: Keep-Alive", "Accept: application/json, text/javascript, */*; q=0.01", "Pragma: no-cache", "Accept-Language: zh-Hans-CN,zh-Hans;q=0.8,en-US;q=0.5,en;q=0.3", "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.198 Safari/537.36", 'CLIENT-IP:' . $xforip, 'X-FORWARDED-FOR:' . $xforip); // 请求头信息
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $sUrl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
    curl_setopt($ch, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // 从证书中检查SSL加密算法是否存在
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    $sResult = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($sError = curl_error($ch)) {
        die($sError);
    }
    if ($httpCode == 200) {
        curl_close($ch);
        return $sResult;
    } else {
        return "没有哦!";
    }
}

function transCountryCode($code)
{
    $code = strtoupper($code);
    $index = array('AA' => '阿鲁巴',
        'AD' => '安道尔',
        'AE' => '阿联酋',
        'AF' => '阿富汗',
        'AG' => '安提瓜和巴布达',
        'AL' => '阿尔巴尼亚',
        'AM' => '亚美尼亚',
        'AN' => '荷属安德列斯',
        'AO' => '安哥拉',
        'AX' => '土地群岛',
        'AQ' => '南极洲',
        'AR' => '阿根廷',
        'AS' => '东萨摩亚',
        'AT' => '奥地利',
        'AU' => '澳大利亚',
        'AP' => '亚洲太平洋区',
        'AZ' => '阿塞拜疆',
        'AI' => '安圭拉岛',
        'AV' => '安圭拉岛',
        'AW' => '阿卢巴',
        'BA' => '波黑',
        'BB' => '巴巴多斯',
        'BD' => '孟加拉',
        'BE' => '比利时',
        'BF' => '巴哈马',
        'BF' => '布基纳法索',
        'BG' => '保加利亚',
        'BL' => '圣巴托洛缪',
        'BH' => '巴林',
        'BI' => '布隆迪',
        'BJ' => '贝宁',
        'BM' => '百慕大',
        'BN' => '文莱布鲁萨兰',
        'BO' => '玻利维亚',
        'BQ' => '英属南极地区',
        'BR' => '巴西',
        'BS' => '巴哈马',
        'BT' => '不丹',
        'BV' => '布韦岛',
        'BW' => '博茨瓦纳',
        'BY' => '白俄罗斯',
        'BZ' => '伯里兹',
        'CA' => '加拿大',
        'CB' => '柬埔寨',
        'CC' => '可可斯群岛',
        'CD' => '刚果',
        'CF' => '中非',
        'CG' => '刚果',
        'CH' => '瑞士',
        'CI' => '象牙海岸',
        'CK' => '库克群岛',
        'CL' => '智利',
        'CM' => '喀麦隆',
        'CN' => '中国',
        'CO' => '哥伦比亚',
        'CW' => '英国殖民地威廉斯堡',
        'CR' => '哥斯达黎加',
        'CS' => '捷克斯洛伐克',
        'CU' => '古巴',
        'CV' => '佛得角',
        'CX' => '圣诞岛',
        'CY' => '塞普路斯',
        'CZ' => '捷克',
        'DE' => '德国',
        'DJ' => '吉布提',
        'DK' => '丹麦',
        'DM' => '多米尼加共和国',
        'DO' => '多米尼加联邦',
        'DZ' => '阿尔及利亚',
        'EC' => '厄瓜多尔',
        'EE' => '爱沙尼亚',
        'EG' => '埃及',
        'EH' => '西撒哈拉',
        'EU' => '欧盟',
        'ER' => '厄立特里亚',
        'ES' => '西班牙',
        'ET' => '埃塞俄比亚',
        'FI' => '芬兰',
        'FJ' => '斐济',
        'FK' => '福兰克群岛',
        'FM' => '米克罗尼西亚',
        'FO' => '法罗群岛',
        'FR' => '法国',
        'FX' => '法国-主教区',
        'GA' => '加蓬',
        'GB' => '英国',
        'GD' => '格林纳达',
        'GE' => '格鲁吉亚',
        'GF' => '法属圭亚那',
        'GG' => '格恩西岛',
        'GH' => '加纳',
        'GI' => '直布罗陀',
        'GL' => '格陵兰岛',
        'GM' => '冈比亚',
        'GN' => '几内亚',
        'GP' => '法属德洛普群岛',
        'GQ' => '赤道几内亚',
        'GR' => '希腊',
        'GS' => 'S. Georgia and S. Sandwich Isls.',
        'GT' => '危地马拉',
        'GU' => '关岛',
        'GW' => '几内亚比绍',
        'GY' => '圭亚那',
        'HK' => '中国香港特区',
        'HM' => '赫德和麦克唐纳群岛',
        'HN' => '洪都拉斯',
        'HR' => '克罗地亚',
        'HT' => '海地',
        'HU' => '匈牙利',
        'ID' => '印度尼西亚',
        'IE' => '爱尔兰',
        'IL' => '以色列',
        'IM' => '马恩岛',
        'IN' => '印度',
        'IO' => '英属印度洋领地',
        'IQ' => '伊拉克',
        'IR' => '伊朗',
        'IS' => '冰岛',
        'IT' => '意大利',
        'RS' => '塞尔维亚',
        'JE' => '泽西岛',
        'JM' => '牙买加',
        'JO' => '约旦',
        'JP' => '日本',
        'KE' => '肯尼亚',
        'KG' => '吉尔吉斯斯坦',
        'KH' => '柬埔寨',
        'KI' => '基里巴斯',
        'KM' => '科摩罗',
        'KN' => '圣基茨和尼维斯',
        'KP' => '韩国',
        'KR' => '朝鲜',
        'KW' => '科威特',
        'KY' => '开曼群岛',
        'KZ' => '哈萨克斯坦',
        'LA' => '老挝',
        'LB' => '黎巴嫩',
        'LC' => '圣卢西亚',
        'LI' => '列支顿士登',
        'LK' => '斯里兰卡',
        'LR' => '利比里亚',
        'LS' => '莱索托',
        'LT' => '立陶宛',
        'LU' => '卢森堡',
        'LV' => '拉托维亚',
        'LY' => '利比亚',
        'MA' => '摩洛哥',
        'MC' => '摩纳哥',
        'MD' => '摩尔多瓦',
        'ME' => '黑山共和国',
        'MG' => '马达加斯加',
        'MH' => '马绍尔群岛',
        'MK' => '马其顿',
        'ML' => '马里',
        'MM' => '缅甸',
        'MN' => '蒙古',
        'MO' => '中国澳门特区',
        'MP' => '北马里亚纳群岛',
        'MQ' => '法属马提尼克群岛',
        'MR' => '毛里塔尼亚',
        'MS' => '蒙塞拉特岛',
        'MT' => '马耳他',
        'MU' => '毛里求斯',
        'MV' => '马尔代夫',
        'MW' => '马拉维',
        'MX' => '墨西哥',
        'MY' => '马来西亚',
        'MZ' => '莫桑比克',
        'NA' => '纳米比亚',
        'NC' => '新卡里多尼亚',
        'NE' => '尼日尔',
        'NF' => '诺福克岛',
        'NG' => '尼日利亚',
        'NI' => '尼加拉瓜',
        'NL' => '荷兰',
        'NO' => '挪威',
        'NP' => '尼泊尔',
        'NR' => '瑙鲁',
        'NT' => '中立区(沙特-伊拉克间)',
        'NU' => '纽爱',
        'NZ' => '新西兰',
        'OM' => '阿曼',
        'PA' => '巴拿马',
        'PE' => '秘鲁',
        'PF' => '法属玻里尼西亚',
        'PG' => '巴布亚新几内亚',
        'PH' => '菲律宾',
        'PK' => '巴基斯坦',
        'PL' => '波兰',
        'PM' => '圣皮艾尔和密克隆群岛',
        'PN' => '皮特克恩岛',
        'PS' => '巴勒斯坦的领土',
        'PR' => '波多黎各',
        'PT' => '葡萄牙',
        'PW' => '帕劳',
        'PY' => '巴拉圭',
        'QA' => '卡塔尔',
        'RE' => '法属尼留旺岛',
        'RO' => '罗马尼亚',
        'RU' => '俄罗斯',
        'RW' => '卢旺达',
        'SA' => '沙特阿拉伯',
        'SB' => '索罗门群岛',
        'SC' => '塞舌尔',
        'SD' => '苏丹',
        'SE' => '瑞典',
        'SG' => '新加坡',
        'SH' => '圣赫勒拿',
        'SI' => '斯罗文尼亚',
        'SJ' => '斯瓦尔巴特和扬马延岛',
        'SK' => '斯洛伐克',
        'SL' => '塞拉利昂',
        'SM' => '圣马力诺',
        'SN' => '塞内加尔',
        'SO' => '索马里',
        'SR' => '苏里南',
        'ST' => '圣多美和普林西比',
        'SU' => '前苏联',
        'SV' => '萨尔瓦多',
        'SX' => '荷属圣马丁',
        'SY' => '叙利亚',
        'SZ' => '斯威士兰',
        'Sb' => '所罗门群岛',
        'TC' => '特克斯和凯科斯群岛',
        'TD' => '乍得',
        'TF' => '法国南部领地',
        'TG' => '多哥',
        'TH' => '泰国',
        'TJ' => '塔吉克斯坦',
        'TK' => '托克劳群岛',
        'TL' => '东帝汶',
        'TM' => '土库曼斯坦',
        'TN' => '突尼斯',
        'TO' => '汤加',
        'TR' => '土尔其',
        'TT' => '特立尼达和多巴哥',
        'TV' => '图瓦卢',
        'TW' => '中国台湾省',
        'TZ' => '坦桑尼亚',
        'UA' => '乌克兰',
        'UG' => '乌干达',
        'UK' => '英国',
        'UM' => '美国海外领地',
        'US' => '美国',
        'UY' => '乌拉圭',
        'UZ' => '乌兹别克斯坦',
        'VA' => '梵蒂岗',
        'VC' => '圣文森特和格陵纳丁斯',
        'VE' => '委内瑞拉',
        'VG' => '英属维京群岛',
        'VI' => '美属维京群岛',
        'VN' => '越南',
        'VU' => '瓦努阿鲁',
        'WF' => '瓦里斯和福图纳群岛',
        'WS' => '西萨摩亚',
        'YE' => '也门',
        'YT' => '马约特岛',
        'YU' => '南斯拉夫',
        'ZA' => '南非',
        'ZM' => '赞比亚',
        'ZR' => '扎伊尔',
        'ZW' => '津巴布韦');
    $code = strtoupper($code);
    $name = $index[$code];
    if (empty($name)) {
        return $code;
    }
    return $name;
}