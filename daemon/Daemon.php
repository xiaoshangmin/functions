<?php
/*
 * @Author: xsm
 * @Date: 2020-03-02 10:29:13
 * @LastEditTime: 2020-03-12 14:59:26
 * @Description: Linux下php实现守护进程
 */

namespace xiaoshangmin;

class Daemon
{
    /**
     * 创建的子进程数总数
     *
     * @var integer
     */
    private int $_total = 0;

    /**
     * pid文件路径
     */
    private string $_pidFile;

    /**
     * 日志文件
     */
    public string $logFile;

    /**
     * 命令行参数
     *
     * @var array
     */
    private array $_argument = [];


    /**
     * 动作  stop/start 
     */
    private string $_action;

    /**
     * 可创建的最大子进程数
     *
     * @var integer
     */
    public int $_maxChild = 5;


    function __construct()
    {
        $argv = $_SERVER['argv'];
        $argc = $_SERVER['argc'];
        if ($argc > 1) {
            $params = array_slice($argv, 1);
            foreach ($params as $k => $v) {
                $this->_parseArgument($v);
            }
        }

        $this->_pidFile = __DIR__ . DIRECTORY_SEPARATOR . 'run.pid';

        $this->logFile = __DIR__ . DIRECTORY_SEPARATOR . 'daemon.log';

        $this->_action = $this->_argument['a'] ?? 'start';
        if ('stop' == $this->_action) {
            $this->stop();
        } elseif ('start' == $this->_action) {
            // declare(ticks=1);
            pcntl_signal(SIGINT, array($this, "signal"));
            $this->start();
        }
    }

    /**
     * 启动
     *
     * @return void
     */
    public function start(): void
    {
        $this->_maxChild = $this->_argument['max'] ?? 1;
        $pid = pcntl_fork();
        if ($pid == -1) {
            $this->log("无法创建子进程");
            exit();
        } elseif ($pid) {
            exit(0);
        }
        $this->run();
    }

    function run(): void
    {
        $str = "初始化的程序和参数";
        $p =  posix_getpid();
        $this->log("父进程的->id：{$p}");
        file_put_contents($this->_pidFile, $p, LOCK_EX);
        while ($this->checkPid()) {
            $pid = pcntl_fork();
            if ($pid > 0) {
                $this->_total++;
                if ($this->_total < $this->_maxChild) {
                    continue;
                }
                $pid = pcntl_wait($status);
                $log = "监听子进程：{$pid}，退出";
                $this->log($log);
                $this->_total--;
            }
            if (-1 == $pid) {
                $log = "无法创建子进程";
                $this->log($log);
                exit(1);
            }
            if (!$pid) {
                $this->doSomething($str);
            }
        }
        $this->dispatch();
    }

    /**
     * 业务逻辑
     *
     * @param [type] $str
     * @return void
     */
    function doSomething($str)
    {
        $id = posix_getpid();
        for ($i = 0; $i < 10; $i++) {
            $log =  $str . "-子进程:{$id}-{$i}" . PHP_EOL;
            $this->log($log);
            sleep(1);
        }
        $log =  "子进程执行10次后退出:{$id}-{$i}" . PHP_EOL;
        $this->log($log);
        exit(0);
    }

    /**
     * 停止
     *
     * @return void
     */
    public function stop(): void
    {
        if (!$this->checkPid()) {
            $log = $this->_pidFile . ",does not exists\n";
            $this->log($log);
            exit(1);
        }
        $this->delPid();
    }

    /**
     * 检测进程id是否存在
     *
     * @return boolean
     */
    public function checkPid(): bool
    {
        $pid = $this->getPid();
        if (!$pid) {
            return false;
        }
        return file_exists('/proc/' . $pid);
    }

    /**
     * 获取进程号
     *
     * @return integer
     */
    public function getPid(): int
    {
        if (!is_file($this->_pidFile) || !file_exists($this->_pidFile)) {
            return 0;
        }
        return (int) file_get_contents($this->_pidFile);
    }

    /**
     * 退出进程&删除进程文件标识
     *
     * @return void
     */
    public function delPid(): void
    {
        $pid = $this->getPid();
        if ($pid) {
            posix_kill($pid, SIGINT);
        }

        if (is_file($this->_pidFile)) {
            @unlink($this->_pidFile);
        }
    }

    /**
     * 分发调用信号处理器
     *
     * @return void
     */
    public function dispatch(): void
    {
        $this->log("do dispatch");
        pcntl_signal_dispatch();
    }

    public function signal(): void
    {
        $this->log("get signal and exit");
    }
    /**
     * 解析参数
     * 
     * @param string $params 参数数据
     * @return void
     */
    public function _parseArgument(string $params): bool
    {
        if (preg_match("/^-([a-zA-Z][a-zA-Z0-9_]*)(=([^=]+))?$/", $params, $out)) {
            $this->_argument[$out[1]] = $out[3] ?? true;
            return true;
        }
        return false;
    }

    public function log(string $msg = ''): void
    {
        $msg = date('Y-m-d H:i:s') . ":{$msg}\r\n";
        file_put_contents($this->logFile, $msg, FILE_APPEND);
    }
}
new Daemon();
