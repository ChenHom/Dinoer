<?php

namespace App\Commands;

use App\Traits\EnvTrait;
use LaravelZero\Framework\Commands\Command;

class UpdateEnvCommand extends Command
{
    use EnvTrait;

    protected $signature = 'env:update {key} {value}';
    protected $description = '更新一個環境變數的值';

    public function handle()
    {
        try {
            $key = $this->argument('key');
            $value = $this->argument('value');

            $envContents = $this->getEnvContents();

            $updated = false;

            foreach ($envContents as &$line) {
                if (strpos($line, "export $key=") === 0) {
                    $line = "export $key=\"$value\"";
                    $updated = true;
                    break;
                }
            }

            if (!$updated) {
                $envContents[] = "export $key=\"$value\"";
            }

            $this->saveEnvContents($envContents);

            if ($updated) {
                $this->info("環境變數 {$key} 已更新");
            } else {
                $this->info("環境變數 {$key} 已新增");
            }
        } catch (\Throwable $th) {
            $this->error("操作失敗：" . $th->getMessage());
        }
    }
}
