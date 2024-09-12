<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Process;
use LaravelZero\Framework\Commands\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '安裝設定的軟體清單';

    protected $software_list = [
        'brew' => [
            'depends' => ['brew'],
            'commands' => [
                [
                    'name' => 'install casks',
                    'command' => [
                        'font-cascadia-code-pl',
                        'php',
                        'postman',
                        'telegram',
                        '1password',
                        'the-unarchiver',
                        'datagrip',
                        'microsoft-edge',
                        'visual-studio-code',
                        'chatgpt',
                        'appcleaner',
                        'itsycal',
                        'iterm2'
                    ],
                    'options' => ['--casks ']
                ],
                [
                    'name' => 'install',
                    'command' => ['tlrc', 'git', 'eza', 'fzf', 'go', 'jq', 'git'],
                    'options' => []
                ],
            ]
        ],
        'go' => [
            'depends' => ['go'],
            'commands' => [
                [
                    'name' => 'sshw',
                    'command' => ['github.com/yinheli/sshw/cmd/sshw@latest'],
                    'options' => []
                ]
            ]
        ],
        'open' => [
            'commands' => [
                [
                    'name' => 'line',
                    'command' => ['https://apps.apple.com/tw/app/line/id539883307?mt=12'],
                    'options' => []
                ]
            ]
        ],
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $installed_depend = true;
        foreach ($this->software_list as $software_name => $commands) {

            if ($depend = data_get($commands, 'depends')) {
                foreach ($depend as $dep) {
                    if (Process::command("which {$dep}")->run()->exitCode()) {
                        $this->error("{$dep} 未安裝，請先安裝");
                        $installed_depend = false;
                        break;
                    }
                }
            }

            if ($installed_depend) {
                foreach ($commands['commands'] as $command) {
                    $this->info("{$software_name} - 安裝 {$command['name']}...");
                    Process::command($this->runner($software_name) . $command['command'])
                        ->timeout(3600)
                        ->start(output: function ($type, $buffer) {
                            $this->output->write($buffer);
                        })->wait();
                }
            }
        }

        $this->info('安裝完成');
    }

    private function runner(string $runner): string
    {
        return match ($runner) {
            'brew' => 'brew install ',
            'go' => 'go install ',
            'open' => 'open ',
            default => ''
        };
    }
}
