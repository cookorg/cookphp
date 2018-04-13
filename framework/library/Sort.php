<?php

namespace library;

/**
 * 数组排序
 * @author CookPHP <admin@cookphp.org>
 */
class Sort {

    /**
     * 升序
     * @param array $preData
     * @param string $sortType
     * @return array
     */
    public static function arrayAsc(array $preData, string $sortType = 'sort'): array {
        $sortData = [];
        foreach ($preData as $key_i => $value_i) {
            $price_i = $value_i[$sortType];
            $min_key = '';
            $sort_total = count($sortData);
            foreach ($sortData as $key_j => $value_j) {
                if ($price_i < $value_j[$sortType]) {
                    $min_key = $key_j + 1;
                    break;
                }
            }
            if (empty($min_key)) {
                array_push($sortData, $value_i);
            } else {
                $sortData1 = array_slice($sortData, 0, $min_key - 1);
                array_push($sortData1, $value_i);
                if (($min_key - 1) < $sort_total) {
                    $sortData2 = array_slice($sortData, $min_key - 1);
                    foreach ($sortData2 as $value) {
                        array_push($sortData1, $value);
                    }
                }
                $sortData = $sortData1;
            }
        }
        return $sortData;
    }

    /**
     * 降序
     * @param array $preData
     * @param string $sortType
     * @return array
     */
    public static function arrayDesc(array $preData, string $sortType = 'sort'): array {
        $sortData = [];
        foreach ($preData as $key_i => $value_i) {
            $price_i = $value_i[$sortType];
            $min_key = '';
            $sort_total = count($sortData);
            foreach ($sortData as $key_j => $value_j) {
                if ($price_i > $value_j[$sortType]) {
                    $min_key = $key_j + 1;
                    break;
                }
            }
            if (empty($min_key)) {
                array_push($sortData, $value_i);
            } else {
                $sortData1 = array_slice($sortData, 0, $min_key - 1);
                array_push($sortData1, $value_i);
                if (($min_key - 1) < $sort_total) {
                    $sortData2 = array_slice($sortData, $min_key - 1);
                    foreach ($sortData2 as $value) {
                        array_push($sortData1, $value);
                    }
                }
                $sortData = $sortData1;
            }
        }
        return $sortData;
    }

}
