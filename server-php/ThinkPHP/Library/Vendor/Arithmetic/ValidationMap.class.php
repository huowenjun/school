<?php
/**
 * 验证坐标点是否在某区域内
 * @author xiaoliang <1058436713@qq.com>
 * Class validationMap
 */
class ValidationMap{
    private static $coordArray;
    private static $vertx = [];
    private static $verty = [];
    /**
     * 设置坐标区域
     * @param mixed $coordArray
     */
    public static function setCoordArray(array $coordArray)
    {
        self::$coordArray = $coordArray;
    }
    /**
     * 验证区域范围
     * @param array $coordArray
     * @return bool
     */
    public static function isCityCenter(array $pointArray){
        if(!self::vaildatePoint($pointArray)){
            return false;
        }
        return self::pnpoly(count(self::$coordArray), $pointArray['lng'], $pointArray['lat']);
    }
    /**
     * 比较区域坐标
     * @param $nvert
     * @param $testx
     * @param $testy
     * @return bool
     */
    private static function pnpoly($nvert,$testx, $testy)
    {
        $c = false;
        for ($i = 0, $j = $nvert-1; $i < $nvert; $j = $i++) {
            if ( ( (self::$verty[$i]>$testy) != (self::$verty[$j]>$testy) ) && ($testx < (self::$vertx[$j]-self::$vertx[$i]) * ($testy-self::$verty[$i]) / (self::$verty[$j]-self::$verty[$i]) + self::$vertx[$i]) )
                $c = !$c;
        }
        return $c;
    }
    /**
     * 验证坐标
     * @param array $pointArray
     * @return bool
     */
    private static function vaildatePoint(array $pointArray){
        $maxY = $maxX = 0;
        $minY = $minX = 9999;
        self::$vertx = [];
        self::$verty = [];
        foreach (self::$coordArray as $item){
            if($item['lng']>$maxX) $maxX = $item['lng'];
            if($item['lng'] < $minX) $minX = $item['lng'];
            if($item['lat']>$maxY) $maxY = $item['lat'];
            if($item['lat'] < $minY) $minY = $item['lat'];
            self::$vertx[] = $item['lng'];
            self::$verty[] = $item['lat'];
        }
        if ($pointArray['lng'] < $minX || $pointArray['lng'] > $maxX || $pointArray['lat'] < $minY || $pointArray['lat'] > $maxY) {
            return false;
        }
        return true;
    }
}
/**************************** test *************************************/
//116.29269,39.841515,116.292843,39.840646,116.293575,39.840864,116.293341,39.841622
//$map = [  //上海
//    ["lng" => 116.29269, "lat" => 39.841515],
//    ["lng" => 116.292843, "lat" => 39.840646],
//    ["lng" => 116.293575, "lat" => 39.840864],
//    ["lng" => 116.293341, "lat" => 39.841622],
//];
//$array = ["lng"=>116.29270,"lat"=>39.841515];//进行验证的区域
//validationMap::setCoordArray($map);
//var_dump(validationMap::isCityCenter($array));