<?php

/**
 * 公共函数
 */

//ajax返回
function ajaxReturn(array $arr)
{
    echo str_replace("\\/", "/", json_encode($arr));
    exit;
}

/**
 * 求解一个值是否为质数
 *
 * @param $a
 * @return int 0是 1不是
 */
function isPrime($a)
{
    $n = 0;
    if ($n > 0 && $n < 2) {
        $n = 1;
    } else {
        $max = $a / 2;
        for ($i = 2; $i <= $max; $i++) {
            if ($a % $i == 0) {
                $n++;
                break;
            }
        }
    }
    return $n;
}

/**
 * $array = the array to be filtered
 * $total = the maximum number of items to return
 * $unique = whether or not to remove duplicates before getting a random list
 */
function unique_array($array, $total, $unique = true)
{
    $newArray = array();
    if ((bool)$unique) {
        $array = array_unique($array);
    }
    shuffle($array);
    $length = count($array);
    for ($i = 0; $i < $total; $i++) {
        if ($i < $length) {
            $newArray[] = $array[$i];
        }
    }
    return $newArray;
}

