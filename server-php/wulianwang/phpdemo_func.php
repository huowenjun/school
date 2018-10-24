<?php
// XML格式转数组格式
function xml_to_array( $xml ) { 
    $reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/"; 
    if(preg_match_all($reg, $xml, $matches)) { 
        $count = count($matches[0]); 
        for($i = 0; $i < $count; $i++) { 
            $subxml= $matches[2][$i]; 
            $key = $matches[1][$i]; 
            if(preg_match( $reg, $subxml )) { 
                $arr[$key] = xml_to_array( $subxml ); 
            } else {
                $arr[$key] = $subxml; 
            } 
        } 
    } 
    return $arr; 
} 

// 页面显示数组格式，用于调试
function echo_xmlarr($res) {
    $res = xml_to_array($res);
    echo "<pre>";
    print_r($res);
    echo "</pre>";
}

 

    function sendMsg($param) {
     return '<?xml version="1.0" encoding="UTF-8"?>
                <root>
                    <spID>'.$param['spID'].'</spID> 
                    <password>'.$param['password'].'</password>
                    <accessCode>'.$param['accessCode'].'</accessCode>
                    <content>'  .$param['content']  .'</content>
                    <mobileString>' .$param['mobileString'] .'</mobileString>
                </root>';
//      echo $this->arg0;
    }

 
 

// 构建"获得上行短信"参数结构体
class queryMo {
    function queryMo($param) {
        $this->arg0='<?xml version="1.0" encoding="UTF-8"?>
                <root>
                    <username>'.$param['username'].'</username>	
                    <password>'.$param['password'].'</password>
                    <veryCode>'.$param['veryCode'].'</veryCode>
                </root>';
        
    }
};
 


?>