<?php
namespace bigDream\thinkJump;

use think\exception\HttpResponseException;
use think\facade\Config;
use think\facade\Request;
use think\facade\Route;
use think\Response;

class Jump
{
    /**
     * 配置
     * @var array
     */
    protected static $config;

    /**
     * 初始化实例
     * @param array $config 配置
     */
    public static function init($config = []) {
        // 默认配置
        $defaultConfig = [
            // 成功跳转页面模板文件
            'success_tmpl' => __DIR__ . '/success.html',
            // 成功跳转页停留时间(秒)
            'success_wait' => 3,
            // 成功跳转的code值
            'success_code' => 0,
            // 错误跳转页面模板文件
            'error_tmpl'   => __DIR__ . '/error.html',
            // 错误跳转页停留时间(秒)
            'error_wait'   => 3,
            // 错误跳转的code值
            'error_code'   => 1,
        ];

        // 应用配置
        $appConfig = Config::get('jump', []);

        self::$config = array_merge($defaultConfig, $appConfig, $config);
    }

    /**
     * 成功跳转
     * @param string $msg 信息
     * @param string $url 跳转地址
     * @param int $wait 等待时间
     * @param array $header Header头
     * @param mixed $data 其它数据
     */
    public static function success(string $msg, string $url = '', int $wait = null, array $header = [], $data = null)
    {
        is_null(self::$config) && self::init();

        // URL处理
        $url = self::buildUrl($url);

        // 跳转等待时间
        null === $wait && $wait = self::$config['success_wait'];

        $result = [
            'code' => self::$config['success_code'],
            'msg'  => $msg,
            'url'  => $url,
            'wait' => $wait,
            'data' => $data
        ];

        // AJAX则返回JSON
        if (Request::isAjax()) {
            $response = Response::create($result, 'json')
                ->header($header);
        } else {
            $response = Response::create(self::$config['success_tmpl'], 'view')
                ->header($header)
                ->assign($result);
        }

        throw new HttpResponseException($response);
    }

    /**
     * 错误跳转
     * @param string $msg 错误信息
     * @param string $url 跳转地址
     * @param int $wait 等待时间
     * @param array $header Header头
     * @param mixed $data 其它数据
     */
    public static function error(string $msg, string $url = '', int $wait = null, array $header = [], $data = null)
    {
        is_null(self::$config) && self::init();

        // URL处理
        $url = self::buildUrl($url);

        // 跳转等待时间
        null === $wait && $wait = self::$config['error_wait'];

        $result = [
            'code' => self::$config['error_code'],
            'msg'  => $msg,
            'url'  => $url,
            'wait' => $wait,
            'data' => $data
        ];

        // AJAX则返回JSON
        if (Request::isAjax()) {
            $response = Response::create($result, 'json')
                ->header($header);
        } else {
            $response = Response::create(self::$config['error_tmpl'], 'view')
                ->header($header)
                ->assign($result);
        }

        throw new HttpResponseException($response);
    }

    /**
     * 页面重定向
     * @param string $url 重定向地址
     * @param string $msg 消息
     * @param int $code 状态码
     * @param array $header Header头
     */
    public static function redirect(string $url = null, string $msg = '', int $code = 302, array $header = [])
    {
        is_null(self::$config) && self::init();

        // URL处理
        $url = self::buildUrl($url);

        // AJAX则返回JSON
        if (Request::isAjax()) {
            $result = [
                'code' => $code,
                'msg'  => $msg,
                'url'  => $url,
            ];
            $response = Response::create($result, 'json');
        } else {
            $response = Response::create($url, 'redirect', $code);
        }

        throw new HttpResponseException($response->header($header));
    }

    /**
     * URL地址生成
     * @param string $url
     * @return string
     */
    public static function buildUrl(string $url = null)
    {
        if(null === $url) {
            $url = Request::server('HTTP_REFERER', '/');
        } elseif (0 === strpos($url, '/') || 8 > strpos($url, '://')) {
            return $url;
        }

        return (string)Route::buildUrl($url);
    }
}