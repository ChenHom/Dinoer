<?php

namespace App\Commands;

use App\Traits\EnvTrait;
use LaravelZero\Framework\Commands\Command;

class PullEnvCommand extends Command
{
    use EnvTrait;

    protected $url = 'https://gist.githubusercontent.com/ChenHom/c8ec83ecd28b2c74178139559aeda0e4/raw/dinoerc';
    protected $signature = 'env:pull';
    protected $description = '從 GitHub 的 repo 拉取環境變數檔案';

    public function handle()
    {
        try {
            $envFilePath = $this->getEnvFilePath();

            // 檢查本地檔案是否存在
            if ($this->isFileReadable($envFilePath)) {
                $shouldOverwrite = $this->confirm(
                    ".dinoerc 文件已存在，您確定要覆蓋嗎？",
                    false // 預設選擇為不覆蓋
                );

                if (!$shouldOverwrite) {
                    $this->info('操作已取消。');
                    return; // 取消操作
                }
            }

            $this->pullEnvFile($this->url);
            $this->info('環境變數檔案已成功拉取。');
        } catch (\Throwable $th) {
            $this->error('拉取失敗: ' . $th->getMessage());
        }
    }

    /**
     * 從指定的 repo URL 拉取 .dinoerc 檔案
     *
     * @param string $repoUrl GitHub repo 的 URL
     * @throws \Exception 當拉取過程中發生錯誤時
     * @return void
     */
    protected function pullEnvFile($repoUrl)
    {
        // $filePath = '.dinoerc'; // 本地的檔案名稱
        // $rawUrl = str_replace('github.com', 'raw.githubusercontent.com', $repoUrl);
        // $rawUrl .= '/main/' . $filePath; // 修改為 raw 檔案的 URL
        // $rawUrl = $repoUrl . '/dinoerc';
        $rawUrl = $repoUrl;

        $contents = file_get_contents($rawUrl);

        if ($contents === false) {
            throw new \Exception("無法從 {$rawUrl} 拉取檔案");
        }

        // 儲存拉取的內容
        $writeResult = file_put_contents($this->getEnvFilePath(), $contents);

        // 檢查寫入是否成功
        if ($writeResult === false) {
            throw new \Exception("寫入本地檔案失敗");
        }
    }
}
