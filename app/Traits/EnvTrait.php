<?php

namespace App\Traits;

trait EnvTrait
{

    /**
     * @return string
     */
    protected function getEnvFilePath()
    {
        return getenv("HOME") . '/.dinoerc';
    }

    /**
     * 檢查檔案是否存在並能讀取
     *
     * @param string $filePath
     * @return bool
     */
    protected function isFileReadable($filePath)
    {
        return file_exists($filePath) && is_readable($filePath);
    }

    /**
     * 檢查檔案是否能寫入
     *
     * @param string $filePath
     * @return bool
     */
    protected function isFileWritable($filePath)
    {
        return file_exists($filePath) && is_writable($filePath);
    }

    /**
     * 確保檔案存在，如果不存在則自動建立
     * @param string $filePath
     */
    protected function ensureEnvFileExists(string $filePath)
    {
        if (!file_exists($filePath)) {
            file_put_contents($filePath, ""); // 如果文件不存在則建立一個空文件
        }
    }

    /**
     * 讀取 .dinoerc 文件的所有行
     *
     * @return array
     * @throws \Exception
     */
    protected function getEnvContents()
    {
        $envFilePath = $this->getEnvFilePath();

        // 確保檔案存在
        $this->ensureEnvFileExists($envFilePath);

        if (!$this->isFileReadable($envFilePath)) {
            throw new \Exception(".dinoerc 文件不存在或無法讀取");
        }

        return file($envFilePath, FILE_IGNORE_NEW_LINES);
    }

    /**
     * 將內容寫回 .dinoerc 文件
     *
     * @param array $envContents
     * @return void
     */
    protected function saveEnvContents($envContents)
    {
        $envFilePath = $this->getEnvFilePath();

        // 確保檔案存在
        $this->ensureEnvFileExists($envFilePath);

        if (!$this->isFileWritable($envFilePath)) {
            throw new \Exception(".dinoerc 文件無法寫入");
        }

        file_put_contents($envFilePath, implode(PHP_EOL, $envContents));
    }
}
