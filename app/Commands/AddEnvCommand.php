<?php

namespace App\Commands;

use App\Traits\EnvTrait;
use LaravelZero\Framework\Commands\Command;

class AddEnvCommand extends Command
{
    use EnvTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'env:add {key} {value}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '添加一個新的環境變數';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $key = $this->argument('key');
            $value = $this->argument('value');

            // 檢查變數名稱是否合法
            if (!$this->isValidKey($key)) {
                $this->error("無效的環境變數名稱：{$key}");
                return;
            }

            $envContents = $this->getEnvContents();

            // 檢查變數是否已經存在
            foreach ($envContents as $line) {
                if (strpos($line, "export $key=") === 0) {
                    $this->error("環境變數 {$key} 已存在，請使用更新命令來修改");
                    return;
                }
            }

            // 添加新變數到 .dinoerc
            $envContents[] = "export $key=\"$value\"";

            // 保存更新後的內容
            $this->saveEnvContents($envContents);

            $this->info("環境變數 {$key} 已新增");
        } catch (\Throwable $th) {
            $this->error("操作失敗：" . $th->getMessage());
        }
    }

    /**
     * 驗證變數名稱是否有效
     *
     * @param string $key
     * @return bool
     */
    private function isValidKey($key): bool
    {
        return preg_match('/^[A-Z_][A-Z0-9_]*$/', $key);
    }
}
