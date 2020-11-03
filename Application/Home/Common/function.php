<?php
//获取用户余额
function getMemberAmount(){
    $where["userid"] = session("ca_userid");
    $amount = M("Member")->where($where)->getField("amount");
    if($amount){
        return $amount;
    }else{
        return "0.00";
    }
}
/**
 * 获取token
 *
 */
function getToken($str = '')
{
    $str = $str ? $str : uniqid();
    return md5(time().$str.'yitianyang');
}
/**
 * 验证手机号是否正确(美国)
 * @author honfei
 * @param number $mobile
 * @return bool
 */
function isMobile($mobile) {
    if (!is_numeric($mobile)) {
        return false;
    }
    if(strlen($mobile) != 10){
        return false;
    }
    return true;
    return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;
}
/**
 * 验证邮箱是否正确
 *
 * @param  $email
 * @return bool
 */
function isEmail($email) {
    return preg_match('/^[A-Za-z\d]+([-_.][A-Za-z\d]+)*@([A-Za-z\d]+[-.])+[A-Za-z\d]{2,4}$/', $email) ? true : false;
}
/**
 * 验证昵称是否正确
 *
 * @param  $nickname
 * @return bool
 */
function isNickname($nickname) {
    return preg_match('/^[a-zA-Z\x{4e00}-\x{9fa5}]{4,20}$/u', $nickname) ? true : false;
}
/**
 * 验证网址是否正确
 *
 * @param  $weburl
 * @return bool
 */
function isWeburl($weburl) {
    return preg_match('/^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/', $weburl) ? true : false;
}

/**
 * 验证昵称是否正确
 *
 * @param  $email
 * @return bool
 */
function isRealname($realname) {
    return preg_match('/^[\x{4e00}-\x{9fa5}]{2,16}$/u', $realname) ? true : false;
}
/**
 * 对用户的密码进行加密
 * @param $password
 * @param $encrypt //传入加密串，在修改密码时做认证
 * @return array/password
 */
function password($password, $encrypt='') {
    $pwd = array();
    $pwd['encrypt'] =  $encrypt ? $encrypt : create_randomstr();
    $pwd['password'] = md5(md5(trim($password)).$pwd['encrypt']);
    return $encrypt ? $pwd['password'] : $pwd;
}
/**
 * 检查密码长度是否符合规定
 *
 * @param STRING $password
 * @return  TRUE or FALSE
 */
function is_password($password) {
    $strlen = strlen($password);
    if($strlen >= 6 && $strlen <= 20) return true;
    return false;
}
/**
 * 生成随机字符串
 * @param string $lenth 长度
 * @return string 字符串
 */
function create_randomstr($lenth = 6) {
    return random($lenth, '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ');
}
/**
 * 产生随机字符串
 *
 * @param    int        $length  输出长度
 * @param    string     $chars   可选的 ，默认为 0123456789
 * @return   string     字符串
 */
function random($length, $chars = '0123456789') {
    $hash = '';
    $max = strlen($chars) - 1;
    for($i = 0; $i < $length; $i++) {
        $hash .= $chars[mt_rand(0, $max)];
    }
    return $hash;
}
/**
 * 取得文件扩展
 *
 * @param $filename 文件名
 * @return 扩展名
 */
function fileext($filename) {
    return strtolower(trim(substr(strrchr($filename, '.'), 1, 10)));
}
/**
 * 字符截取
 */
function str_cut($title, $num) {
    if (mb_strlen($title, "utf-8") > $num) {
        $title = mb_substr($title, 0, $num, "utf-8") . "...";
    }
    return $title;
}
/**
 * 检测验证码
 * @param  integer $id 验证码ID
 */
function check_verify($code, $id = '1'){
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}
/**
 *缩略图生成
 */
