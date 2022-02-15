<?php

namespace Miaoxing\Qcloud\Service;

class Cos
{
    /**
     * {@inheritdoc}
     * @see Cos::write
     */
    public static function write(string $path, string $content, array $options = []): \Wei\Ret
    {
    }

    /**
     * {@inheritdoc}
     * @see Cos::getUrl
     */
    public static function getUrl(string $path): string
    {
    }

    /**
     * @see Cos::getClient
     */
    public static function getClient(): \Qcloud\Cos\Client
    {
    }

    /**
     * 将本地文件写入到文件系统中
     *
     * @param string $file
     * @param array{path?: string} $options
     * @return Ret
     * @see BaseStorage::writeFile
     */
    public static function writeFile(string $file, array $options = []): \Wei\Ret
    {
    }

    /**
     * 将本地文件写入到文件系统中并删除原来的文件
     *
     * @see BaseStorage::moveLocal
     */
    public static function moveLocal(string $path, array $options = []): \Wei\Ret
    {
    }
}

namespace Miaoxing\Qcloud\Service;

if (0) {
    class Cos
    {
        /**
         * {@inheritdoc}
         * @see Cos::write
         */
        public function write(string $path, string $content, array $options = []): \Wei\Ret
        {
        }

        /**
         * {@inheritdoc}
         * @see Cos::getUrl
         */
        public function getUrl(string $path): string
        {
        }

        /**
         * @see Cos::getClient
         */
        public function getClient(): \Qcloud\Cos\Client
        {
        }

        /**
         * 将本地文件写入到文件系统中
         *
         * @param string $file
         * @param array{path?: string} $options
         * @return Ret
         * @see BaseStorage::writeFile
         */
        public function writeFile(string $file, array $options = []): \Wei\Ret
        {
        }

        /**
         * 将本地文件写入到文件系统中并删除原来的文件
         *
         * @see BaseStorage::moveLocal
         */
        public function moveLocal(string $path, array $options = []): \Wei\Ret
        {
        }
    }
}
