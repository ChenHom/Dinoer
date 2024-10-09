<?php

namespace App\Traits;

/**
 * Trait EnvTrait
 *
 * This trait provides methods to handle environment file (.dinoerc) operations such as
 * checking file readability/writability, ensuring file existence, saving contents,
 * backing up files, and reloading environment variables.
 */
trait EnvTrait
{
    /**
     * 取得 .dinoerc 文件的路徑
     *
     * @return string 環境變數文件的完整路徑
     */
    protected function getEnvFilePath()
    {
        return getenv("HOME") . '/.dinoerc';
    }

    /**
     * 從 .dinoerc 文件中讀取環境變數的內容
     *
     * @return array 由 .dinoerc 文件內容組成的陣列
     * @throws \Exception 當檔案無法讀取時拋出例外
     */
    protected function getEnvContents()
    {
        $envFilePath = $this->getEnvFilePath();

        // 檢查檔案是否存在且可讀
        if (!$this->isFileReadable($envFilePath)) {
            throw new \Exception(".dinoerc 文件無法讀取");
        }

        // 讀取檔案內容並將其轉換為陣列
        $contents = file($envFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($contents === false) {
            throw new \Exception("讀取 .dinoerc 文件失敗");
        }

        return $contents;
    }

    /**
     * 檢查檔案是否存在且可讀
     *
     * @param string $filePath 文件的完整路徑
     * @return bool 文件是否存在且可讀
     */
    protected function isFileReadable($filePath)
    {
        return file_exists($filePath) && is_readable($filePath);
    }

    /**
     * 檢查檔案是否可寫
     *
     * @param string $filePath 文件的完整路徑
     * @return bool 文件是否可寫
     */
    protected function isFileWritable($filePath)
    {
        return file_exists($filePath) && is_writable($filePath);
    }

    /**
     * 確保 .dinoerc 檔案存在，若不存在則自動建立空白文件
     *
     * @param string $filePath 文件的完整路徑
     * @return void
     */
    protected function ensureEnvFileExists($filePath)
    {
        if (!file_exists($filePath)) {
            file_put_contents($filePath, "");
        }
    }

    /**
     * 備份並儲存環境變數內容到 .dinoerc 文件中
     * 若儲存成功則重新加載環境變數
     *
     * @param array $envContents 環境變數的內容陣列
     * @throws \Exception 當檔案無法寫入或備份失敗時
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

        // 備份檔案
        $backupFilePath = $this->backupAndSaveFile($envFilePath);

        // 寫入更新後的內容
        $result = file_put_contents($envFilePath, implode(PHP_EOL, $envContents));

        // 檢查寫入是否成功
        if ($result === false) {
            throw new \Exception("寫入 .dinoerc 文件失敗");
        }

        // 成功後 reload 環境變數
        $this->reloadEnv();

        // 刪除備份檔案
        $this->deleteBackupFile($backupFilePath);
    }

    /**
     * 備份 .dinoerc 檔案並返回備份檔案的路徑
     *
     * @param string $filePath 要備份的文件路徑
     * @throws \Exception 當備份失敗時
     * @return string 備份檔案的完整路徑
     */
    protected function backupAndSaveFile($filePath)
    {
        $backupFilePath = $filePath . '_' . date('Ymd_His') . '.bak';

        if (!copy($filePath, $backupFilePath)) {
            throw new \Exception("無法備份 .dinoerc 文件");
        }

        return $backupFilePath; // 返回備份檔案的路徑
    }

    /**
     * 刪除指定的備份檔案
     *
     * @param string $backupFilePath 要刪除的備份檔案路徑
     * @return void
     */
    protected function deleteBackupFile($backupFilePath)
    {
        if (file_exists($backupFilePath)) {
            unlink($backupFilePath);
        }
    }

    /**
     * 重新加載 .dinoerc 文件的環境變數
     *
     * @return void
     */
    protected function reloadEnv()
    {
        $envFilePath = $this->getEnvFilePath();
        shell_exec("source $envFilePath");
    }
}