function thumb($url, $target_width = 300, $target_height = 300, $fixed = ''){
    $imgurl = str_replace(C('SITE_URL'), '', $url);
    if(!file_exists($imgurl)){
        return $url;
    }
    $imgName = basename($imgurl);
    $imgPath = dirname($imgurl);
    $image = new \Think\Image();
    $image->open(''.$imgurl.'');
    $thumbPath = dirname($imgurl).'/thumb_'.$target_width.'_'.$target_height.'_'.basename($imgurl);
    if(file_exists($thumbPath)){
        return C('SITE_URL').$thumbPath;
    }else{
        $result = $image->thumb($target_width, $target_height,\Think\Image::IMAGE_THUMB_CENTER)->save(''.$thumbPath.'');
        if($result){
            return C('SITE_URL').$thumbPath;
        }else{
            return $url;
        }
    }
}
function reduce($imgurl, $target_width = 300, $target_height = 300, $fixed = ''){
    $imgurl = str_replace(C('SITE_URL'), '', $imgurl);
    ///return $imgurl;
    if(!file_exists($imgurl)){
        return false;
    }
    $imgName = basename($imgurl);
    $imgPath = dirname($imgurl);
    $image = new \Think\Image();
    $image->open(''.$imgurl.'');
    $thumbPath = dirname($imgurl).'/thumb_'.$target_width.'_'.$target_height.'_'.basename($imgurl);
    if(file_exists($thumbPath)){
        return C('SITE_URL').$thumbPath;
    }else{
        $result = $image->thumb($target_width, $target_height,\Think\Image::IMAGE_THUMB_SCALE)->save(''.$thumbPath.'');
        return C('SITE_URL').$thumbPath;
    }
}
/**
 * 生成订单流水号
 */
function create_sn(){
    mt_srand((double )microtime() * 1000000 );
    return date("YmdHis" ).str_pad( mt_rand( 1, 99999 ), 5, "0", STR_PAD_LEFT );
}
/**
 * 对数据进行编码转换
 * @param array/string $data       数组
 * @param string $input     需要转换的编码
 * @param string $output    转换后的编码
 */
function array_iconv($data, $input = 'gbk', $output = 'utf-8') {
    if (!is_array($data)) {
        return iconv($input, $output, $data);
    } else {
        foreach ($data as $key=>$val) {
            if(is_array($val)) {
                $data[$key] = array_iconv($val, $input, $output);
            } else {
                $data[$key] = iconv($input, $output, $val);
            }
        }
        return $data;
    }
}
/**
 * 将时间戳转为文字表示
 *@param $time
 */
function mdate($time = NULL) {
    $text = '';
    $time = $time === NULL || $time > time() ? time() : intval($time);
    $t = time() - $time; //时间差 （秒）
    $y = date('Y', $time)-date('Y', time());//是否跨年
    switch($t){
        case $t == 0:
            $text = '刚刚';
            break;
        case $t < 60:
            $text = $t . '秒前'; // 一分钟内
            break;
        case $t < 60 * 60:
            $text = floor($t / 60) . '分钟前'; //一小时内
            break;
        case $t < 60 * 60 * 24:
            $text = floor($t / (60 * 60)) . '小时前'; // 一天内
            break;
        case $t < 60 * 60 * 24 * 3:
            $text = floor($time/(60*60*24)) ==1 ?'昨天 ' . date('H:i', $time) : '前天 ' . date('H:i', $time) ; //昨天和前天
            break;
        case $t < 60 * 60 * 24 * 30:
            $text = date('m月d日 H:i', $time); //一个月内
            break;
        case $t < 60 * 60 * 24 * 365&&$y==0:
            $text = date('m月d日', $time); //一年内
            break;
        default:
            $text = date('Y年m月d日', $time); //一年以前
            break;
    }
    return $text;
}
/**
 * 获取请求ip
 *
 * @return ip地址
 */
function ip() {
    if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $ip = getenv('HTTP_CLIENT_IP');
    } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $ip = getenv('REMOTE_ADDR');
    } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
}

/**
 * @desc 根据两点间的经纬度计算距离 ,返回米
 * @param float $lat 纬度值
 * @param float $lng 经度值
 */
