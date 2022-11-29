<?php
namespace bigDream\thinkJump;

use think\Facade;
use think\Response;

/**
 * Jump 跳转类门面
 * @see \bigDream\thinkJump\ThinkJump
 * @package bigDream\thinkJump
 * @mixin ThinkJump
 * @method static void setConfig(array|string $name, mixed $value = null) 设置配置
 * @method static mixed getConfig(string $name = null) 设置配置
 * @method static Response|void success(string $msg, string $url = null, int $wait = null, array $header = [], $data = null) 成功跳转
 * @method static Response|void error(string $msg, string $url = null, int $wait = null, array $header = [], $data = null) 错误跳转
 * @method static Response|void redirect(string $url = null, string $msg = '', int $code = 302, array $header = []) 页面重定向
 * @method static Response|void result($data, $code = null, $msg = '', string $type = null, array $header = []) 返回封装后的API数据
 * @method static ThinkJump returnResponse() 返回封装后的API数据
 * @method static string buildUrl(string $url = null) URL地址生成
 */
class Jump extends Facade
{
    protected static function getFacadeClass()
    {
        return ThinkJump::class;
    }
}