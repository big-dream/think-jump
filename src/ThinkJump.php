<?php
declare (strict_types = 1);

namespace bigDream\thinkJump;

use think\exception\HttpResponseException;
use think\facade\Config;
use think\facade\Request;
use think\facade\Route;
use think\Response;

/**
 * ThinkJump 跳转类
 * @package bigDream\thinkJump
 */
class ThinkJump
{
    /**
     * 配置
     * @var array
     */
    protected $config;

    /**
     * 不抛出异常，直接返回Response
     * @var bool
     */
    protected $returnResponse = false;

    /**
     * 构造方法
     * @param array $config 配置
     */
    public function __construct(array $config = [])
    {
        // 默认配置
        $defaultConfig = [
            // 成功跳转页面模板文件
            'success_tmpl'    => __DIR__ . '/success.html',
            // 成功跳转页停留时间(秒)
            'success_wait'    => 3,
            // 成功跳转的code值
            'success_code'    => 0,
            // 错误跳转页面模板文件
            'error_tmpl'      => __DIR__ . '/error.html',
            // 错误跳转页停留时间(秒)
            'error_wait'      => 3,
            // 错误跳转的code值
            'error_code'      => 1,
            // 封装API数据的默认code
            'result_code'     => 0,
            // 默认AJAX请求返回数据格式，可用：Json,Jsonp,Xml
            'ajax_return'     => 'Json',
        ];

        // 应用配置
        $appConfig = Config::get('jump', []);

        $this->config = array_merge($defaultConfig, $appConfig, $config);
    }

    /**
     * 初始化配置
     * @deprecated 2.0.0 采用setConfig()设置配置
     * @param array $config 配置
     * @see setConfig()
     */
    public function init(array $config = []) {
        $this->setConfig($config);
    }

    /**
     * 设置配置
     * @param array|string $name 配置名或配置数组
     * @param mixed $value 配置值
     * @return void
     */
    public function setConfig($name, $value = null)
    {
        if (is_array($name)) {
            $this->config = array_merge($this->config, $name);
        } else {
            $this->config[$name] = $value;
        }
    }

    /**
     * 获取配置
     * @param null|string $name 配置名
     * @return array|mixed|null
     */
    public function getConfig($name = null)
    {
        if (null === $name) {
            return $this->config;
        } else {
            return $this->config[$name] ?? null;
        }
    }

    /**
     * 成功跳转
     * @param string $msg 信息
     * @param string $url 跳转地址
     * @param int $wait 等待时间
     * @param array $header Header头
     * @param mixed $data 其它数据
     * @return Response
     * @throws \think\exception\HttpResponseException
     */
    public function success(string $msg, string $url = null, int $wait = null, array $header = [], $data = null)
    {
        // URL处理
        if (null === $url) {
            $url = Request::server('HTTP_REFERER', 'javascript:history.back()');
        } else {
            $url = self::buildUrl($url);
        }

        // 跳转等待时间
        null === $wait && $wait = $this->config['success_wait'];

        $result = [
            'code' => $this->config['success_code'],
            'msg'  => $msg,
            'url'  => $url,
            'wait' => $wait,
            'data' => $data
        ];

        // AJAX则返回JSON
        if (Request::isAjax()) {
            $response = Response::create($result, $this->getAjaxReturn())->header($header);
        } else {
            $response = Response::create($this->config['success_tmpl'], 'view')->header($header)->assign($result);
        }

        return $this->throwException($response);
    }

    /**
     * 错误跳转
     * @param string $msg 错误信息
     * @param string $url 跳转地址
     * @param int $wait 等待时间
     * @param array $header Header头
     * @param mixed $data 其它数据
     * @return Response
     * @throws \think\exception\HttpResponseException
     */
    public function error(string $msg, string $url = null, int $wait = null, array $header = [], $data = null)
    {
        // URL处理
        if (null === $url) {
            $url = 'javascript:history.back()';
        } else {
            $url = self::buildUrl($url);
        }

        // 跳转等待时间
        null === $wait && $wait = $this->config['error_wait'];

        $result = [
            'code' => $this->config['error_code'],
            'msg'  => $msg,
            'url'  => $url,
            'wait' => $wait,
            'data' => $data
        ];

        // AJAX则返回JSON
        if (Request::isAjax()) {
            $response = Response::create($result, $this->getAjaxReturn())->header($header);
        } else {
            $response = Response::create($this->config['error_tmpl'], 'view')->header($header)->assign($result);
        }

        return $this->throwException($response);
    }

    /**
     * 页面重定向
     * @param string $url 重定向地址
     * @param string $msg 消息
     * @param int $code 状态码
     * @param array $header Header头
     * @return Response
     * @throws \think\exception\HttpResponseException
     */
    public function redirect(string $url = null, string $msg = '', int $code = 302, array $header = [])
    {
        // URL处理
        $url = self::buildUrl($url);

        // AJAX则返回JSON
        if (Request::isAjax()) {
            $result = [
                'code' => $code,
                'msg'  => $msg,
                'url'  => $url,
            ];
            $response = Response::create($result, $this->getAjaxReturn());
        } else {
            $response = Response::create($url, 'redirect', $code);
        }

        return $this->throwException($response);
    }

    /**
     * 返回封装后的API数据
     * @param mixed $data 数据
     * @param mixed $code 状态
     * @param string $msg 提示信息
     * @param string $type 数据类型
     * @param array $header Header头
     * @return Response
     * @throws \think\exception\HttpResponseException
     */
    public function result($data, $code = null, $msg = '', string $type = null, array $header = [])
    {
        $result = [
            'code' => $code ?? $this->config['result_code'],
            'msg'  => $msg,
            'time' => time(),
            'data' => $data,
        ];

        $response = Response::create($result, $type ?? $this->getAjaxReturn())->header($header);

        return $this->throwException($response);
    }

    /**
     * 下次直接返回Response，不抛出异常
     * @return $this
     */
    public function returnResponse()
    {
        $this->returnResponse = false;

        return $this;
    }

    /**
     * URL地址生成
     * @param string $url
     * @return string
     */
    public function buildUrl(string $url = null)
    {
        if(null === $url) {
            $url = Request::server('HTTP_REFERER', '/');
        } elseif (preg_match('@^([a-zA-Z0-9-]+://|/|javascript:)@', $url)) {
            return $url;
        }

        return (string)Route::buildUrl($url);
    }

    /**
     * 获取AJAX请求返回数据格式，根据客户端接受的数据类型自动判断
     * @return string
     */
    protected function getAjaxReturn()
    {
        $type = Request::type();
        switch ($type) {
            case 'json':
            case 'xml':
                return $type;
                break;
            case 'js':
                return 'jsonp';
                break;
            default:
                return $this->config['ajax_return'];
                break;
        }
    }

    /**
     * 抛出异常或返回响应对象
     * @param \think\Response $response
     * @throws \think\exception\HttpResponseException
     * @return \think\Response
     */
    protected function throwException(Response $response)
    {
        if (true === $this->returnResponse) {
            $this->returnResponse = true;

            return $response;
        }

        throw new HttpResponseException($response);
    }
}