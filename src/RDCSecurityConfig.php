<?php

namespace liuyuanjun\aliyun;


/**
 * 阿里云RDC私密配置项读取 一般是根目录下的 rdc_security_config.properties
 * 变量名以 # 开头，代表注释，不做解析
 * 数组变量以 . 分隔，例如：
 * [
 *     'test' => [
 *         'db' => [
 *             'host' => 'localhost',
 *             'username' => 'root'
 *         ]
 *     ]
 * ]
 * 写作：
 * test.db.host=localhost
 * test.db.username=root
 *
 * 用法：RDCSecurityConfig::get('test')
 * 如果需要加载根目录 rdc_security_config.properties 以外的文件，用 RdcSecurityConfig::load('file')
 * 可以使用 Yii Alias
 *
 * @author liuyuanjun
 * @package common\helpers
 */
class RDCSecurityConfig
{

    /**
     * @var array 配置变量
     */
    protected static $configVars;


    /**
     * 载入配置文件
     *
     * @param string $file
     * @return array
     * @throws \Exception
     */
    public static function load($file = null)
    {
        $filePath = $file ? $file : dirname(__DIR__) . '/../../../rdc_security_config.properties';
        if (!is_file($filePath) || !is_readable($filePath)) {
            throw new \Exception('配置文件不存在 ' . $filePath);
        }
        $lines = static::readLinesFromFile($filePath);
        if (static::$configVars === null)
            static::$configVars = [];
        foreach ($lines as $line) {
            if (!static::isComment($line) && strpos($line, '=') !== false) {
                static::parseLine($line);
            }
        }
        return static::$configVars;
    }

    /**
     * 获取配置
     *
     * @param string $name
     * @return string|array|null
     * @throws \Exception
     */
    public static function get($name)
    {
        //没有载入过，猜测文件路径尝试载入一次
        if (static::$configVars === null) {
            static::load();
        }
        if (strpos($name, '.') === false) {
            return isset(static::$configVars[$name]) ? static::$configVars[$name] : null;
        } else {
            $keys = array_filter(array_map('trim', explode('.', $name)));
            $array = &static::$configVars;

            while (count($keys) > 1) {
                $key = array_shift($keys);
                if (isset($array[$key]) && is_array($array[$key])) {
                    $array = &$array[$key];
                } else {
                    return null;
                }
            }
            $key = array_shift($keys);
            return isset($array[$key]) ? $array[$key] : null;
        }
    }

    /**
     * 解析行
     *
     * @param string $line
     *
     * @return array|bool
     */
    protected static function parseLine($line)
    {
        if (strpos($line, '=') !== false) {
            list($name, $value) = array_map('trim', explode('=', $line, 2));
            if (strpos($name, '.') === false) {
                if (isset(static::$configVars[$name])) //跳过已设置过的
                    return false;
                static::$configVars[$name] = $value;
            } else {
                $keys = array_filter(array_map('trim', explode('.', $name)));
                $array = &static::$configVars;

                while (count($keys) > 1) {
                    $key = array_shift($keys);
                    if (!isset($array[$key])) {
                        $array[$key] = [];
                    }
                    if (!is_array($array[$key])) {
                        return false;
                    }
                    $array = &$array[$key];
                }
                $key = array_shift($keys);
                if (isset($array[$key])) //跳过已设置过的
                    return false;
                $array[$key] = $value;
            }
        }
        return false;
    }

    /**
     * 从配置文件读取条目
     *
     * @param string $filePath
     * @return array
     */
    protected static function readLinesFromFile($filePath)
    {
        $autodetect = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings', '1');
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        ini_set('auto_detect_line_endings', $autodetect);
        return $lines;
    }

    /**
     * 判断是否为注释行
     *
     * @param string $line
     * @return bool
     */
    protected static function isComment($line)
    {
        $line = ltrim($line);
        return isset($line[0]) && $line[0] === '#';
    }

}
