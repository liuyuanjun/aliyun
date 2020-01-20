<?php

namespace liuyuanjun\aliyun\yii2\components;

use OSS\OssClient;
use yii\base\Component;
use yii\base\Exception;

/**
 * 阿里OSS Yii2 组件
 *
 * @author liuyuanjun
 */
class OSS extends Component
{
    public $accessKeyId;
    public $accessKeySecret;
    public $endpoint;
    public $bucket;
    public $cdnUrlPrefix;
    protected $_ossClient;

    /**
     * @return OssClient
     * @throws Exception
     */
    public function getOssClient()
    {
        if ($this->_ossClient === null) {
            if (!$this->accessKeyId || !$this->accessKeySecret || !$this->endpoint) {
                throw new Exception('OSS配置参数缺失');
            }
            $this->_ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
        }
        return $this->_ossClient;
    }

    /**
     * 设置bucket
     * @param string $bucket
     * @return $this
     */
    public function bucket($bucket)
    {
        if (!$this->bucket || $this->bucket === $bucket) {
            $this->bucket = $bucket;
            return $this;
        }
        $cloned = clone $this;
        $cloned->bucket = $bucket;
        return $cloned;
    }

    /**
     * 根据配置返回CDN URL
     * @param string $object
     * @return string
     */
    public function cdnUrl($object)
    {
        $object = trim($object);
        return $object ? rtrim($this->cdnUrlPrefix, '/') . '/' . ltrim($object, '/') : '';
    }

    /**
     * 去掉url,返回object
     * @param $url
     * @return string
     * @author Yuanjun.Liu <6879391@qq.com>
     */
    public function stripUrl($url)
    {
        return ltrim(parse_url($url, PHP_URL_PATH), '/');
    }

    /**
     * 提取Url里的 object name
     * @param string $url
     * @param bool $strict 只有比对相同Url前缀的才返回
     * @return string
     */
    public function getObjectNameFromUrl($url, $strict = true)
    {
        $parseUrl = parse_url($url);
        if ($strict && $parseUrl['host'] != parse_url($this->cdnUrlPrefix)['host']) {
            return false;
        }
        return ltrim($parseUrl['path'], '/');
    }

    /**
     * 字符串上传
     *
     * @param string $object
     * @param string $content
     * @param null|array $options
     * @return mixed
     * @throws Exception
     */
    public function put($object, $content, $options = NULL)
    {
        return $this->getOssClient()->putObject($this->bucket, $object, $content, $options);
    }

    /**
     * 下载
     * @param string $object
     * @param string|array|null $optionsOrFilePath
     * @return string
     * @throws Exception
     */
    public function get($object, $optionsOrFilePath = NULL)
    {
        $options = is_string($optionsOrFilePath) ? [OssClient::OSS_FILE_DOWNLOAD => $optionsOrFilePath] : $optionsOrFilePath;
        return $this->getOssClient()->getObject($this->bucket, $object, $options);
    }

    /**
     * 删除
     * @param string $object
     * @param array|null $options
     * @return string
     * @throws Exception
     */
    public function delete($object, $options = NULL)
    {
        return $this->getOssClient()->deleteObject($this->bucket, $object, $options);
    }

    /**
     * 文件上传
     *
     * @param string $object
     * @param string $filePath
     * @param null|array $options
     * @return mixed
     * @throws Exception
     * @throws \OSS\Core\OssException
     */
    public function upload($object, $filePath, $options = NULL)
    {
        return $this->getOssClient()->uploadFile($this->bucket, $object, $filePath, $options);
    }

    /**
     * 拷贝
     *
     * @param string $fromObject
     * @param string $toBucket
     * @param string $toObject
     * @param null|array $options
     * @return mixed
     * @throws Exception
     * @throws \OSS\Core\OssException
     */
    public function copy($fromObject, $toBucket, $toObject, $options = NULL)
    {
        return $this->getOssClient()->copyObject($this->bucket, $fromObject, $toBucket, $toObject, $options);
    }

    /**
     * 判断是否存在
     * @param string $object
     * @param null|array $options
     * @return bool
     * @throws Exception
     */
    public function isExists($object, $options = NULL)
    {
        return $this->getOssClient()->doesObjectExist($this->bucket, $object, $options);
    }

}