function getDistance($lat1, $lng1, $lat2, $lng2) {
    $earthRadius = 6371000;
    $lat1 = ($lat1 * pi() ) / 180;
    $lng1 = ($lng1 * pi() ) / 180;

    $lat2 = ($lat2 * pi() ) / 180;
    $lng2 = ($lng2 * pi() ) / 180;
    $calcLongitude = $lng2 - $lng1;
    $calcLatitude = $lat2 - $lat1;
    $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
    $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
    $calculatedDistance = $earthRadius * $stepTwo;
    return round($calculatedDistance,0);
}

// 生成8位推广号
function createSpreadCode(){
    $memberDb = M('Member');
    $spread_code = random(8, '123456789');
    $where['spread_code'] = $spread_code;
    $isIn = $memberDb->where($where)->find();
    if($isIn){
        createSpreadCode();
    }else{
        return $spread_code;
    }
}


/**
 * http请求
 * @param  string  $url    请求地址
 * @param  boolean|string|array $params 请求数据
 * @param  integer $ispost 0/1，是否post
 * @param  array  $header
 * @param  $verify 是否验证ssl
 * return string|boolean          出错时返回false
 */
function http($url, $params = '', $ispost = 1, $header = array(), $verify = false) {
    $httpInfo = array();
    $ch = curl_init();
    if(!empty($header)){
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    //忽略ssl证书
    if($verify === true){
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    } else {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    }
    if ($ispost) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_URL, $url);
    } else {
        if (is_array($params)) {
            $params = http_build_query($params);
        }
        if ($params) {
            curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
        } else {
            curl_setopt($ch, CURLOPT_URL, $url);
        }
    }
    $response = curl_exec($ch);
    if ($response === FALSE) {
        trace("cURL Error: " . curl_errno($ch) . ',' . curl_error($ch), 'error');
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
        trace($httpInfo, 'error');
    }else{
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    }

    return $response;
}
/**
 * base64 转图片并保存
 * @param  $base64
 * @return url
 */
function base64_upload($base64) {
    $base64_image = str_replace(' ', '+', $base64);
    //post的数据里面，加号会被替换为空格，需要重新替换回来，如果不是post的数据，则注释掉这一行
    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image, $result)){
        //匹配成功
        if($result[2] == 'jpeg'){
            $image_name = uniqid().'.jpg';
            //纯粹是看jpeg不爽才替换的
        }else{
            $image_name = create_guid().'.'.$result[2];
        }
        $uploadsPath = C('UPLOAD_PATH')."idcard/".date("Y-m-d");
        if (!file_exists($uploadsPath)){
            mkdir($uploadsPath);
        }
        $image_file = $uploadsPath."/{$image_name}";
        //服务器文件存储路径
        if (file_put_contents($image_file, base64_decode(str_replace($result[1], '', $base64_image)))){
            return $image_file;
        }else{
            return false;
        }
    }else{
        return false;
    }
}
/**
 * 生成唯一值
 */
function create_guid($parameter = '')
{
    $guid = '';
    $uid = uniqid("", true);
    $data = strlen(trim($parameter)) > 0 ? $parameter : time();
    $data .= $_SERVER['REQUEST_TIME'];
    $data .= $_SERVER['HTTP_USER_AGENT'];
    $data .= $_SERVER['LOCAL_ADDR'];
    $data .= $_SERVER['LOCAL_PORT'];
    $data .= $_SERVER['REMOTE_ADDR'];
    $data .= $_SERVER['REMOTE_PORT'];
    $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
    $guid = substr($hash, 0, 8) . '-' . substr($hash, 8, 4) . '-' . substr($hash, 12, 4) . '-' . substr($hash, 16, 4) . '-' . substr($hash, 20, 12);
    return $guid;
}

//判断是否是ip地址
function is_ip($ip){
    if(preg_match('/^((?:(?:25[0-5]|2[0-4]\d|((1\d{2})|([1-9]?\d)))\.){3}(?:25[0-5]|2[0-4]\d|((1\d{2})|([1 -9]?\d))))$/', $ip)){
        return 1;
    }else{
        return 0;
    }
}

?>