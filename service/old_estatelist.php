<?php include "./header.php"; 
      include './system/db_info.php';  //DB接続のため dbConnect関数呼び出し
?>

<?php 
    $dbh = dbConnect();  //db接続
?>

<!-- 物件写真の表示・非表示に関与する、elementUI提供CSS -->
<link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/display.css">

<!-- パラメータを取得し、クエリに変換する -->
<?php
    $srt=urldecode((empty($_SERVER["HTTPS"]) ? "http://" : "https://") . 
    $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    $url=parse_url($srt);
    if(array_key_exists('query',$url)){
        parse_str($url['query'], $parms);
    }

    $linkToDetail=substr($srt,0,strpos($srt,'estatelist'));
    $whereQuery = ''; //クエリ文を格納する変数
    $aoo = ''; //クエリ文の内、「OR」「AND」を格納する変数
    $puQueried=0; //「ピックアップ一覧」を利用して開かれたかどうかを判定する変数
    $isPurchase = 0; //このページが「購入」か「賃貸」かを判定する変数
    $transportTimeQueried = 100; //駅からの徒歩時間が検索された場合、それを格納するための変数
    
    // サイドクエリデフォルト値
    $sideTransport = '';
    $sidePriceLow = '';
    $sidePriceHigh= '';
    $sideFloorPlan = '';
    $sideLandLow = '';
    $sideLandHigh = '';
    $sideBuildingLow='';
    $sideBuildingHigh = '';
    $sideTransport = '';
    $sideFree = '';

    // このページがピックアップ一覧として開かれているかどうかを判別
    if(array_key_exists('piup',$parms)){
        $whereQuery = 'pickup_flg = 1';
        $puQueried = 1;
    }

    // サイドクエリから遷移するURLを生成するための大本を構築(http://…/estatelist.php?puse=0 or 1 || http://…/estatelist.php?piup=1)
    if($puQueried == 0){
        $parmGen=substr($srt,0,strpos($srt,$parms['puse']) + 1);
    }else{
        $parmGen=substr($srt,0,strpos($srt,$parms['piup']) + 1);
    }

    // ピックアップ一覧である場合はpuseパラメータが存在しないためエラーが発生する。それを回避するためピックアップでないことを確認
    if($puQueried == 0){
        if($parms['puse'] == 1){
            $isPurchase = 1;
        }else{
            $isPurchase = 0;
        }
    }

    // NULL値を0に変換するための関数
    function nullZero($value){
        if(is_null($value)){
            return 0;
        }else{
            return $value;
        }
    }

    // 配列として渡されているkbn系のデータを、クエリ文に利用できるよう整形した上でクエリ文に挿入する関数
    function kbnQuerior($whereQuery,$kbn,$value,$max,$aoo){
        $check_checker = false;
        for($i = 0;$i <= $max;$i++){

            $pos = strpos($value,strval($i));
            if($check_checker){
                $aoo = ' OR ';
            }
            if ($pos !== false && !$check_checker){
                $check_checker = true;
                $whereQuery .= $aoo.'('.$kbn.' = '.$i;
            }else if($pos !== false){
                $whereQuery .= $aoo.$kbn.' = '.$i;
            }
        }
        $whereQuery .= ')';

        return $whereQuery;
    }

    // クエリ文が空でない場合にANDを加える関数
    function addAnd($whereQuery){
        if($whereQuery == ''){
            return '';
        }else{
            return ' AND '.$whereQuery;
        }
    }

    
    function flPtnChecker($flNum){
        if($flNum == null){
            return '';
        }else{
            return $flNum;
        }
    }

    function cityExchanger($city_kbn){
        switch($city_kbn){
            case 0:
                return '船橋';
            break;

            case 1:
                return '鎌ヶ谷';
            break;

            case 2:
                return '市川';
            break;

            case 3:
                return '白井';
            break;

            case 4:
                return '松戸';
            break;
        }
    }

    function flkbnExchanger($floor_plan_kbn){
        switch($floor_plan_kbn){
            case 0:
                return '土地';
            break;

            case 1:
                return 'R';
            break;

            case 2:
                return 'K';
            break;

            case 3:
                return 'DK';
            break;

            case 4:
                return 'LDK';
            break;
        }
    }

    function eskbnExchanger($estate_kbn){
        switch($estate_kbn){
            case 0:
                return '土地';
            break;
            case 1:
                return '事務所';
            break;
            case 2:
                return 'マンション';
            break;
            case 3:
                return '中古戸建';
            break;
            case 4:
                return '新築戸建';
            break;
        }
    }

    function tagMaker($estkbn){
        switch($estkbn){
            case 0:
                return 'succes';
            break;
            case 1:
                return 'info';
            break;
            case 2:
                return 'warning';
            break;
            case 3:
                return 'default';
            break;
            case 4:
                return 'danger';
            break;

        }
    }

function queryMaker($aoo,$whereQuery,$parms){
    foreach($parms as $key => $value){

        switch($key){
            case 'transport_time':
                if(strpos($value,'分')){
                    $value = substr($value,0,strpos($value,'分'));
                }
                $GLOBALS['sideTransport'] = $value.'分以内';
                $GLOBALS['transportTimeQueried'] = $value;
            break;

            case 'puse':
                $whereQuery .= $aoo.'purchase_flg = '.$value;
                $aoo = ' AND ';
            break;

            case 'este':
                $whereQuery = kbnQuerior($whereQuery,'estate_kbn',$value,4,$aoo);
                $aoo = ' AND ';
            break;

            case 'city':                
                $whereQuery = kbnQuerior($whereQuery,'city_kbn',$value,4,$aoo);
                $aoo = ' AND ';
            break;

            case 'estate_priceLow':
                if(strpos($value,'万')){
                    $value = substr($value,0,strpos($value,'万'));
                }
                $GLOBALS['sidePriceLow'] = $value.'万円';
                $whereQuery .= $aoo.'estate_price >= '.$value;
                $aoo = ' AND ';
            break;

            case 'estate_priceHigh':
                if(strpos($value,'万')){
                    $value = substr($value,0,strpos($value,'万'));
                }elseif(strpos($value,'億')){
                    $value = substr($value,0,strpos($value,'億'));
                    $value *= 10000;
                }
                if($value < 10000){ $GLOBALS['sidePriceHigh'] = $value.'万円'; }
                else{$GLOBALS['sidePriceHigh'] = ($value / 10000).'億円';}
                $whereQuery .= $aoo.'estate_price <= '.$value;
                $aoo = ' AND ';
            break;

            case 'floor_plan': 

                $a_flg = false;
                $b_flg = false;
                $c_flg = false;
                $d_flg = false;
                $e_flg = false;
                $f_flg = false;

                if(false !== strpos($value,'A')){
                    $GLOBALS['sideFloorPlan'] .= "'A',";
                    $value = str_replace('A','1,1',$value);
                    $a_flg = true;
                }
                if(false !== strpos($value,'B')){
                    $GLOBALS['sideFloorPlan'] .= "'B',";
                    $value = str_replace('B','1,5',$value);
                    $b_flg = true;
                }
                if(false !== strpos($value,'C')){
                    $GLOBALS['sideFloorPlan'] .= "'C',";
                    $value = str_replace('C','2,5',$value);
                    $c_flg = true;
                }
                if(false !== strpos($value,'D')){
                    $GLOBALS['sideFloorPlan'] .= "'D',";
                    $value = str_replace('D','3,5',$value);
                    $d_flg = true;
                }
                if(false !== strpos($value,'E')){
                    $GLOBALS['sideFloorPlan'] .= "'E',";
                    $value = str_replace('E','4,5',$value);
                    $e_flg = true;
                }
                if(false !== strpos($value,'F')){
                    $GLOBALS['sideFloorPlan'] .= "'F',";
                    $value = str_replace('F','5,5',$value);
                    $f_flg = true;
                }
                // floor_planは1,1,2,3のような形でわたされる
                $num=explode(',',$value);
                for($i = 0;$i < count($num);$i++){
                    $enzansi = ' = ';

                    if(($i + 1) % 2 != 0){
                    //floor_plan_numをつかさどるパラメタ
                    switch($num[$i]){
                        case 1:
                            if(!$a_flg && $num[$i + 1] == 1){
                                $GLOBALS['sideFloorPlan'] .= "'A',";
                                $a_flg = true;
                            }else if(!$b_flg){
                                $GLOBALS['sideFloorPlan'] .= "'B',";
                                $b_flg = true;
                            }
                            break;
                        case 2:
                            if(!$c_flg){
                                $GLOBALS['sideFloorPlan'] .= "'C',";
                                $c_flg = true;
                            }
                            break;
                        case 3:
                            if(!$d_flg){
                                $GLOBALS['sideFloorPlan'] .= "'D',";
                                $d_flg = true;
                            }
                            break;
                        case 4:
                            if(!$e_flg){
                                $GLOBALS['sideFloorPlan'] .= "'E',";
                                $e_flg = true;
                            }
                            break;
                        case 5:
                            if(!$f_flg){
                                $GLOBALS['sideFloorPlan'] .= "'F',";
                                $f_flg = true;
                            }
                            break;
                        default:                            
                            break;                       
                        }
                        if($num[$i] == 5){
                            $enzansi = ' >= ';
                        }
                        if($i == 0 && count($num) > 2){
                            $whereQuery.=$aoo.'(('.'floor_plan_num'.$enzansi.$num[$i].' AND ';
                            $aoo = ' OR ';
                        }else{
                            $whereQuery.=$aoo.'('.'floor_plan_num'.$enzansi.$num[$i].' AND '; 
                        }
                    }else{
                        //floor_plan_kbnをつかさどるパラメタ
                        if($num[$i] == 5){
                            $whereQuery.='floor_plan_kbn >= 1)';
                        }else{
                            $whereQuery.='floor_plan_kbn = '.$num[$i].')';
                        }
                    }
                }
                $GLOBALS['sideFloorPlan'] = substr($GLOBALS['sideFloorPlan'],0,strlen($GLOBALS['sideFloorPlan']) - 1);
                if(count($num) > 2){
                    $whereQuery.=')';
                }
                $aoo = ' AND ';
            break;

            case 'land_areaLow':
                if(strpos($value,'㎡')){
                    $value = substr($value,0,strpos($value,'㎡'));
                }
                $GLOBALS['sideLandLow'] = $value.'㎡';
                $whereQuery .= $aoo.'land_area >= '.$value;
                $aoo = ' AND ';
            break;

            case 'land_areaHigh':
                if(strpos($value,'㎡')){
                    $value = substr($value,0,strpos($value,'㎡'));
                }
                $GLOBALS['sideLandHigh'] = $value.'㎡';
                $whereQuery .= $aoo.'land_area <= '.$value;
                $aoo = ' AND ';
            break;

            case 'building_areaLow':
                if(strpos($value,'㎡')){
                    $value = substr($value,0,strpos($value,'㎡'));
                }
                $GLOBALS['sideBuildingLow'] = $value.'㎡';
                $whereQuery .= $aoo.'building_area >= '.$value;
                $aoo = ' AND ';
            break;

            case 'building_areaHigh':
                if(strpos($value,'㎡')){
                    $value = substr($value,0,strpos($value,'㎡'));
                }
                $GLOBALS['sideBuildingHigh'] = $value.'㎡';
                $whereQuery .= $aoo.'building_area <= '.$value;
                $aoo = ' AND ';
            break;

            case 'age':
                if($value !== 1){
                    $whereQuery .= $aoo.'age <= '.$value;
                    $aoo = ' AND ';
                }
            break;

            case 'feed':
                $whereQuery .= $aoo.'fee_included_flg = 1';
                $aoo = ' AND ';
            break;

            case 'management_fee':
                $whereQuery .= $aoo.'management_fee = 0';
                $aoo = ' AND ';
            break;            

            case 'condo_fee':
                $whereQuery .= $aoo.'condo_fee = 0';
                $aoo = ' AND ';
            break;

            case 'pang':
                $whereQuery .=$aoo.'parking_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';
            break;

            case 'bile':
                $whereQuery .=$aoo.'bicycle_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';
            break;

            case 'pang':
                $whereQuery .=$aoo.'parking_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';
            break;

            case 'baed':
                $whereQuery .=$aoo.'bath_separated_kbn = 2';
                // $orDivision = 1;
                $aoo = ' AND ';
            break;

            case 'auck':
                $whereQuery .=$aoo.'autolock_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';
            break;

            case 'pet':
                $whereQuery .=$aoo.'pet_kbn >= 1';
                $aoo = ' AND ';
            break;

            case 'fole':
                $whereQuery .=$aoo.'forsale_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';
            break;

            case 'inee':
                $whereQuery .=$aoo.'internetfree_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';
            break;

            case 'denk':
                $whereQuery .=$aoo.'devidedsink_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';
            break;

            case 'inry':
                $whereQuery .=$aoo.'inlandry_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';
            break;

            case 'frnt':
                $whereQuery .=$aoo.'freelent_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';
            break;

            case 'reed':
                $whereQuery .=$aoo.'reformed_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';
            break;

            case 'reon':
                $whereQuery .=$aoo.'renovation_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';
            break;

            case 'apce':
                $whereQuery .=$aoo.'appliance_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';
            break;

            case 'apce':
                $whereQuery .=$aoo.'appliance_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';
            break;

            case 'guor':
                $whereQuery .=$aoo.'guarantor_flg = 0';
                // $orDivision = 1;
                $aoo = ' AND ';
            break;

            case 'shre':
                $whereQuery .=$aoo.'share_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';
            break;           

            case 'ders':
                $whereQuery .=$aoo.'designers_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';
            break; 

            case 'prht':
                $whereQuery .=$aoo.'pre_under_ht_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';
            break; 

            case 'floor_num_a':
                $whereQuery .=$aoo.'floor_num = 1';
                // $orDivision = 1;
                $aoo = ' AND ';
            break; 

            case 'floor_num_b':
                $whereQuery .=$aoo.'floor_num > 2';
                // $orDivision = 1;
                $aoo = ' AND ';
            break; 

            case 'floor_num_c':
                $whereQuery .=$aoo.'story_num = floor_num';
                // $orDivision = 1;
                $aoo = ' AND ';
            break; 

            case 'aion':
                $whereQuery .=$aoo.'aircon_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';
            break;            

            case 'flat':
                $whereQuery .=$aoo.'floorheat_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';               
            break;

            case 'mone':
                $whereQuery .=$aoo.'monitor_interphone_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';               
            break;

            case 'flng':
                $whereQuery .=$aoo.'flooring_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';               
            break;

            case 'elor':
                $whereQuery .=$aoo.'elevator_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';               
            break;

            case 'sera':
                $whereQuery .=$aoo.'security_camera_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';               
            break;

            case 'deox':
                $whereQuery .=$aoo.'deliver_box_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';               
            break;

            case 'coen':
                $whereQuery .=$aoo.'counter_kitchen_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';               
            break;           

            case 'elve':
                $whereQuery .=$aoo.'electric_stove_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';               
            break; 

            case 'muve':
                $whereQuery .=$aoo.'multi_stove_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';               
            break;      
            
            case 'syen':
                $whereQuery .=$aoo.'system_kitchen_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';               
            break;            

            case 'gale':
                $whereQuery .=$aoo.'gas_stove_available_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';               
            break;   

            case 'shet':
                $whereQuery .=$aoo.'shower_toilet_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';               
            break; 

            case 'reat':
                $whereQuery .=$aoo.'reheat_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';               
            break; 

            case 'bary':
                $whereQuery .=$aoo.'bathroom_dry_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';               
            break; 

            case 'sher':
                $whereQuery .=$aoo.'shampoo_dresser_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';               
            break; 

            case 'coom':
                $whereQuery .=$aoo.'corner_room_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';               
            break;             

            case 'roth':
                $whereQuery .=$aoo.'room_tosouth_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';               
            break;  

            case 'mate':
                $whereQuery .=$aoo.'maisonette_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';               
            break;  

            case 'loft':
                $whereQuery .=$aoo.'loft_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';               
            break; 

            case 'catv':
                $whereQuery .=$aoo.'catv_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';               
            break; 

            case 'csna':
                $whereQuery .=$aoo.'cs_antenna_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';               
            break; 

            case 'bsna':
                $whereQuery .=$aoo.'bs_antenna_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';               
            break; 

            case 'woly':
                $whereQuery .=$aoo.'women_only_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';               
            break; 

            case 'elok':
                $whereQuery .=$aoo.'elder_ok_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';               
            break; 

            case 'neke':
                $whereQuery .=$aoo.'newlyweds_like_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';               
            break; 

            case 'imle':
                $whereQuery .=$aoo.'immediate_movavle_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';               
            break; 

            case 'bree':
                $whereQuery .=$aoo.'brokerage_free_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';               
            break; 

            case 'maty':
                $whereQuery .=$aoo.'managed_property_flg = 1';
                // $orDivision = 1;
                $aoo = ' AND ';               
            break; 

            default:break;
        }
    }
    $result = [$whereQuery,$aoo];
    return $result;
}

if($puQueried == 0){
    if(array_key_exists('query',$url)){
        $madeQuery = queryMaker($aoo,$whereQuery,$parms);
        $whereQuery = $madeQuery[0];
        $aoo = $madeQuery[1];
        if(array_key_exists('freeword',$parms)){
            $GLOBALS['sideFree'] = $parms['freeword'];
            if($aoo == ' AND '){
                $aoo = ' AND ';
            }
            $freewords = explode(" ",$parms['freeword']);
            if(count($freewords) == 1){
                $whereQuery .=$aoo."( estate_name LIKE '%".$freewords[0]."%' OR description LIKE '%".$freewords[0]."%' OR address_detail LIKE '%".$freewords[0]."%')";
                $aoo = ' OR ';
            }else{
                $aoo = '';
                $whereQuery .= ' AND (';
                foreach($freewords as $freeword){
                    $whereQuery .=$aoo."( estate_name LIKE '%".$freeword."%' OR description LIKE '%".$freeword."%' OR address_detail LIKE '%".$freeword."%')";
                    $aoo = ' OR ';
                }
                $whereQuery.=')';
            }
         }
    }
}
    $query = "SELECT estate_id,t_estate_detail.* FROM t_estate_detail INNER JOIN t_estate USING(estate_id) WHERE t_estate.delete_flg = 0 AND t_estate.display_flg = 1".addAnd($whereQuery);
    $queryForCount = "SELECT count(*) FROM t_estate_detail INNER JOIN t_estate USING(estate_id) WHERE delete_flg = 0 AND t_estate.display_flg = 1".addAnd($whereQuery);
    $query .= " ORDER BY registered_datetime DESC";
    $stmt = $dbh->query($queryForCount);
    $count = $stmt->fetchColumn();
    if ($count > 0) {
        $stmt = $dbh->query($query);
        $estate_list = $stmt->fetchAll(PDO::FETCH_ASSOC|PDO::FETCH_UNIQUE);
    }else{
        $dbh = null;
    }
?>

<template v-if="!toBoolean(<?php echo $puQueried ?>)">
<el-button icon="el-icon-search" circle @click="dialogFormVisible = true" v-if="isSm" v-bind:style="searchButton"></el-button>
</template>
<el-dialog :visible.sync="dialogFormVisible" width="85%">
<el-row v-bind:style=changeSearchModal>
        <el-col :span="24" v-bind:style=title v-bind:style="{ marginBottom:'10px' }">
            <p v-bind:style="{fontSize:bigChar - 0.2 + 'rem'}">検索条件の</p>
            <h1 v-bind:style="{ fontSize:bigChar + 'rem' }">変更</h1>
        </el-col>
        <el-form :model="sideQuerySelected">
            <el-col :span="24" class="conditions-title" v-bind:style="[boxMarginTop,impact]">価格</el-col>
            <el-col :span="10">
                <el-form-item>
                    <el-select v-model="sideQuerySelected.estate_priceLow" clearable placeholder="下限なし" name="estate_priceLow" @change="parMaker()" clearable>
                        <template v-if="toBoolean(<?php echo $isPurchase?>)">
                            <template v-for="item in sideQuery.estatePricePuse">
                                <template v-if=isLower(item.value,sideQuerySelected.estate_priceHigh)>
                                <el-option
                                    :label="item.label"
                                    :value="item.value"
                                    >
                                </el-option>
                                </template>
                            </template>
                        </template>
                        <template v-else>
                            <template v-for="item in sideQuery.estatePriceNpuse">
                                <template v-if=isLower(item.value,sideQuerySelected.estate_priceHigh)>
                                    <el-option
                                        :label="item.label"
                                        :value="item.value"
                                        >
                                    </el-option>
                                </template>
                            </template>
                        </template>
                    </el-select>
                </el-form-item>
            </el-col>
            <el-col :span="4" v-bind:style="{ textAlign:stCenter,marginTop:'8px'}">〜</el-col>
            <el-col :span="10">
                <el-form-item>
                        <el-select v-model="sideQuerySelected.estate_priceHigh" clearable placeholder="上限なし" @change="parMaker()" clearable>
                        <template v-if="toBoolean(<?php echo $isPurchase?>)">
                            <template v-for="item in sideQuery.estatePricePuse">
                            <template v-if=isHigher(item.value,sideQuerySelected.estate_priceLow)>
                                <el-option
                                    :label="item.label"
                                    :value="item.value"
                                    >
                                </el-option>
                            </template>
                            </template>
                        </template>
                        <template v-else>
                            <template v-for="item in sideQuery.estatePriceNpuse">
                                <template v-if=isHigher(item.value,sideQuerySelected.estate_priceLow)>
                                    <el-option
                                        :label="item.label"
                                        :value="item.value"
                                        >
                                    </el-option>
                                </template>
                            </template>
                        </template>
                        </el-select>
                    </el-form-item>
            </el-col>
            <el-col :span="24" class="conditions-title" v-bind:style="[boxMarginTop,impact]">間取り</el-col>
            <el-col :span="24">
                <el-form-item>
                    <el-checkbox-group v-model="sideQuerySelected.floor_plan" @change="parMaker()">
                        <el-checkbox 
                        v-for="item in sideQuery.floorPlan"
                            :label="item.label"
                            > {{ item.value }} </el-checkbox>
                    </el-checkbox-group>
                </el-form-item>
            </el-col>
            <el-col :span="24" class="conditions-title" v-bind:style="[boxMarginTop,impact]">土地面積</el-col>
            <el-col :span="10">
                <el-form-item>
                    <el-select v-model="sideQuerySelected.land_areaLow" clearable placeholder="下限なし" @change="parMaker()" clearable>
                        <template v-for="item in sideQuery.landArea">
                            <template v-if=isLower(item.value,sideQuerySelected.land_areaHigh)>
                                <el-option
                                :label="item.label"
                                :value="item.value"
                                >
                                </el-option>
                            </template>
                        </template>
                    </el-select>
                </el-form-item>
            </el-col>
            <el-col :span="4" v-bind:style="{ textAlign:stCenter,marginTop:'8px'}">〜</el-col>
            <el-col :span="10">
                <el-form-item>
                        <el-select v-model="sideQuerySelected.land_areaHigh" clearable placeholder="上限なし" @change="parMaker()" clearable>
                            <template v-for="item in sideQuery.landArea">
                                <template v-if=isHigher(item.value,sideQuerySelected.land_areaLow)>
                                    <el-option
                                    :label="item.label"
                                    :value="item.value"
                                    >
                                    </el-option>
                                </template>
                            </template>
                        </el-select>
                    </el-form-item>
            </el-col>
            <el-col :span="24" class="conditions-title" v-bind:style="[boxMarginTop,impact]">建物面積</br><small>専有面積</small></el-col>
            <el-col :span="10">
                <el-form-item>
                    <el-select v-model="sideQuerySelected.building_areaLow" placeholder="下限なし" @change="parMaker()" clearable>
                        <template v-for="item in sideQuery.buildingArea[0]">
                            <template v-if=isLower(item.value,sideQuerySelected.building_areaHigh)>
                                <el-option
                                :label="item.label"
                                :value="item.value"
                                >
                                </el-option>
                            </template>
                        </template>
                    </el-select>
                </el-form-item>
            </el-col>
            <el-col :span="4" v-bind:style="{ textAlign:stCenter,marginTop:'8px' }">〜</el-col>
            <el-col :span="10">
                <el-form-item>
                        <el-select v-model="sideQuerySelected.building_areaHigh" placeholder="上限なし" @change="parMaker()" clearable>
                            <template v-for="item in sideQuery.buildingArea[0]">
                                <template v-if=isHigher(item.value,sideQuerySelected.building_areaLow)>
                                    <el-option
                                    :label="item.label"
                                    :value="item.value"
                                    >
                                    </el-option>
                                </template>
                            </template>
                        </el-select>
                    </el-form-item>
            </el-col>
            <el-col :span="24" v-bind:style=boxMarginTop>
                <el-col :span="24" class="conditions-title" v-bind:style="[staMarginTop,impact]">駅徒歩</el-col>
                <el-col :span="24">
                    <el-form-item>
                        <el-select v-model="sideQuerySelected.transport_time" placeholder="上限なし" @change="parMaker()" clearable>
                            <el-option
                            v-for="item in sideQuery.transportTime"

                            :label="item.label"
                            :value="item.value"
                            >
                            </el-option>
                        </el-select>
                    </el-form-item>           
                </el-col>
            </el-col>
            <el-col :span="24" v-bind:style=boxMarginTop>
                    <el-form-item>
                    <el-input v-model="sideQuerySelected.freeword" placeholder="フリーワード検索" prefix-icon="el-icon-search" @blur="parMaker()"></el-input>
                    </el-form-item>
            </el-col>

            <el-col :span="24" v-bind:style=boxMarginTop>
                <el-form-item v-bind:style="{height: '70px',boxSizing: 'borderBox',textAlign:'center'}">
                    <el-link v-bind:href="queryparm" :underline="false" type="info" id="searchButton"><el-button type="info" icon="el-icon-search" v-bind:style="{width: '100%',height:'70px',fontSize:'1rem'}" round>
                    この条件で検索</el-button></el-link>
                </el-form-item>
            </el-col>
        </el-form>
    </el-row>
</el-dialog>

<el-col :span="24" style="padding: 24px">
    <el-link href="./index.php" type="primary">ホーム</el-link><i class="el-icon-arrow-right"></i>
    <template v-if="toBoolean(<?php echo $puQueried?>)"> PICK UP物件 </template>
    <template v-else>
        <template v-if="toBoolean(<?php echo $isPurchase?>)"><el-link href="conditions.php?puse=1" type="primary">物件を買う</el-link></template>
        <template v-else><el-link href="conditions.php?puse=0" type="primary">物件を借りる</el-link></template><i class="el-icon-arrow-right"></i> 物件一覧
    </template>
</el-col>

<el-container style="padding: 20px 0.1px">

    <!-- 画面左側パーツ -->
    <template v-if="!toBoolean(<?php echo $puQueried ?>)"> 
        <template v-if="!isSm">
            <el-aside style="margin-top:32px">
                <!-- 検索条件の変更・追加 -->
                <el-row v-bind:style=changeSearchQuery>
                    <el-col :span="24" v-bind:style=title v-bind:style="{ marginBottom:'10px' }">
                        <p v-bind:style="{fontSize:bigChar - 0.2 + 'rem'}">検索条件の</p>
                        <h1 v-bind:style="{ fontSize:bigChar + 'rem' }">変更</h1>
                    </el-col>
                    <el-form :model="sideQuerySelected">
                        <el-col :span="24" class="conditions-title" v-bind:style="[boxMarginTop,impact]">価格</el-col>
                        <el-col :span="10">
                            <el-form-item>
                                <el-select v-model="sideQuerySelected.estate_priceLow" clearable placeholder="下限なし" name="estate_priceLow" @change="parMaker()" clearable>
                                    <template v-if="toBoolean(<?php echo $isPurchase?>)">
                                        <template v-for="item in sideQuery.estatePricePuse">
                                            <template v-if=isLower(item.value,sideQuerySelected.estate_priceHigh)>
                                            <el-option
                                                :label="item.label"
                                                :value="item.value"
                                                >
                                            </el-option>
                                            </template>
                                        </template>
                                    </template>
                                    <template v-else>
                                        <template v-for="item in sideQuery.estatePriceNpuse">
                                            <template v-if=isLower(item.value,sideQuerySelected.estate_priceHigh)>
                                                <el-option
                                                    :label="item.label"
                                                    :value="item.value"
                                                    >
                                                </el-option>
                                            </template>
                                        </template>
                                    </template>
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :span="4" v-bind:style="{ textAlign:stCenter,marginTop:'8px'}">〜</el-col>
                        <el-col :span="10">
                            <el-form-item>
                                    <el-select v-model="sideQuerySelected.estate_priceHigh" clearable placeholder="上限なし" @change="parMaker()" clearable>
                                    <template v-if="toBoolean(<?php echo $isPurchase?>)">
                                        <template v-for="item in sideQuery.estatePricePuse">
                                        <template v-if=isHigher(item.value,sideQuerySelected.estate_priceLow)>
                                            <el-option
                                                :label="item.label"
                                                :value="item.value"
                                                >
                                            </el-option>
                                        </template>
                                        </template>
                                    </template>
                                    <template v-else>
                                        <template v-for="item in sideQuery.estatePriceNpuse">
                                            <template v-if=isHigher(item.value,sideQuerySelected.estate_priceLow)>
                                                <el-option
                                                    :label="item.label"
                                                    :value="item.value"
                                                    >
                                                </el-option>
                                            </template>
                                        </template>
                                    </template>
                                    </el-select>
                                </el-form-item>
                        </el-col>
                        <el-col :span="24" class="conditions-title" v-bind:style="[boxMarginTop,impact]">間取り</el-col>
                        <el-col :span="24">
                            <el-form-item>
                                <el-checkbox-group v-model="sideQuerySelected.floor_plan" @change="parMaker()">
                                    <el-checkbox 
                                    v-for="item in sideQuery.floorPlan"
                                        :label="item.label"
                                        > {{ item.value }} </el-checkbox>
                                </el-checkbox-group>
                            </el-form-item>
                        </el-col>
                        <el-col :span="24" class="conditions-title" v-bind:style="[boxMarginTop,impact]">土地面積</el-col>
                        <el-col :span="10">
                            <el-form-item>
                                <el-select v-model="sideQuerySelected.land_areaLow" clearable placeholder="下限なし" @change="parMaker()" clearable>
                                    <template v-for="item in sideQuery.landArea">
                                        <template v-if=isLower(item.value,sideQuerySelected.land_areaHigh)>
                                            <el-option
                                            :label="item.label"
                                            :value="item.value"
                                            >
                                            </el-option>
                                        </template>
                                    </template>
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :span="4" v-bind:style="{ textAlign:stCenter,marginTop:'8px'}">〜</el-col>
                        <el-col :span="10">
                            <el-form-item>
                                    <el-select v-model="sideQuerySelected.land_areaHigh" clearable placeholder="上限なし" @change="parMaker()" clearable>
                                        <template v-for="item in sideQuery.landArea">
                                            <template v-if=isHigher(item.value,sideQuerySelected.land_areaLow)>
                                                <el-option
                                                :label="item.label"
                                                :value="item.value"
                                                >
                                                </el-option>
                                            </template>
                                        </template>
                                    </el-select>
                                </el-form-item>
                        </el-col>
                        <el-col :span="24" class="conditions-title" v-bind:style="[boxMarginTop,impact]">建物面積</br><small>専有面積</small></el-col>
                        <el-col :span="10">
                            <el-form-item>
                                <el-select v-model="sideQuerySelected.building_areaLow" placeholder="下限なし" @change="parMaker()" clearable>
                                    <template v-for="item in sideQuery.buildingArea[0]">
                                        <template v-if=isLower(item.value,sideQuerySelected.building_areaHigh)>
                                            <el-option
                                            :label="item.label"
                                            :value="item.value"
                                            >
                                            </el-option>
                                        </template>
                                    </template>
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :span="4" v-bind:style="{ textAlign:stCenter,marginTop:'8px' }">〜</el-col>
                        <el-col :span="10">
                            <el-form-item>
                                    <el-select v-model="sideQuerySelected.building_areaHigh" placeholder="上限なし" @change="parMaker()" clearable>
                                        <template v-for="item in sideQuery.buildingArea[0]">
                                            <template v-if=isHigher(item.value,sideQuerySelected.building_areaLow)>
                                                <el-option
                                                :label="item.label"
                                                :value="item.value"
                                                >
                                                </el-option>
                                            </template>
                                        </template>
                                    </el-select>
                                </el-form-item>
                        </el-col>
                        <el-col :span="24" v-bind:style=boxMarginTop>
                            <el-col :span="24" class="conditions-title" v-bind:style="[staMarginTop,impact]">駅徒歩</el-col>
                            <el-col :span="24">
                                <el-form-item>
                                    <el-select v-model="sideQuerySelected.transport_time" placeholder="上限なし" @change="parMaker()" clearable>
                                        <el-option
                                        v-for="item in sideQuery.transportTime"

                                        :label="item.label"
                                        :value="item.value"
                                        >
                                        </el-option>
                                    </el-select>
                                </el-form-item>           
                            </el-col>
                        </el-col>
                        <el-col :span="24" v-bind:style=boxMarginTop>
                                <el-form-item>
                                <el-input v-model="sideQuerySelected.freeword" placeholder="フリーワード検索" prefix-icon="el-icon-search" @blur="parMaker()"></el-input>
                                </el-form-item>
                        </el-col>

                        <el-col :span="24" v-bind:style=boxMarginTop>
                            <el-form-item v-bind:style="{height: '70px',boxSizing: 'borderBox'}">
                                <el-link v-bind:href="queryparm" :underline="false" type="info" id="searchButton"><el-button type="info" icon="el-icon-search" v-bind:style="{width: '100%',height:'70px',fontSize:bigChar + 'rem'}" round>
                                この条件で検索</el-button></el-link>
                            </el-form-item>
                        </el-col>
                    </el-form>
                </el-row>
            </el-aside>
        </template>
    </template>

    <!-- メインパーツ -->
    <el-main v-bind:style="{ padding: 0}">
        <el-row>
            <el-col :span="24" 
                v-bind:style="{ 
                    border:borderDef,
                    color:mainColor,
                    fontSize:bigChar + 'rem',
                    fontWeight:fwbold, 
                    borderRadius: radius + 'px',
                    height:'50px',
                    paddingLeft:'10px',
                    paddingTop:'10px'}">
                <h1>物件一覧</h1>
            </el-col>
            <template v-if="toBoolean(<?php echo intval($count);?>)">
                <el-col :span="24">
                    <el-row>
                        <el-col :md="24" :lg="18">
                            <el-row v-bind:style="{height: '100%'}">
                                <el-col :md="24" :lg="3" v-bind:style="{textAlign:'center',lineHeight:'80px'}">表示順</el-col>
                                <el-col :md="24" :lg="21" v-bind:style="{lineHeight:'80px'}">
                                    <el-col  :xs="24" :sm="12" :md="6" v-bind:style="{textAlign:'center'}"><el-button class="order" @click="sortByPickup()">&nbsp;オススメ&nbsp;</el-button></el-col>
                                    <el-col  :xs="24" :sm="12" :md="6" v-bind:style="{textAlign:'center'}"><el-button class="order" @click="sortByNew()">&nbsp;&emsp;新着&emsp;&nbsp;</el-button></el-col>
                                    <el-col  :xs="24" :sm="12" :md="6" v-bind:style="{textAlign:'center'}"><el-button class="order" @click="sortByPriceL()">価格が安い</el-button></el-col>
                                    <el-col  :xs="24" :sm="12" :md="6" v-bind:style="{textAlign:'center'}"><el-button class="order" @click="sortByPriceH()">価格が高い</el-button></el-col>                        
                                </el-col>
                            </el-row>
                        </el-col>
                        <el-col :md="24" :lg={span:5,offset:1}>
                            <el-row v-bind:style="{height: '100%'}">
                                <el-col :xs="24" :span="10" v-bind:style="{textAlign:'center',lineHeight:'80px'}">表示数</el-col>
                                <el-col :xs="24" :span="14" v-bind:style="{textAlign:'center',lineHeight:'80px'}">
                                    <el-select class="order" v-model="displayNum" placeholder="10件">
                                        <el-option :label="'10件'" :value="'10'">10件</el-option>
                                        <el-option :label="'20件'" :value="'20'">20件</el-option>
                                        <el-option :label="'30件'" :value="'30'">30件</el-option>
                                        <el-option :label="'40件'" :value="'40'">40件</el-option>
                                    </el-select>                                   
                                </el-col>
                            </el-row>
                        </el-col>
                    </el-row>
                </el-col>

                <el-row v-bind:style=elMainBoxPad()>
                <template v-for="estate in pagedList">
                    <template v-if="transportTimeJudge(estate.transportTime)">
                        <div v-bind:style="{maxWidth:'800px',elMainBoxMar,float:'left'}">
                            <el-col :span="24" v-bind:style=estateName><h1 v-bind:style="{fontSize: '2rem',lineHeight:'80px'}">{{ estate.name }}</h1></el-col>
                            <el-col :span="24" v-bind:style=estateData style="border-radius: 0 0 1rem 1rem;">
                                <el-row style="height: 100%">
                                    <el-col :sm="24" :md="9">
                                        <el-row>
                                            <el-col :span="24" style="height: 200px; margin-bottom:8px">
                                                <el-image style="width: 100%; height: 100%"
                                                        :fit="'contain'"
                                                        v-bind:src="estate.mainImage"
                                                        >
                                                    <div slot="error" style="width: 100%; height: 100%" class="image-slot" v-bind:style="dummy">
                                                        <img src="../../www/img/JUST-FIT_ロゴ.png" :style="imgStyle">
                                                    </div>
                                                </el-image>                        
                                            </el-col>

                                            <el-col :span="24" class="hidden-sm-and-down">
                                                <el-col :span="11" style="height:120px;">
                                                    <el-image style="width: 100%; height: 100%"
                                                            :fit="'contain'"
                                                            v-bind:src="estate.subImageF"
                                                            >
                                                        <div slot="error" class="image-slot" v-bind:style="dummy">
                                                            <img src="../../www/img/JUST-FIT_ロゴ.png" :style="imgStyle">  
                                                        </div>
                                                    </el-image>  
                                                </el-col>
                                                <el-col :span="11" :offset="2" style="height:120px;">
                                                    <el-image style="width: 100%; height: 100%"
                                                            :fit="'contain'"
                                                            v-bind:src="estate.subImageS"
                                                            >
                                                        <div slot="error" class="image-slot" v-bind:style="dummy">
                                                            <img src="../../www/img/no_image.png" :style="imgStyle">   
                                                        </div>                        
                                                    </el-image>  
                                                </el-col>
                                            </el-col>
                                        </el-row>
                                    </el-col>
                                    <el-col :sm="24" :md={span:14,offset:1} style="height: 100%">
                                        <el-row style="height: 100%">
                                            <el-col :xs={pull:1} :span="6" :offset="18" style="text-align:right"><el-tag :type="estate.tagType" effect="plain" style="height:32px; width:96px;line-height:32px;font-size:0.9rem;text-align:center">{{ estate.type }}</el-tag></el-col>                             
                                            <el-col :span="24" v-bind:style="estateData" style="height:80px; margin:16px 0;"><h2 style="white-space:pre-wrap; word-wrap:break-word;">{{ estate.description }}</h2></el-col>
                                            <!-- 価格 -->
                                            <el-row v-bind:style="listedData">
                                                <!-- 価格欄の項目名,puse=0なら賃料,1なら価格,という項目名で表示する -->
                                                <template v-if="toBoolean(estate.purchaseFlg)">
                                                    <el-col :xs="24" :sm="24" :md="6" v-bind:style="estateInfoCategory"><h2>価格</h2></el-col>
                                                </template>
                                                <template v-else>
                                                    <el-col :xs={span:24} :sm="24" :md="6" v-bind:style="estateInfoCategory"><h2>賃料</h2></el-col>
                                                </template>
                                                <el-row>
                                                    <el-col :xs="14" :sm="12" :md={span:9,offset:1} style="color: red">
                                                        <h2 v-bind:style="{ fontSize:'1.4rem',fontWeight:'bold' }">
                                                            <b v-bind:style="{fontSize: '2rem',fontWeight: 'bold'}">
                                                                {{ okuManNum(estate.price) }}
                                                            </b>
                                                            {{ okuMan(estate.price) }}
                                                        </h2>
                                                    </el-col>
                                                    <el-col :xs="10" :sm="12" :md="8" v-bind:style="{fontSize:maFeeFontSize() + 'rem'}">
                                                        (管理費:{{ estate.managementFee }}円)
                                                    </el-col>
                                                </el-row>
                                            </el-row>
                                            <el-col :span="24" style="height: 70%">
                                                <!-- 交通 -->
                                                <el-row v-bind:style="listedData">
                                                    <el-col :sm="24" :md="6" v-bind:style="estateInfoCategory"><h2>交通</h2></el-col>
                                                    <el-col :sm="24" :md={span:17,offset:1}>
                                                        <el-col :span="17">{{ estate.transportStation }}</el-col>
                                                        <el-col :span="7" style="font-size:0.8rem">徒歩{{ estate.transportTime }}分</el-col>
                                                    </el-col>
                                                </el-row>
                                                <!-- 所在地 -->
                                                <el-row v-bind:style="listedData">
                                                    <el-col :sm="24" :md="6" v-bind:style="estateInfoCategory"><h2>所在地</h2></el-col>
                                                    <el-col :sm="24" :md={span:17,offset:1}>{{ estate.address }}</el-col>
                                                </el-row>
                                                <!-- 土地面積 -->
                                                <template v-if="toBoolean(estate.landArea)">
                                                    <el-row v-bind:style="listedData">
                                                        <el-col :sm="24" :md="6" v-bind:style="estateInfoCategory"><h2>土地面積</h2></el-col>
                                                        <el-col :sm="24" :md={span:17,offset:1}>{{ estate.landArea }} ㎡</el-col>
                                                    </el-row>
                                                </template>
                                                <!-- 建物面積 -->
                                                <template v-if="toBoolean(estate.buildingArea)">
                                                    <el-row v-bind:style="listedData">
                                                        <template v-if="estate.estkbn != 2">
                                                            <el-col :sm="24" :md="6" v-bind:style="estateInfoCategory"><h2>建物面積</h2></el-col>
                                                        </template>
                                                        <template v-else>
                                                            <el-col :sm="24" :md="6" v-bind:style="estateInfoCategory"><h2>専有面積</h2></el-col>
                                                        </template>
                                                        <el-col :sm="24" :md={span:17,offset:1}>{{ estate.buildingArea }} ㎡</el-col>
                                                    </el-row>
                                                </template>
                                                <!-- 間取り -->
                                                <template v-if="estate.estkbn != 0">
                                                    <el-row v-bind:style="listedData">
                                                        <el-col :sm="24" :md="6" v-bind:style="estateInfoCategory"><h2>間取り</h2></el-col>
                                                        <el-col :sm="24" :md={span:17,offset:1}>{{ estate.floorPlan }}</el-col>
                                                    </el-row>
                                                </template>
                                                <el-row v-bind:style="listedData">
                                                    <el-col :sm="24" :md="6" v-bind:style="estateInfoCategory"><h2>敷金 / 礼金</h2></el-col>
                                                    <el-col :sm="24" :md={span:17,offset:1}>{{ estate.securityDep }}円/ {{ estate.keyMoney }}円</el-col>
                                                </el-row>
                                                <el-row>
                                                    <el-col :xs={span:7,offset:17} :sm={span:10,offset:14} :lg={span:9,offset:15}>
                                                        <el-link v-bind:href="estate.estCode" :underline="false" type="info">
                                                            <el-button icon="el-icon-document">詳細を見る</el-button>
                                                        </el-link>
                                                    </el-col>
                                                </el-row>
                                            </el-col>                        
                                        </el-row>
                                    </el-col>
                                </el-row>
                            </el-col>
                            </div>                                 
                    </template>
                </template>
                </el-row>   
                <el-col class="page-menu">
                    <el-pagination
                        background
                        layout="prev, pager, next"
                        :page-size.sync="displayNum"
                        :total="estateList.length"
                        :current-page.sync="currentPage"
                    >
                    </el-pagination>
                </el-col>    
            </template>
            <template v-else>
                <el-row v-bind:style="{height:'80vh'}">
                    <el-col :span="24" style="margin-top:64px; margin-left:16px">
                        <h1>お探しの条件に一致する物件はございませんでした。</h1>
                    </el-col>
                </el-row>
            </template>
        </el-row>
    </el-main>

