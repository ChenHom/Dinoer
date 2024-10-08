<?php

namespace App\Commands;

use App\Traits\EnvTrait;
use LaravelZero\Framework\Commands\Command;

class ListEnvCommand extends Command
{
    use EnvTrait;

    protected $signature = 'env:list';
    protected $description = '列出所有環境變數';

    public function handle()
    {
        try {
            $envContents = $this->getEnvContents();

            if (empty($envContents)) {
                $this->info("沒有任何環境變數設定");
            } else {
                $this->info("目前的環境變數：");
                foreach ($envContents as $line) {
                    if (strpos($line, "export ") === 0) {
                        $this->line($line);
                    }
                }
            }
        } catch (\Throwable $th) {
            $this->error("操作失敗：" . $th->getMessage());
        }
    }
}
