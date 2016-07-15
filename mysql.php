<?php
include_once ("mysql_query.php");
include_once ("array_iconv.php");
include_once ("arraytoxml_function.php");
$action = $_POST ['op'];
if ($action == "insert") {
	$sql = stripslashes ( $_POST ['sql'] );
	$return = mysql_query ( $sql );
	print_r ( $return );
} elseif ($action == "select") {
	$key = $_POST ['key'];
	if ($key == "count") {
		$sql = stripslashes ( $_POST ['sql'] );
		$return = MysqlQueryAny ( $sql, "count" );
		print_r ( $return );
	} elseif ($key == "list") {
		$start = $_REQUEST ['start'];
		$len = $_REQUEST ['len'];
		if ($start == "") {
			$sql = stripslashes ( $_POST ['sql'] );
		} else {
			$sql = $sql = stripslashes ( $_POST ['sql'] ) . " limit $start,$len";
		}
		$return = MysqlQueryAny ( $sql, "query" );
		array_iconv ( $return, "gbk", "utf-8" );
		$return = arrtoxml ( $return );
		print_r ( $return );
	} elseif ($key == "one") {
		$sql1 = stripslashes ( $_POST ['sql1'] );
		$sql2 = stripslashes ( $_POST ['sql2'] );
		$return["return"] = MysqlQueryAny ( $sql1, "query" );
		$return["verion"] = MysqlQueryAny ( $sql2, "query" );
		array_iconv ( $return, "gbk", "utf-8" );
		$return = arrtoxml ( $return );
		print_r ( $return );
	}
} elseif ($action == "update") {
	$fid = $_POST ["fid"];
	$spver = $_POST ["spver"];
	$forname = $_POST ["forname"];
 	$query = "update sp_for set vid='".$spver."',for_name='".$forname."' where fid = '".$fid."'";
   	$ret=mysql_query($query);
	echo $ret;
} elseif ($action == "delete") {
	$sql = stripslashes ( $_POST ['sql'] );
	$ret = mysql_query ( $sql );
	print_r ( $ret );
}elseif ($action=="addinfo"){
	$sql1=stripslashes($_POST["sql1"]);
	$sql2=stripslashes($_POST["sql2"]);
	$sql3=stripslashes($_POST["sql3"]);
	$sql4=stripslashes($_POST["sql4"]);
	$return["type"]=MysqlQueryAny($sql1, "query");
	$return["verion"]=MysqlQueryAny($sql2, "query");
	$return["type_info"]=MysqlQueryAny($sql3, "query");
	$return["rel_obj"]=MysqlQueryAny($sql4, "query");
	array_iconv ( $return, "gbk", "utf-8" );
	$return = arrtoxml ( $return );
	print_r ( $return );
}elseif($action=="addobj"){
	$sql=stripslashes($_POST["sql"]);
	$temp=xml2array($sql);
	if(is_array($temp["root"]["item"])&&count($temp["root"]["item"])>1){
		$sql1=$temp["root"]["item"];
		array_iconv($sql1, "utf-8", "gbk");
	}else{
		$sql1[]=iconv("utf-8", "gbk", $temp["root"]["item"]);
	}
	$is_vip=$_POST["is_vip"];
	if(is_array($sql1)&&count($sql1)>0){
		for ($i=0;$i<count($sql1);$i++){
			$sql2=$sql1[$i];
			$num=MysqlQueryAny($sql2, "count");
			$return=MysqlQueryAny($sql2, "query");
			if($return=="null"||count($return)<=0||$num==0){
				continue;
			}
			if($return == "error"){
				echo "数据库异常错误，请重试";
			}
			if($num==1){
				$query="insert into rel_obj(a_id,is_vip,updatetime) values('".$return[0][0]["a_id"]."', '".$is_vip."',".time().")";
				if(!mysql_query($query)){
					$ret="添加失败，请重试";
				}

			}else{
				for($j=0;$j<count($return);$j++){
					$query="insert into rel_obj(a_id,is_vip,updatetime) values('".$return[$j]["a_id"]."', '".$is_vip."',".time().")";
					if(!mysql_query($query)){
						$ret="添加失败，请重试";
					}
				}
			}
		}
	}
	//return $query;
	if(isset($ret)){
		$ret=iconv("gbk", "utf-8", $ret);
		echo $ret;
	}
}elseif($action=="addmember"){
	$sql = stripslashes ( $_POST ['sql'] );
	$return=MysqlQueryAny($sql, "query");
	array_iconv ( $return, "gbk", "utf-8" );
	$return = arrtoxml ( $return );
	print_r ( $return );
}elseif ($action == "insertlogin") {
	$sql = stripslashes ( $_POST ['sql'] );
	mysql_query ( $sql );
	$return = mysql_insert_id();
	print_r ( $return );
}elseif($action == "selectone"){
		$sql = stripslashes ( $_POST ['sql'] );
		$return = MysqlQueryAny ( $sql, "query" );
		array_iconv ( $return, "gbk", "utf-8" );
		//print_r($return);exit;
		$return = arrtoxml ( $return );
		print_r ( $return );
}elseif($action == "approveone"){
	$sql = stripslashes ( $_POST ['sql'] );
	$return = mysql_query ( $sql );
	
	print_r ( $return );
}elseif($action == "approves"){
	$sql = stripslashes ( $_POST ['sql'] );
	$return = mysql_query ( $sql );
	$lid = $_REQUEST["id"];
	$sql = "SELECT vname,r_type
       	    FROM sp_for, sp_info, sp_type, verion, address,login_msg
            WHERE sp_type.sp_tid = sp_info.sp_type
            AND verion.vid = sp_for.vid
            AND sp_info.sp_id = sp_for.sp_id
            AND sp_for.fid = address.fid and address.a_id = login_msg.aid and lid>=".$lid;
	$return=MysqlQueryAny($sql, "query");
	array_iconv ( $return, "gbk", "utf-8" );
	$return = arrtoxml ( $return );
	print_r ( $return );
}



?>