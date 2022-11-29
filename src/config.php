<?php

// 跳转配置
return [
    // 成功跳转页面模板文件
    'success_tmpl' => app()->getRootPath() . 'vendor/big-dream/think-jump/src/success.html',
    // 成功跳转页停留时间(秒)
    'success_wait' => 3,
    // 成功跳转的code值
    'success_code' => 0,
    // 错误跳转页面模板文件
    'error_tmpl'   => app()->getRootPath() . 'vendor/big-dream/think-jump/src/error.html',
    // 错误跳转页停留时间(秒)
    'error_wait'   => 3,
    // 错误跳转的code值
    'error_code'   => 1,
    // 默认AJAX请求返回数据格式，可用：Json,Jsonp,Xml
    'ajax_return' => 'Json',
];