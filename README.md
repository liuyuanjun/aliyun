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