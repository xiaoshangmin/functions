## 通过位运算计算每月的签到数据


### 使用
```php
    //日期从0开始计算（0-30）
    $days = [0, 1, 2, 3, 4, 5, 6, 7,  8, 9, 10, ];
    $num = 0;

    //1-11号都签到
    foreach ($days as $key => $day) {
        $num = SignIn::signIn($num, $day);
    }

    //11号是否签到
    $flag = SignIn::isSignIn($num, 10);
    var_dump($flag);

    //截至到11号 连续签到的天数
    $count = SignIn::continuous($num, 10);
    echo $count;

    //截至到11号 未签到的总天数
    $count = SignIn::break($num, 10);
    echo $count;

    //截至到11号 未签到的日期数组
    $days = SignIn::breakDays($num, 10);
    print_r($days);
```
