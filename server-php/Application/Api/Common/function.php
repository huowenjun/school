<?php

	function createApiKey(){
		$ret = $Model->query("Select UUID() as UUID");
		$key=$$ret['UUID'];
        $qian=array("-");
        $hou=array("");
        return str_replace($qian,$hou,$key);
	}

	//计算年龄
	function get_birthday($birthday){
        //  $age = strtotime($birthday);
        //  if ($age === false) {
        //      return false;
        //  }
        //  list($y1,$m1,$d1) = explode('-', date("Y-m-d",$age));
        //  $now = strtotime('now');
        //  list($y2,$m2,$d2) = explode('-', date("Y-m-d",$age));
        //  $age = $y2-$y1;
        //  if (int($m2.$d2) < int($m1.$d1)) {
        //  	$age -=1;
        //  }
        //  return $age;
        $age = date('Y', time()) - date('Y', strtotime($birthday)) - 1;  
		if (date('m', time()) == date('m', strtotime($birthday))){  
		  
		    if (date('d', time()) > date('d', strtotime($birthday))){  
		    $age++;  
		    }  
		}elseif (date('m', time()) > date('m', strtotime($birthday))){  
		    $age++;  
		}  
		return $age;

    }