<?php
/*
 * @Author: xsm
 * @Date: 2020-03-02 10:29:13
 * @LastEditTime: 2020-03-04 17:32:46
 * @Description: 位运算实现签到核心代码
 */

namespace xiaoshangmin;

class SignIn
{

    /**
     * 某天是否签到
     *
     * @param integer $day 签到的日期（0-30）
     * @param integer $num 签到统计的总数值
     * @return boolean
     */
    public static function isSignIn(int $num, int $day): bool
    {
        return (($num >> $day) & 1) == 1;
    }

    /**
     * 签到
     *
     * @param integer $day 签到的日期（0-30）
     * @param integer $num 签到统计的总数值
     * @return int
     */
    public static function signIn(int $num, int $day): int
    {
        $num |= (1 << $day);
        return $num;
    }

    /**
     * 当月连续签到天数
     *
     * @param integer $num 签到统计的总数值
     * @param integer $endDay 已签到的最后日期，默认30即31号
     * @return int
     */
    public static function continuous(int $num, int $endDay = 30): int
    {
        $count = 0;
        for ($day = $endDay; $day >= 0; $day--) {
            if ((($num >> $day) & 1) == 1) {
                $count += 1;
                continue;
            }
            break;
        }
        return $count;
    }

    /**
     * 当月未签到的总天数
     *
     * @param integer $num 签到统计的总数值
     * @param integer $endDay 已签到的最后日期，默认30即31号
     * @return int
     */
    public static function break(int $num, int $endDay = 30): int
    {
        $count = 0;
        for ($day = $endDay; $day >= 0; $day--) {
            if ((($num >> $day) & 1) == 0) {
                $count += 1;
            }
        }
        return $count;
    }

    /**
     * 当月未签到的日期
     *
     * @param integer $num
     * @param integer $endDay
     * @return array
     */
    public static function breakDays(int $num, int $endDay = 30): array
    {
        $days = [];
        for ($day = $endDay; $day >= 0; $day--) {
            if ((($num >> $day) & 1) == 0) {
                $days[] = $day;
            }
        }
        return $days;
    }
}