</el-container>

<?php include "./footer.php"; ?>

<script>
    const app = new Vue({
        el: "#app",
        data:function(){
        return{
            drawer: false,  //ハンバーガーメニューのためのプロパティ

            urlNow:"<?php echo $parmGen;?>",
            mainColor: "#ED701A",
            borderDef:'2px solid #ED701A',
            paddingDef:'20',
            radius: '30',
            stCenter:'center',
            bigChar: '1.6',
            half:'50%',
            quat:'25%',
            fwbold:'bold',
            limitBottom: '下限なし',
            limitTop:'上限なし',
            displayNum:10,
            queryparm:'<?php echo $srt;?>',
            currentPage:1,
            width: window.innerWidth,
            height: window.innerHeight,
            dialogFormVisible: false,
            elMainBoxPad: function(){
                if(this.width <= 1398){
                    return 'padding: 0';
                }else{
                    return 'padding:0 '+ Math.floor((((this.width - ((this.width / 1398) * 233)) - 300) - (864 * Math.floor(((this.width - ((this.width / 1398) * 233)) - 300) / 864))) / 2)+'px';
                }
            },
            imgStyle:{
                width: 'auto',
                height: 'auto',
                maxWidth: '100%',
                maxHeight: '100%',
                position: 'relative',
                top: '50%',
                WebkitTransform: 'translateY(-50%)', /* Safari用 */
                transform: 'translateY(-50%)'
            },
            dummy:{
                height:'100%',
                width:'100%',
                // backgroundColor: 'rgb(240, 240, 240)'
            },
            title:{
                borderBottom:'1px solid black',
                textAlign: 'center',
                padding: '5px',
                margin:'20px 0',
                letterSpacing:'0.3em'
            },
            impact:{
                fontSize:'1.2rem',
                fontWeight:'bold'
            },
            boxMarginTop:{
                marginTop:'16px'
            },
            staMarginTop:{
                marginTop:'8px'
            },
            changeSearchQuery:{
                margin:'20px 32px 20px 0',
                padding:'0',
                borderTop:'8px solid #ED701A',
                borderBottom:'2px solid #ED701A',
                position:'sticky',
                top: '0'
            },
            changeSearchModal:{
                margin:'0',
                borderTop:'8px solid #ED701A',
                borderBottom:'2px solid #ED701A',
            },
            estateName:{
                backgroundColor:'#ED701A',
                height:'80px',
                color:'white',
                textAlign:'center',
                borderRadius: '1rem 1rem 0 0',
                margin:'3rem 0 0 0'
            },
            estateData:{
                display:'block',
                border:'1px solid #898989',
                padding:'16px'
            },
            estateInfoCategory:{
                border:'1px solid #898989',
                textAlign:'center',
                height:'100%',
                fontSize:'0.8rem',
            },
            listedData:{
                margin:'8px',
                height:'32px',
                lineHeight:'32px',
            },
            listedDataPrice:{
                height:'72px',
                lineHeight:'72px'
            },
            searchButton:{
                position:'fixed',
                bottom:'10px',
                right:'10px',
                zIndex:'100',
                opacity:'0.9'
            },
            maFeeFontSize:function(){
                if(this.width < 768){
                    return 1.2;
                }else if(this.width < 992){
                    return 1.0;
                }else if(this.width < 1200){
                    return 0.7;
                }else if(this.width < 1920){
                    return 0.9;
                }else{
                    return 1.1;
                }
            },
            elMainBoxMar: function(){
                if(this.width < 768){
                    return "margin:'0'";
                }else{
                    return "margin:'0 32px'";
                }
            },
            okuManNum: function(value){
                if(value >= 10000){
                    return (value / 10000);
                }else{
                    return value;
                }
            },
            okuMan:function(value){
                if(value >= 10000){
                    return '億円';
                }else{
                    return '万円';
                }
            },
            sideQuery:{
                estatePriceNpuse:[{
                    label: "3万円",
                    value: 3.00
                }, {
                    label: "4万円",
                    value: 4.00
                }, {
                    label: "5万円",
                    value: 5.00
                }, {
                    label: "6万円",
                    value: 6.00
                }, {
                    label: "7万円",
                    value: 7.00
                }, {
                    label: "8万円",
                    value: 8.00
                }, {
                    label: "9万円",
                    value: 9.00
                }, {
                    label: "10万円",
                    value: 10.00
                }, {
                    label: "11万円",
                    value: 11.00
                }, {
                    label: "12万円",
                    value: 12.00
                }, {
                    label: "13万円",
                    value: 13.00
                }, {
                    label: "14万円",
                    value: 14.00
                }, {
                    label: "15万円",
                    value: 15.00
                }
                ],
                estatePricePuse:[
                    {label: "1000万円",
                    value: 1000.00
                }, {
                    label: "2000万円",
                    value: 2000.00
                }, {
                    label: "3000万円",
                    value: 3000.00
                }, {
                    label: "4000万円",
                    value: 4000.00
                }, {
                    label: "5000万円",
                    value: 5000.00
                }, {
                    label: "6000万円",
                    value: 6000.00
                }, {
                    label: "7000万円",
                    value: 7000.00
                }, {
                    label: "8000万円",
                    value: 8000.00
                }, {
                    label: "9000万円",
                    value: 9000.00
                }, {
                    label: "1億円",
                    value: 10000.00}
                ],
                floorPlan:[{
                    label:'A',
                    value:'ワンルーム'
                },{
                    label:'B',
                    value:'1K/DK/LDK'
                },{
                    label:'C',
                    value:'2K/DK/LDK'
                },{
                    label:'D',
                    value:'3K/DK/LDK'
                },{
                    label:'E',
                    value:'4K/DK/LDK'
                },{
                    label:'F',
                    value:'5K以上'
                }],
                landArea:[
                {
                    label:'50㎡',
                    value:50
                },{
                    label:'60㎡',
                    value:60
                },{
                    label:'70㎡',
                    value:70
                },{
                    label:'80㎡',
                    value:80
                },{
                    label:'90㎡',
                    value:90
                },{
                    label:'100㎡',
                    value:100
                },{
                    label:'110㎡',
                    value:110
                },{
                    label:'120㎡',
                    value:120
                },{
                    label:'130㎡',
                    value:130
                },{
                    label:'140㎡',
                    value:140
                },{
                    label:'150㎡',
                    value:150
                },{
                    label:'200㎡',
                    value:200
                },{
                    label:'300㎡',
                    value:300
                },{
                    label:'400㎡',
                    value:400
                },{
                    label:'500㎡',
                    value:500
                }    
                ],
                buildingArea:[[
                {
                    label:'50㎡',
                    value:50
                },{
                    label:'60㎡',
                    value:60
                },{
                    label:'70㎡',
                    value:70
                },{
                    label:'80㎡',
                    value:80
                },{
                    label:'90㎡',
                    value:90
                },{
                    label:'100㎡',
                    value:100
                },{
                    label:'110㎡',
                    value:110
                },{
                    label:'120㎡',
                    value:120
                },{
                    label:'130㎡',
                    value:130
                },{
                    label:'140㎡',
                    value:140
                },{
                    label:'150㎡',
                    value:150
                },{
                    label:'200㎡',
                    value:200
                }],
                [{
                    label:'20㎡',
                    value:20
                },{
                    label:'30㎡',
                    value:30
                },{
                    label:'40㎡',
                    value:40
                },{
                    label:'50㎡',
                    value:50
                },{
                    label:'60㎡',
                    value:60
                },{
                    label:'70㎡',
                    value:70
                },{
                    label:'80㎡',
                    value:80
                },{
                    label:'90㎡',
                    value:90
                },{
                    label:'100㎡',
                    value:100
                },{
                    label:'110㎡',
                    value:110
                },{
                    label:'120㎡',
                    value:120
                },{
                    label:'130㎡',
                    value:130
                },{
                    label:'140㎡',
                    value:140
                },{
                    label:'150㎡',
                    value:150
                }]
                ],
                transportTime:[
                    {
                        label:'3分以内',
                        value:3
                    },{
                        label:'5分以内',
                        value:5
                    },{
                        label:'10分以内',
                        value:10
                    },{
                        label:'15分以内',
                        value:15
                    },{
                        label:'20分以内',
                        value:20
                    }
                ]
            },
            sideQuerySelected:{
                estate_priceLow:'<?php echo $sidePriceLow; ?>',
                estate_priceHigh:'<?php echo $sidePriceHigh; ?>',
                floor_plan:[<?php echo $sideFloorPlan; ?>],
                land_areaLow:'<?php echo $sideLandLow; ?>',
                land_areaHigh:'<?php echo $sideLandHigh; ?>',
                building_areaLow:'<?php echo $sideBuildingLow; ?>',
                building_areaHigh:'<?php echo $sideBuildingHigh; ?>',
                transport_time:'<?php echo $sideTransport; ?>',
                freeword:'<?php echo $sideFree; ?>'
            },
            estateList: [
                <?php if($count > 0): ?>
                    <?php foreach($estate_list As $estate): ?>                
                    <?php
                        $est = $dbh->query("SELECT photo_url FROM t_estate_photo WHERE estate_id = $estate[estate_id] AND main_flg = 1 AND delete_flg = 0 LIMIT 1");
                        $mainImage = $est -> fetch(PDO::FETCH_COLUMN);
                        $est = $dbh->query("SELECT photo_url FROM t_estate_photo WHERE estate_id = $estate[estate_id] AND main_flg = 0 AND delete_flg = 0 LIMIT 1");
                        $subImageF = $est -> fetch(PDO::FETCH_COLUMN);
                        $est = $dbh->query("SELECT photo_url FROM t_estate_photo WHERE estate_id = $estate[estate_id] AND main_flg = 0 AND delete_flg = 0 LIMIT 1 OFFSET 2");
                        $subImageS = $est -> fetch(PDO::FETCH_COLUMN);
                        $est = $dbh->query("SELECT transport_id FROM t_estate_transport WHERE estate_id = $estate[estate_id] AND transport_delete_flg = 0 ORDER BY transport_time DESC LIMIT 1");
                        $transport = $est -> fetch(PDO::FETCH_COLUMN);
                        $est = $dbh->query("SELECT transport_station FROM t_estate_transport WHERE transport_id = $transport");
                        $transportStation = $est -> fetch(PDO::FETCH_COLUMN);
                        $est = $dbh->query("SELECT transport_time FROM t_estate_transport WHERE transport_id = $transport");
                        $transportTime = $est -> fetch(PDO::FETCH_COLUMN);
                        ?>
                <?php echo '{'; ?>
                <?php echo 'id:'."'".$estate["estate_id"]."'".','."\n"; ?>
                <?php echo 'purchaseFlg:'."'".$estate["purchase_flg"]."'".','."\n"; ?>
                <?php echo 'name:'."'".$estate["estate_name"]."'".','."\n"; ?>    
                <?php echo 'mainImage:'."'".$mainImage."'".','."\n"; ?>
                <?php echo 'subImageF:'."'".$subImageF."'".','."\n"; ?>
                <?php echo 'subImageS:'."'".$subImageS."'".','."\n"; ?>
                <?php echo 'description:'."'".$estate["description"]."'".','."\n"; ?>
                <?php echo 'price:'."'".floatval($estate["estate_price"])."'".','."\n"; ?>
                <?php echo 'managementFee:'."'".nullZero($estate["management_fee"])."'".','."\n";?>
                <?php echo 'type:'."'".eskbnExchanger($estate["estate_kbn"])."'".','."\n"; ?>
                <?php echo 'transportStation:'."'".$transportStation."'".','."\n"; ?>
                <?php echo 'transportTime:'."'".$transportTime."'".','."\n"; ?>
                <?php echo 'address:'."'".cityExchanger($estate["city_kbn"])."市".$estate["address_detail"]."'".','."\n"; ?>
                <?php echo 'landArea:'."'".nullZero($estate["land_area"])."'".','."\n"; ?>
                <?php echo 'buildingArea:'."'".nullZero($estate["building_area"])."'".','."\n"; ?>
                <?php echo 'floorPlan:'."'".flPtnChecker($estate["floor_plan_num"]).flkbnExchanger($estate["floor_plan_kbn"])."'".','."\n"; ?>
                <?php echo 'pickUp:'."'".$estate["pickup_flg"]."'".','."\n"; ?>
                <?php echo 'register:'."'".$estate["registered_datetime"]."'".','."\n"; ?>
                <?php echo 'tagType:'."'".tagMaker($estate["estate_kbn"])."'".','."\n"; ?>
                <?php echo 'estkbn:'."'".$estate["estate_kbn"]."'".','."\n"; ?>
                <?php echo 'securityDep:'."'".nullZero(number_format($estate["security_dep"]))."'".','."\n"; ?>
                <?php echo 'keyMoney:'."'".nullZero(number_format($estate["key_money"]))."'".','."\n"; ?>
                <?php echo 'estCode:'."'".$linkToDetail.'details.php?estate_code='.$estate["estate_code"]."'"."\n"; ?>
                <?php echo '},'."\n"; ?>
                <?php endforeach;$dbh = null; ?>         
                <?php endif; ?>        
            ]
        }},
        methods:({
            parMaker: function(){
                let parm = '';
                for(key in this.sideQuerySelected){
                    if(this.sideQuerySelected[key] != ''){
                        parm = parm + key + '=' + this.sideQuerySelected[key] + '&';
                    };
                };
                this.queryparm = this.urlNow +'&'+ parm.substring(0,parm.length - 1);
            },
            toBoolean:function(zeroOne){
                if(zeroOne > 0){
                    return true;
                }else{
                    return false;
                }
            },
            transportTimeJudge:function(estateTt){
                if(estateTt <= <?php echo $transportTimeQueried; ?>){
                    return true;
                }else{
                    return false;
                }
            },
            sortByPickup:function(){
                this.estateList.sort(function(a,b){
                    return b.pickUp-a.pickUp;
                }),
                this.currentPage = 1;
            },
            sortByPriceL:function(){
                this.estateList.sort(function(a,b){
                    return a.price-b.price;
                }),
                this.currentPage = 1;
            },
            sortByPriceH:function(){
                this.estateList.sort(function(a,b){
                    return b.price-a.price;
                }),
                this.currentPage = 1;
            },
            sortByNew:function(){
                this.estateList.sort(function(a,b){
                    if(a.register < b.register){
                        return 1;
                    }else{
                        return -1;
                    }
                }),
                this.currentPage = 1;
            },
            isLower:function(value,higher){
                if(String(higher).indexOf('万') != -1){
                    higher=parseInt(higher.substr(0,higher.indexOf('万')));
                }
                if(String(higher).indexOf('㎡') != -1){
                    higher=parseInt(higher.substr(0,higher.indexOf('㎡')));
                }
                if(higher == ''){
                    return true;
                }else if(value <= higher){
                    return true;
                }else{
                    return false;
                }
            },
            isHigher:function(value,lower){
                if(String(lower).indexOf('万') != -1){
                    lower=parseInt(lower.substr(0,lower.indexOf('万')));
                }
                if(String(lower).indexOf('㎡') != -1){
                    lower=parseInt(lower.substr(0,lower.indexOf('㎡')));
                }
                if(lower == ''){
                    return true;
                }else if(value >= lower){
                    return true;
                }else{
                    return false;
                }
            },
            handleResize: function() {
             this.width = window.innerWidth;
             this.height = window.innerHeight;
            },
            window: onload = function() {
            let puse = <?php echo $isPurchase; ?>;
            let piup = <?php echo $puQueried?>;
            if(piup == 0){
            if (puse == 0) {
                document.getElementById("navRen").style.backgroundColor = '#fbdac8';
            } else {
                document.getElementById("navPur").style.backgroundColor = '#fbdac8';
            }}
        }
        }),
        computed:{
            pagedList:function(){
                var i = 0;
                var j = 0;
                var computedPage = [];
                var computedList = [];
                while(i < this.displayNum){
                    if(this.transportTimeJudge(this.estateList[j].transportTime)){
                        computedPage.push(this.estateList[j]);
                        j++;
                        i++;
                        if(this.estateList.length > j){
                            if(i == this.displayNum){
                                computedList.push(computedPage);
                                computedPage=[];
                                i=0;
                            }
                        }else{                       
                            computedList.push(computedPage);
                            i=this.displayNum;
                        }
                    }else{
                        j++;
                        if(this.estateList.length <= j){
                            computedList.push(computedPage);
                            i=this.displayNum;
                        }
                    }
                }
                return computedList[this.currentPage - 1];
            },
            isSm: function(){
                if(this.width < 768){
                    return true;
                }else{
                    return false;
                }
            }
        },
        mounted: function () {
            window.addEventListener('resize', this.handleResize)
        },
        beforeDestroy: function () {
            window.removeEventListener('resize', this.handleResize)
        }
    })
</script>

<style>
    .conditions-title {
        margin-bottom: .8rem;
    }
    .page-menu {
        text-align: left;
        margin-top: 1.5rem;
        padding-left: .5rem;
    }
    .el-pagination {
        width: auto;
    }
    .page-menu li {
        height: 32px;
        width: 32px;
        padding-top: 3px;;
    }
    .page-menu button {
        height: 32px;
        width: 32px;
        padding-top: 3px;;
    }
    .detail-link {
        margin-left: auto !important;
    }
    .detail-link-tag {
        color: #888;
        border-radius: 0;
        border: 1px solid #888;
        width: 8rem;
        text-align: center;
        padding: 8px;
        float: right;
    }
    .detail-link-tag:hover {
        background-color: #409eff;
        color: white;
        transition: 0.3s;
        border: 1px solid #409eff;
    }
    .order{
        width:80%;
        padding:20px;
    }
}
    
</style>

</html>

