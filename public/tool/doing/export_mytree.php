<?php

require "/var/www/html/public/index.php";

$pid = request('pid');
if(!$pid)
{
    die("NOT PID!");
}

if(!$pObj = \App\Models\GiaPha::find($pid)){
    die("Not found data!");
}

$uid = getCurrentUserId();

if($pid != '11461493758623744')
if($pObj->user_id != $uid){
    die("NOT YOUR DATA!");
}

$objPr = new \App\Components\clsParamRequestEx();
$objPr->set_gid = 3;
$objPr->need_set_uid = $uid;
//    $objPr->need_set_uid = null;
$objPr->module = 'member';
$objPr->ignore_check_userid = 1;
$mretAll = [];
\App\Models\GiaPha_Meta::getTreeDeep($pid, $objPr, $mretAll, 0);
$mretAll[0]['parent_id'] = 0;
$ver = 1;

if($usObj = \App\Models\GiaPhaUser::where("user_id", $uid)->first()){
    $ver = $usObj->version_using;
    if($ver == 2){
        if($tree = \App\Models\MyTreeInfo::where("tree_id", $pid)->first()){

            if($tree->tree_nodes_xy){

                if($mPost = json_decode($tree->tree_nodes_xy)){
//                    echo "<br/>\n $tree->tree_nodes_xy";
//                    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                    print_r($mPost);
//                    echo "</pre>";
                    foreach ($mPost AS $one){

//                        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                        print_r($one);
//                        echo "</pre>";
                        if($one->x ?? '')
                        foreach ($mretAll AS &$one1) {
//                            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                            print_r($one1);
//                            echo "</pre>";
                            if ($one1['id'] == $one->id) {
                                $one1['orders'] = intval($one->x);
                            }
                        }
                    }
                }
            }
        }
    }
}

if($ver == 2){
//Sắp xếp lại maảng theo trường orders
    usort($mretAll, function($a, $b) {
        return $a['orders'] > $b['orders'];
    });
}
else
    usort($mretAll, function($a, $b) {
        return $a['orders'] < $b['orders'];
    });


echo json_encode($mretAll, JSON_PRETTY_PRINT);
