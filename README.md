# ThinkPHP 6.1 跳转扩展

从 ThinkPHP 6.0 开始，从核心中移除Jump类，类里面包含`success`、`error`、`redirect`和`result`方法。这几个方法在项目里面用的蛮多的，所以将它们移植成一个扩展使用。

## 安装
```
composer require big-dream/think-jump:3.*
```

## 使用示例

### Jump::success($msg, $url, $wait, $header, $data)
```php
// 显示提示信息，然后返回上一页
\bigDream\thinkJump\Jump::success('操作成功!');

// 显示提示信息，然后返回Index/index页面
\bigDream\thinkJump\Jump::success('操作成功!', 'Index/index');

// 显示提示信息，然后15秒后返回Index/index页面
\bigDream\thinkJump\Jump::success('操作成功!', 'Index/index', 15);

// 显示提示信息，并且为页面添加header头，然后15秒后返回Index/index页面
\bigDream\thinkJump\Jump::success('操作成功!', 'Index/index', 15, ['auth-token' => 'abcd学英语']);
```

### Jump::error($msg, $url, $wait, $header, $data)
```php
// 显示提示信息，然后返回上一页
\bigDream\thinkJump\Jump::error('操作失败!');

// 显示提示信息，然后返回Index/index页面
\bigDream\thinkJump\Jump::error('操作失败!', 'Index/index');

// 显示提示信息，然后15秒后返回Index/index页面
\bigDream\thinkJump\Jump::error('操作失败!', 'Index/index', 15);

// 显示提示信息，并且为页面添加header头，然后15秒后返回Index/index页面
\bigDream\thinkJump\Jump::error('操作失败!', 'Index/index', 15, ['auth-token' => 'abcd学英语']);
```

### Jump::redirect($url, $msg, $code, $header)
```php
// 跳转到上一页
\bigDream\thinkJump\Jump::redirect();

// 跳转到Index/index页面，设置在AJAX请求下返回的信息
\bigDream\thinkJump\Jump::redirect('Index/index', '请先登录');

// 跳转到Index/index页面，设置状态码和在AJAX请求下返回的信息
\bigDream\thinkJump\Jump::redirect('Index/index', '请先登录', 301);

// 跳转到Index/index页面，设置状态码、Header头和在AJAX请求下返回的信息
\bigDream\thinkJump\Jump::redirect('Index/index', '请先登录', 301, ['auth-token' => 'abcd学英语']);
```

### Jump::result($data, $code, $msg, $type, $header)
```php
$result = [
    ['id' => 1, 'name' => 'jwj'],
    ['id' => 2, 'name' => 'china'],
];

// 返回封装后的数据集
\bigDream\thinkJump\Jump::result($result);

// 返回封装后的数据集，并且设置code
\bigDream\thinkJump\Jump::result($result, 'success');

// 返回封装后的数据集，并且设置code和msg
\bigDream\thinkJump\Jump::result($result, 'success', '查询成功');

// 返回封装后的数据集，并且设置code、msg和数据类型
\bigDream\thinkJump\Jump::result($result, 'success', '查询成功', 'json');

// 返回封装后的数据集，并且设置code、msg、数据类型和Header头
\bigDream\thinkJump\Jump::result($result, 'success', '查询成功', 'json', ['auth-token' => 'abcd学英语']);
```

## AJAX请求
当前请求信息`header`中的`x-requested-with`为`XMLHttpRequest`时，会被认定为AJAX请求。
这时候，程序根据`header`中的`accept`来自动判断客户端所需要的数据类型，然后返回对应的数据类型。

目前，仅支持三种数据类型：`json`、`jsonp`和`xml`。

### JSON返回示例
```json
{
    "code": 0,
    "msg": "操作成功!",
    "url": "/Index/index",
    "wait": 15,
    "data": null
}
```

### JSONP返回示例
> 可在请求信息中携带`callback`参数来自定义`JSONP`回调方法，一般放在URL地址参数中，也可以放在POST参数中。
```js
jsonpReturn({
    "code": 0,
    "msg": "操作成功!",
    "url": "/Index/index",
    "wait": 15,
    "data": null
});
```

### XML返回示例
```xml
<?xml version="1.0" encoding="utf-8"?>
<think>
    <code>0</code>
    <msg>操作成功!</msg>
    <url>/Index/index</url>
    <wait>15</wait>
    <data></data>
</think>
```

## 返回Response
为了方便在中间件里使用，`2.0.0`版本增加了返回`\think\Response`对象的支持，只需要在调用跳转方法前调用`returnResponse`方法即可。

简单示例：
```php
return \bigDream\thinkJump\Jump::returnResponse()->success('操作成功!');

return \bigDream\thinkJump\Jump::returnResponse()->error('操作失败!');

return \bigDream\thinkJump\Jump::returnResponse()->redirect();

reutrn \bigDream\thinkJump\Jump::returnResponse()->result([
    ['id' => 1, 'name' => 'jwj'],
    ['id' => 2, 'name' => 'china'],
]);
```

中间件完整示例：
```php
<?php
namespace app\middleware;

use bigDream\thinkJump\Jump;

class Check
{

    /**
     * @param \think\Request $request
     * @param \Closure $next
     * @return \think\Response
     */
    public function handle($request, \Closure $next)
    {
        if (time() % 2) {
            return Jump::returnResponse()->error('呀，偶数不能访问!');
        } else {
            return $next($request);
        }
    }
}
```

## 使用配置
初始化时，首先会读取应用配置`jump.php`，然后再合并初始化时传入的配置。
优先级：`默认配置` < `配置文件` < `初始化配置`

### 配置文件
默认没有配置文件，如需使用配置文件，请自行在配置目录(/config)新建一个`jump.php`文件并写入以下内容，然后根据需求修改配置内容。
```php
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
```

### 初始化配置
如果想配置初始化配置，可以手动初始化。
```php
\bigDream\thinkJump\Jump::init([
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
]);
```