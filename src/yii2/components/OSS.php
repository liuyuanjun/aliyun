<?php

namespace liuyuanjun\aliyun\yii2\components;

use OSS\OssClient;
use yii\base\Component;
use yii\base\Exception;

/**
 * 阿里OSS
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
        return rtrim($this->cdnUrlPrefix, '/') . '/' . ltrim($object, '/');
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

}
