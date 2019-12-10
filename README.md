# ThinkPHP6跳转扩展

ThinkPHP6已从核心中移除Jump类，类里面包含`success`、`error`和`redirect`方法。这几个方法在项目里面用的蛮多的，所以将它们移植成一个扩展使用。

## 安装
```
composer require big-dream/think-jump
```

## 使用示例

### Controller::success()
```php
// 显示提示信息，然后返回上一页
\bigDream\thinkJump::success('操作成功!');

// 显示提示信息，然后返回Index/index页面
\bigDream\thinkJump::success('操作成功!', 'Index/index');

// 显示提示信息，然后15秒后返回Index/index页面
\bigDream\thinkJump::success('操作成功!', 'Index/index', 15);

// 显示提示信息，并且为页面添加header头，然后15秒后返回Index/index页面
\bigDream\thinkJump::success('操作成功!', 'Index/index', 15, ['auth-token' => 'abcd学英语']);
```

在AJAX请求下，会返回JSON，以下是返回示例
```json
{
    "code": 0,
    "msg": "操作成功!",
    "url": "/Index/index",
    "wait": 15,
    "data": null
}
```

### Controller::error()
```php
// 显示提示信息，然后返回上一页
\bigDream\thinkJump::error('操作失败!');

// 显示提示信息，然后返回Index/index页面
\bigDream\thinkJump::error('操作失败!', 'Index/index');

// 显示提示信息，然后15秒后返回Index/index页面
\bigDream\thinkJump::error('操作失败!', 'Index/index', 15);

// 显示提示信息，并且为页面添加header头，然后15秒后返回Index/index页面
\bigDream\thinkJump::error('操作失败!', 'Index/index', 15, ['auth-token' => 'abcd学英语']);
```

在AJAX请求下，会返回JSON，以下是返回示例
```json
{
    "code": 1,
    "msg": "操作失败!",
    "url": "/Index/index",
    "wait": 15,
    "data": null
}
```

### Controller::redirect()
```php
// 跳转到上一页
\bigDream\thinkJump::redirect();

// 跳转到Index/index页面，设置在AJAX请求下返回的信息
\bigDream\thinkJump::redirect('Index/index', '请先登录');

// 跳转到Index/index页面，设置状态码和在AJAX请求下返回的信息
\bigDream\thinkJump::redirect('Index/index', '请先登录', 301);

// 跳转到Index/index页面，设置状态码、Header头和在AJAX请求下返回的信息
\bigDream\thinkJump::redirect('Index/index', '请先登录', 301, ['auth-token' => 'abcd学英语']);
```

在AJAX请求下，会返回JSON，以下是返回示例
```json
{
    "code": 301,
    "msg": "请先登录",
    "url": "/Index/index"
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
];
```

### 初始化配置
如果想配置初始化配置，可以手动初始化。
```php
\bigDream\thinkJump::init([
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
]);
```