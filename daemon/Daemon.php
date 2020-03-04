<?php
/*
 * @Author: xsm
 * @Date: 2020-03-02 10:29:13
 * @LastEditTime: 2020-03-04 19:12:15
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
    private $_total = 0;

    private $_argument = [];
    /**
     * 可创建的最大子进程数
     *
     * @var integer
     */
    public $_maxChild = 5;

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
        $this->_maxChild = $this->_argument['max'] ?? 1;
        $pid = pcntl_fork();
        if ($pid == -1) {
            echo "无法创建子进程";
            exit();
        } elseif ($pid) {
            exit(0);
        }

        $this->run();
    }

    function run()
    {
        $str = "controller,action";
        $p =  posix_getpid();
        echo "创建父进程-》id：{$p}" . PHP_EOL;
        file_put_contents('run.pid', $p, LOCK_EX);
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
                $this->app($str);
            }
        }
    }

    function app($str)
    {
        $id = posix_getpid();
        for ($i = 0; $i < 10; $i++) {
            echo $str . "-子进程:{$id}-{$i}" . PHP_EOL;
            sleep(2);
        }
        echo "子进程执行10次后推出:{$id}-{$i}" . PHP_EOL;
        exit(0);
    }

    public function checkPid()
    {
        $pid = file_get_contents('run.pid');
        return file_exists('/proc/' . $pid);
    }

    /**
     * 解析参数
     * 
     * @param array $param 数据
     * @return void
     */
    public function _parseArgument($param)
    {

        if (preg_match("/^-([a-zA-Z][a-zA-Z0-9_]*)(=([^=]+))?$/", $param, $out)) {
            $this->_argument[$out[1]] = isset($out[3]) ? $out[3] : true;
            return true;
        }
    }
}
new Daemon();
// print_r($_SERVER);
