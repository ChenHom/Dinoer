<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;
use App\Traits\EnvTrait;

class DeleteEnvCommand extends Command
{
    use EnvTrait;

    protected $signature = 'env:delete {key}';
    protected $description = '刪除一個環境變數';

    public function handle()
    {
        try {
            //code...

            $key = $this->argument('key');

            $envContents = $this->getEnvContents();

            $updatedContents = [];
            $deleted = false;

            foreach ($envContents as $line) {
                if (strpos($line, "export $key=") === 0) {
                    $deleted = true; // 找到並刪除該行
                    continue;
                }
                $updatedContents[] = $line;
            }

            if ($deleted) {
                $this->saveEnvContents($updatedContents);
                $this->info("環境變數 {$key} 已刪除");
            } else {
                $this->error("找不到環境變數 {$key}");
            }
        } catch (\Throwable $th) {
            $this->error("操作失敗：" . $th->getMessage());
        }
    }
}
