# Aliyun Utils
Aliyun 小工具


## Installation

Pull this package in through Composer.

```js

    {
        "require": {
            "liuyuanjun/aliyun": "0.1.*"
        }
    }

```

or run in terminal:
`composer require liuyuanjun/aliyun`


## Usage

### RDC隐私配置解析
```php

    use liuyuanjun\aliyun\RDCSecurityConfig;
    
    /*
    示例:
    rdc_security_config.properties 内容:
    
    test.db.host=localhost
    test.db.username=root
    
    返回:
    [
         'test' => [
             'db' => [
                  'host' => 'localhost',
                  'username' => 'root'
             ]
         ]
    ]
    */

    RDCSecurityConfig::get('test'));

```

### Yii2 OSS 组件
```php

    /*
    配置 components 中添加：
    
        //阿里云OSS
        'oss' => [
            'class' => 'liuyuanjun\aliyun\yii2\components\OSS',
            'cdnUrlPrefix' => 'https://xxxx.xxxx.xx',
            'accessKeyId' => 'your-key',
            'accessKeySecret' => 'your-secret',
            'endpoint' => 'http://oss-cn-hangzhou.aliyuncs.com',
            'bucket' => 'your-bucket',
        ],
    */
    
    $oss = Yii::$app->oss;
    
    /*
    上传
    */
    $oss->upload($objectName, $filePath);
    /*
    下载
    */
    $oss->get($objectName, $filePath);
    /*
    推内容
    */
    $oss->put($objectName, $content);
    /*
    另一个bucket
    */
    $otherBucket = $oss->bucket($bucket);
    /*
    根据配置返回CDN URL
    */
    $oss->cdnUrl($objectName);

```

### Yii2 阿里大鱼SMS 组件
```php

    /*
    配置 components 中添加：
    
        //阿里大鱼短信
        'sms' => [
            'class' => 'liuyuanjun\aliyun\yii2\components\SMS',
            'accessKeyId' => 'your-key',
            'accessKeySecret' => 'your-secret',
            'signName' => '签名',
            'templateCode' => 'SMS_123456789',
        ],
    */
    
    $sms = Yii::$app->sms;
    
    /*
    发送
    */
    $sms->send('18888888888', ['code'=>123456]);
    /*
    另一个模板
    */
    $otherTemplate = $sms->tpl($templateCode,'新签名');
    $otherTemplate->send('13333333333', ['product'=>'手机']);

```