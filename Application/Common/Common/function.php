<?php

function curl_post($url,$data = '',$cuscode = '',$header = array(),$file = array()){

    $post_data = array(
        'secretkey' => C('API_SECRET'),
        'data' => $data,
        'sign' => md5($data.C('API_SECRET'))
    );
    if ($file && count($file) > 0){
        $post_data = array_merge($post_data,$file);
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}