<?php
/*
 * @Author: xsm
 * @Date: 2020-03-02 10:29:13
 * @LastEditTime: 2020-03-10 19:32:31
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
    private string $_pidFile = 'run.pid';

    /**
     * 命令行参数
     *
     * @var array
     */
    private array $_argument = [];


    /**
     * 动作  stop/start  restart
     */
    private string $action;

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
        if ('stop' == $this->_argument['a']) {
            $this->stop();
        } elseif ('start' == $this->_argument['a']) {
            $this->start();
        }
    }


    public function start()
    {
        $this->_maxChild = $this->_argument['max'] ?? 1;
        $pid = pcntl_fork();
        if ($pid == -1) {
            echo "无法创建子进程";
            exit();
        } elseif ($pid) {
            exit(0);
        }
        pcntl_signal(SIGINT, array($this, "signal"));
        $this->run();
    }

    function run()
    {
        $str = "初始化的程序和参数";
        $p =  posix_getpid();
        echo "父进程的->id：{$p}" . PHP_EOL;
        file_put_contents($this->_pidFile, $p, LOCK_EX);
        while ($this->checkPid()) {
            $pid = pcntl_fork();
            if ($pid > 0) {
                $this->_total++;
                if ($this->_total < $this->_maxChild) {
                    continue;
                }
                $pid = pcntl_wait($status);
                echo "监听子进程：{$pid}，退出" . PHP_EOL;
                $this->_total--;
            }
            if (-1 == $pid) {
                echo "无法创建子进程" . PHP_EOL;
                exit(1);
            }
            if (!$pid) {
                $this->doSomething($str);
            }
        }
    }

    function doSomething($str)
    {
        $id = posix_getpid();
        for ($i = 0; $i < 10; $i++) {
            echo $str . "-子进程:{$id}-{$i}" . PHP_EOL;
            sleep(2);
        }
        echo "子进程执行10次后退出:{$id}-{$i}" . PHP_EOL;
        exit(0);
    }

    public function stop()
    {
        if (!$this->checkPid()) {
            exit(1);
        }
        $this->delPid();
    }

    public function checkPid(): bool
    {
        $pid = $this->getPid();
        if (!$pid) {
            return false;
        }
        return file_exists('/proc/' . $pid);
    }

    public function getPid(): int
    {
        if (!is_file($this->_pidFile)) {
            return 0;
        }
        return (int) file_get_contents($this->_pidFile);
    }

    public function delPid()
    {
        $pid = $this->getPid();
        if ($pid) {
            posix_kill($pid, SIGINT);
        }

        if (is_file($this->_pidFile)) {
            @unlink($this->_pidFile);
        }
    }

    public function signal()
    {
        file_put_contents('daemon.txt',"get signal and exit");
    }
    /**
     * 解析参数
     * 
     * @param string $param 参数数据
     * @return void
     */
    public function _parseArgument(string $param): bool
    {
        if (preg_match("/^-([a-zA-Z][a-zA-Z0-9_]*)(=([^=]+))?$/", $param, $out)) {
            $this->_argument[$out[1]] = isset($out[3]) ? $out[3] : true;
            return true;
        }
        return false;
    }
}
new Daemon();
