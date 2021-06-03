<?php
//必要ファイルの読み込み
include './system/db_info.php';  //DB接続のため dbConnect関数呼び出し
include './system/QueryCheck.php';  //クエリ文のやりとりをしてくれるクラス

// DB接続
$dbh = dbConnect();  //接続

new QueryCheck($dbh);  //引数に$dbhを渡す  これで条件にあう物件情報の検索がされる

$use_id_infos = QueryCheck::$use_id_infos;  //条件にあうestate_idが入る配列
$use_id_count = QueryCheck::$use_id_count;  //条件にあうestate_idが何個あるか
$total_house_count = QueryCheck::$total_house_count;  //条件にあう物件が全部で何個あったか（limitとoffsetがかかっていない）

//市の情報を配列に
$city_kbn_arr = ['船橋', '鎌ヶ谷', '市川', '白井', '松戸'];

//市の情報を配列に
$estate_kbn_tag_arr = [
    "<el-tag style='border-radius: 3px;' effect='plain' type='success'>土地</el-tag>",
    "<el-tag style='border-radius: 3px;' effect='plain' type='info'>事務所</el-tag>",
    "<el-tag style='border-radius: 3px;' effect='plain' type='warning'>マンション</el-tag>",
    "<el-tag style='border-radius: 3px;' effect='plain'>中古戸建</el-tag>",
    "<el-tag style='border-radius: 3px;' effect='plain' type='danger'>新築戸建</el-tag>"
];

//間取り情報
$floor_kbn_arr = ['土地', 'R', 'K', 'DK', 'LDK'];

//画像が存在しない時に使用するダミー画像のURLを配列で定義
$dummy_url_arr = ["../../www/img/no_image.png", "../../www/img/no_image.png", "../../www/img/no_image.png"];


if($use_id_count != 0) {  //該当する物件があったら各物件の情報を取得する
    //物件情報を入れる配列の定義
    $house_infos = array();  //物件の詳細情報を入れる配列
    $transport_infos = array();  //物件の駅徒歩情報を入れる配列
    $photo_infos_main = array();  //物件のメイン写真を入れる配列
    $photo_infos_sub = array();  //物件のサブ写真を入れる配列
    $tag_infos = array();  //物件のestate_kbnを元にタグのHTMLを入れる配列

    //各種情報取得
    foreach($use_id_infos as $id) {
        //物件詳細情報
        $sql = "SELECT estate_name, description, estate_code, estate_price, estate_kbn, city_kbn, address_detail, land_area, building_area, floor_plan_num, floor_plan_kbn, management_fee, security_dep, key_money, t_estate_detail.update_datetime as update_datetime FROM t_estate JOIN t_estate_detail ON t_estate.estate_id = t_estate_detail.estate_id WHERE t_estate.estate_id = {$id};";
        $house_stmt = $dbh->query($sql);
        $house_infos[] = $house_stmt->fetchall(PDO::FETCH_ASSOC)[0];
        //交通情報
        $sql = "SELECT transport_station, transport_time FROM t_estate_transport WHERE transport_delete_flg = 0 AND estate_id = {$id} ORDER BY transport_time ASC LIMIT 1;";
        $trans_stmt = $dbh->query($sql);
        $transport_infos[] = $trans_stmt->fetchall(PDO::FETCH_ASSOC)[0];
        //写真URL（メイン）
        $sql = "SELECT photo_url FROM t_estate_photo WHERE delete_flg = 0 AND estate_id = {$id} AND main_flg = 1 LIMIT 1;";
        $photo_main_stmt = $dbh->query($sql);
        $photo_infos_main[] = $photo_main_stmt->fetchall(PDO::FETCH_ASSOC);
        //写真URL（サブ）
        $sql = "SELECT photo_url FROM t_estate_photo WHERE delete_flg = 0 AND estate_id = {$id} AND main_flg = 0 ORDER BY display_order ASC LIMIT 3;";
        $photo_sub_stmt = $dbh->query($sql);
        $photo_infos_sub[] = $photo_sub_stmt->fetchall(PDO::FETCH_ASSOC);
    }

    //物件情報で写真表示に使う配列の定義
    $main_url_arr = array();  //メイン画像用  中身のイメージ → ["url","url","url",...];  配列
    $sub_url_arr = array();   //サブ画像用    中身のイメージ → [["url","url"],["url", "url"],["url", "url"],...]; 二次元配列
    $sub_url = array();  //上記のサブ画像用の二次元配列を作るための配列

    //物件情報にて使用する情報の加工 市、価格、管理費、敷金、礼金、間取り 
    for($i = 0; $i < $use_id_count; $i++) {
        $house_infos[$i]['city_kbn'] = $city_kbn_arr[$house_infos[$i]['city_kbn']];  //該当する市の名前を入れる
        $house_infos[$i]['estate_price'] = preg_replace('/\.?0+$/', '', number_format($house_infos[$i]['estate_price'], 2));  //価格は「万円」がcss指定が異なるので付けない
        $house_infos[$i]['management_fee'] = number_format($house_infos[$i]['management_fee']) . '円';  //円までつける
        $house_infos[$i]['security_dep'] = number_format($house_infos[$i]['security_dep']) . '円';
        $house_infos[$i]['key_money'] = number_format($house_infos[$i]['key_money']) . '円';
        $house_infos[$i]['floor_plan_kbn'] = $floor_kbn_arr[$house_infos[$i]['floor_plan_kbn']];  //「4LDK」などの形に
        
        $tag_infos[] = $estate_kbn_tag_arr[$house_infos[$i]['estate_kbn']];  //タグを作成
        
        if(isset($photo_infos_main[$i][0]['photo_url'])) {  //メイン写真
            $main_url_arr[] = $photo_infos_main[$i][0]['photo_url'];
        } else {
            $main_url_arr[] = $dummy_url_arr[0];  //なければダミー
        }
        if(isset($photo_infos_sub[$i][0]['photo_url'])) {  //サブ写真1枚目
            $sub_url[] = $photo_infos_sub[$i][0]['photo_url'];
        } else {
            $sub_url[] = $dummy_url_arr[1];
        }
        if(isset($photo_infos_sub[$i][1]['photo_url'])) {  //サブ写真2枚目
            $sub_url[] = $photo_infos_sub[$i][1]['photo_url'];
        } else {
            $sub_url[] = $dummy_url_arr[2];
        }

        $sub_url_arr[] = $sub_url;  //上記の処理で作られた配列を二次元配列に追加
        $sub_url = array();  //配列を空に
    }
}

//現在日付から1週間前算出
$one_week_ago = date("Y-m-d H:i:s",strtotime("-1 week"));

//表示順序のボタン押下後に遷移するURLの作成
$basic_url = null;

if(isset($_GET['piup'])) {  //piupフラグがあったら
    $basic_url = "./estatelist.php?piup=1";

} else {  //なかったらpuseを決める
    $basic_url = "./estatelist.php?puse={$_GET['puse']}";
}

//上記の$basic_urlを元に、表示件数、
$search_basic_url = $basic_url;  //検索条件を決めるURLの元となるURLが入る
$limit_basic_url = $basic_url;  //表示件数を決めるURLの元となるURLが入る（表示件数のセレクトが選ばれた時limitとpageの情報がURLに入る）
$pagination_basic_url = $basic_url;  //ページを決めるURLの元となるURLが入る（ページネーションが押された時にpageの情報がURLに入る）
$order_basic_url = $basic_url;  //表示順序を決めるURLの元となるURLが入る（表示順序が選ばれた時orderとpageの情報がURLに入る）

//上記四つのURLに反映させたくないキー文字列を配列に定義
$search_for_arr = ['estate_priceLow', 'estate_priceHigh', 'floor_plan', 'land_areaLow', 'land_areaHigh', 'building_areaLow', 'building_areaHigh', 'transport_time', 'freeword'];
//上記で定義した配列を用いてURLの作成
foreach($_GET as $key => $value) {
    if($key == 'piup') { continue; }  //piupは既に条件に入っているのでcontinue
    if($key == 'puse') { continue; }  //puseは既に条件に入っているのでcontinue
    
    if(array_search($key, $search_for_arr) == false && array_search($key, $search_for_arr) != 0) {  //「この条件で検索」用
        $search_basic_url .= "&" . $key . "=" . $value;
    }

    if($key != 'limit' && $key != 'page') {  //表示件数用
        $limit_basic_url .= "&" . $key . "=" . $value;
    }
    
    if($key != 'page') {  //ページネーション用
        $pagination_basic_url .= "&" . $key . "=" . $value;
    }

    if($key != 'order' && $key != 'page') {  //表示順序用
        $order_basic_url .= "&" . $key . "=" . $value;
    }
}

//表示順序がどの並び順で選択されているかを確認する（表示する時に選択されているボタンには「disabled」を当てたいため
$el_button_disabled = [null, null, null, null];  //初期値は全てnull
$el_button_kind = ['pickup', 'new', 'low', 'high'];  //ボタンの種類
if(isset($_GET['order'])) {
    $button_num = array_search($_GET['order'], $el_button_kind);  //配列内を検索し
    $el_button_disabled[$button_num] = 'disabled';  
}


//floor_planの情報、今回のページの読み込み時にfloor_plan情報が選択されたかを確認する二次元配列
$floor_plan_bool = [[false, '1,1'],[false, '1,2'],[false, '1,3'],[false, '1,4'],[false, '2,2'],[false, '2,3'],[false, '2,4'],[false, '3,2'],[false, '3,3'],[false, '3,4'],[false, '4,2'],[false, '4,3'],[false, '4,4'],[false,'5,5']];
$floor_plan_bool_count = count($floor_plan_bool);  //上の配列の要素数を確認する

//floor_plan情報を受け取ったら
if(isset($_GET['floor_plan'])) {  
    $floor_plan_commas = explode(',', $_GET['floor_plan']);  //floor_plan=1,2,1,3 のようにくる情報をカンマ区切りで配列に入れる[1,2,1,3]のように
    $floor_plan_commas_count = count($floor_plan_commas);  //上で作られた配列の要素数を数える
    $floor_plan_oneset = [];  //下のfor文で使用する空の配列
    for($i = 0; $i < $floor_plan_commas_count; $i = $i + 2) {  //配列の要素二つで一つの間取り情報なのでまとめて配列に入れる
        $floor_plan_oneset[] = "{$floor_plan_commas[$i]},{$floor_plan_commas[$i + 1]}";  //$floor_plan_onset[] = "1,2"; のようになるイメージ
    }
    $oneset_count = count($floor_plan_oneset);  //上記for文で作られた間取り情報の要素数を数える

    for($i = 0; $i < $oneset_count; $i++) {  //$floor_plan_onesetがあるだけforで回す
        switch($floor_plan_oneset[$i]) {  //それぞれ該当する文字列があったら該当する配列のfalseをtrueに変更
            case $floor_plan_bool[0][1]:
                $floor_plan_bool[0][0] = true; 
                break;
            case $floor_plan_bool[1][1]:
                $floor_plan_bool[1][0] = true; 
                break;
            case $floor_plan_bool[2][1]:
                $floor_plan_bool[2][0] = true; 
                break;
            case $floor_plan_bool[3][1]:
                $floor_plan_bool[3][0] = true; 
                break;
            case $floor_plan_bool[4][1]:
                $floor_plan_bool[4][0] = true; 
                break;
            case $floor_plan_bool[5][1]:
                $floor_plan_bool[5][0] = true; 
                break;
            case $floor_plan_bool[6][1]:
                $floor_plan_bool[6][0] = true; 
                break;
            case $floor_plan_bool[7][1]:
                $floor_plan_bool[7][0] = true; 
                break;
            case $floor_plan_bool[8][1]:
                $floor_plan_bool[8][0] = true; 
                break;
            case $floor_plan_bool[9][1]:
                $floor_plan_bool[9][0] = true; 
                break;
            case $floor_plan_bool[10][1]:
                $floor_plan_bool[10][0] = true; 
                break;
            case $floor_plan_bool[11][1]:
                $floor_plan_bool[11][0] = true; 
                break;
            case $floor_plan_bool[12][1]:
                $floor_plan_bool[12][0] = true; 
                break;
            case $floor_plan_bool[13][1]:
                $floor_plan_bool[13][0] = true; 
                break;
        }
    }
}

?>


<!-- </header>までが入る ======================== -->
<?php include "./header.php"; ?>
<!-- ========================================== -->

<div class="current-position">
    <p class="map-link">
        <el-link type="primary" href="./index.php">ホーム </el-link>
        <i class="el-icon-arrow-right"></i>
        <?php if(isset($_GET['piup'])): ?>
        PICK UP一覧
        <?php else: ?>
            <?php if($_GET['puse']): ?>
            <el-link type="primary" href="./conditions.php?puse=1">物件を買う </el-link>
            <?php else: ?>
            <el-link type="primary" href="./conditions.php?puse=0">物件を借りる </el-link>
            <?php endif ?>
            <i class="el-icon-arrow-right"></i>物件一覧
        <?php endif ?>
    </p>
</div>

<el-container class="aside-main-box">

    <?php if(!isset($_GET['piup'])): ?>
    <el-aside class="aside"><!-- 768px未満の時表示されない -->
        <el-row class="condition-title-box">
            <el-col class="condition-title-box-child">
                <p class="change-title-sentence">検索条件の</p>
                <p class="change-title-sentence">変更・追加</p>
            </el-col>
        </el-row>

        <!-- 検索条件を実際に選択するところ -->
        <el-row class="condition-action-box">
            <el-col class="change-action-box"><!-- 価格フォーム -->
                <?php if($_GET['puse'] == 0): ?><!-- 借りるなら「賃料」に買うなら「価格」に -->
                <el-col :span="24" tag="p" class="action-title">賃料</el-col>
                <?php else: ?>
                <el-col :span="24" tag="p" class="action-title">価格</el-col>
                <?php endif ?>
                <div>
                    <el-col :span="10" tag="p">
                        <el-select v-model="selectedOption.priceLowNum" placeholder="下限なし" clearable @change="changePriceNum('0')">
                            <!-- 上限の価格より小さい、または、上限の価格が未選択の時に表示 -->
                            <?php if($_GET['puse'] == 0): ?><!-- puseが0なら賃貸の価格表示を -->
                                <el-option v-for="item in sideQuery.pricePuse0" v-if="item.value <= queryValue.estate_priceHigh || selectedOption.priceHighNum == ''" :label="item.label" :value="item.value" :key="item.value"></el-option>
                            <?php else: ?><!-- puseが1なら購入の価格表示を -->
                                <el-option v-for="item in sideQuery.pricePuse1" v-if="item.value <= queryValue.estate_priceHigh || selectedOption.priceHighNum == ''" :label="item.label" :value="item.value" :key="item.value"></el-option>
                            <?php endif ?>
                        </el-select>
                    </el-col>
                    <el-col :span="4" tag="p" class="middle-border">〜</el-col>
                    <el-col :span="10" tag="p">
                        <el-select v-model="selectedOption.priceHighNum" placeholder="上限なし" clearable @change="changePriceNum('1')">
                            <!-- 下限の価格より大きい、または、下限の価格が未選択の時に表示 -->
                            <?php if($_GET['puse'] == 0): ?>
                                <el-option v-for="item in sideQuery.pricePuse0" v-if="item.value >= queryValue.estate_priceLow || selectedOption.priceLowNum == ''" :label="item.label" :value="item.value" :key="item.value"></el-option>
                            <?php else: ?>
                                <el-option v-for="item in sideQuery.pricePuse1" v-if="item.value >= queryValue.estate_priceLow || selectedOption.priceLowNum == ''" :label="item.label" :value="item.value" :key="item.value"></el-option>
                            <?php endif ?>
                        </el-select>
                    </el-col>
                </div>
            </el-col>

            <el-col class="change-action-box"><!-- 間取りフォーム -->
                <el-col :span="24" tag="p" class="action-title">間取り</el-col>
                <div>
                    <el-col :span="8" tag="p" class="floorPlanCheckbox"><el-checkbox @change="checkFloorPlan" v-model="sideQuery.floorPlan[0].planBool">{{ sideQuery.floorPlan[0]['label'] }}</el-checkbox></el-col>
                    <el-col :span="8" tag="p" class="floorPlanCheckbox"><el-checkbox @change="checkFloorPlan" v-model="sideQuery.floorPlan[1].planBool">{{ sideQuery.floorPlan[1]['label'] }}</el-checkbox></el-col>
                    <el-col :span="8" tag="p" class="floorPlanCheckbox"><el-checkbox @change="checkFloorPlan" v-model="sideQuery.floorPlan[2].planBool">{{ sideQuery.floorPlan[2]['label'] }}</el-checkbox></el-col>
                    <el-col :span="8" tag="p" class="floorPlanCheckbox"><el-checkbox @change="checkFloorPlan" v-model="sideQuery.floorPlan[3].planBool">{{ sideQuery.floorPlan[3]['label'] }}</el-checkbox></el-col>
                    <el-col :span="8" tag="p" class="floorPlanCheckbox"><el-checkbox @change="checkFloorPlan" v-model="sideQuery.floorPlan[4].planBool">{{ sideQuery.floorPlan[4]['label'] }}</el-checkbox></el-col>
                    <el-col :span="8" tag="p" class="floorPlanCheckbox"><el-checkbox @change="checkFloorPlan" v-model="sideQuery.floorPlan[5].planBool">{{ sideQuery.floorPlan[5]['label'] }}</el-checkbox></el-col>
                    <el-col :span="8" tag="p" class="floorPlanCheckbox"><el-checkbox @change="checkFloorPlan" v-model="sideQuery.floorPlan[6].planBool">{{ sideQuery.floorPlan[6]['label'] }}</el-checkbox></el-col>
                    <el-col :span="8" tag="p" class="floorPlanCheckbox"><el-checkbox @change="checkFloorPlan" v-model="sideQuery.floorPlan[7].planBool">{{ sideQuery.floorPlan[7]['label'] }}</el-checkbox></el-col>
                    <el-col :span="8" tag="p" class="floorPlanCheckbox"><el-checkbox @change="checkFloorPlan" v-model="sideQuery.floorPlan[8].planBool">{{ sideQuery.floorPlan[8]['label'] }}</el-checkbox></el-col>
                    <el-col :span="8" tag="p" class="floorPlanCheckbox"><el-checkbox @change="checkFloorPlan" v-model="sideQuery.floorPlan[9].planBool">{{ sideQuery.floorPlan[9]['label'] }}</el-checkbox></el-col>
                    <el-col :span="8" tag="p" class="floorPlanCheckbox"><el-checkbox @change="checkFloorPlan" v-model="sideQuery.floorPlan[10].planBool">{{ sideQuery.floorPlan[10]['label'] }}</el-checkbox></el-col>
                    <el-col :span="8" tag="p" class="floorPlanCheckbox"><el-checkbox @change="checkFloorPlan" v-model="sideQuery.floorPlan[11].planBool">{{ sideQuery.floorPlan[11]['label'] }}</el-checkbox></el-col>
                    <el-col :span="8" tag="p"><el-checkbox @change="checkFloorPlan" v-model="sideQuery.floorPlan[12].planBool">{{ sideQuery.floorPlan[12]['label'] }}</el-checkbox></el-col>
                    <el-col :span="8" tag="p"><el-checkbox @change="checkFloorPlan" v-model="sideQuery.floorPlan[13].planBool">{{ sideQuery.floorPlan[13]['label'] }}</el-checkbox></el-col>
                </div>
            </el-col>

            <el-col class="change-action-box"><!-- 土地面積フォーム -->
                <el-col :span="24" tag="p" class="action-title">土地面積</el-col>
                <div>
                    <el-col :span="10" tag="p">
                        <el-select v-model="selectedOption.landAreaLowNum" placeholder="下限なし" clearable @change="changeLandAreaNum('0')">
                            <!-- 上限の価格より小さい、または、上限の価格が未選択の時に表示 -->
                            <el-option v-for="item in sideQuery.landArea" v-if="item.value <= queryValue.land_areaHigh || selectedOption.landAreaHighNum == ''" :label="item.label" :value="item.value" :key="item.value"></el-option>
                        </el-select>
                    </el-col>
                    <el-col :span="4" tag="p" class="middle-border">〜</el-col>
                    <el-col :span="10" tag="p">
                        <el-select v-model="selectedOption.landAreaHighNum" placeholder="上限なし" clearable @change="changeLandAreaNum('1')">
                            <!-- 下限の価格より大きい、または、下限の価格が未選択の時に表示 -->
                            <el-option v-for="item in sideQuery.landArea" v-if="item.value >= queryValue.land_areaLow || selectedOption.landAreaLowNum == ''" :label="item.label" :value="item.value" :key="item.value"></el-option>
                        </el-select>
                    </el-col>
                </div>
            </el-col>

            <el-col class="change-action-box"><!-- 建物、専有面積フォーム -->
                <el-col :span="24" tag="p" class="action-title">建物・専有面積</el-col>
                <div>
                    <el-col :span="10" tag="p">
                        <el-select v-model="selectedOption.buildingAreaLowNum" placeholder="下限なし" clearable @change="changeBuildingAreaNum('0')">
                            <!-- 上限の価格より小さい、または、上限の価格が未選択の時に表示 -->
                            <el-option v-for="item in sideQuery.buildingArea" v-if="item.value <= queryValue.building_areaHigh || selectedOption.buildingAreaHighNum == ''" :label="item.label" :value="item.value" :key="item.value"></el-option>
                        </el-select>
                    </el-col>
                    <el-col :span="4" tag="p" class="middle-border">〜</el-col>
                    <el-col :span="10" tag="p">
                        <el-select v-model="selectedOption.buildingAreaHighNum" placeholder="上限なし" clearable @change="changeBuildingAreaNum('1')">
                            <!-- 下限の価格より大きい、または、下限の価格が未選択の時に表示 -->
                            <el-option v-for="item in sideQuery.buildingArea" v-if="item.value >= queryValue.building_areaLow || selectedOption.buildingAreaLow == ''" :label="item.label" :value="item.value" :key="item.value"></el-option>
                        </el-select>
                    </el-col>
                </div>
            </el-col>

            <el-col class="change-action-box"><!-- 徒歩何分フォーム -->
                <el-col :span="24" tag="p" class="action-title">駅徒歩</el-col>
                <div>
                    <el-col :span="24" tag="p">
                        <el-select class="select-width" v-model="selectedOption.transportTimeNum" placeholder="上限なし" clearable @change="changeTransportTimeNum">
                            <el-option v-for="item in sideQuery.transportTime" :label="item.label" :value="item.value" :key="item.value"></el-option>
                        </el-select>
                    </el-col>
                </div>
            </el-col>

            <el-col class="change-action-box"><!-- フリーワード検索フォーム -->
                <div>
                    <el-col :span="24" tag="p">
                        <el-input class="select-width" v-model="queryValue.freeword" placeholder="フリーワード検索" prefix-icon="el-icon-search"></el-input>
                    </el-col>
                </div>
            </el-col>

            <el-col class="search-btn-box"><!-- 検索ボタン -->
                <el-button class="search-btn" type="info" icon="el-icon-search" round @click="searchClick">この条件で検索</el-button>
            </el-col>

        </el-row>
    </el-aside>

    <!-- 768px未満のときに表示されるもの -->
    <el-button class="condition-dialog-btn" icon="el-icon-search" @click="conditionDialog = true">条件変更</el-button>

    <!-- 上記のボタン押下後、下記のダイアログを表示 中身は768px以上の時と全く同じ（cssも一緒） -->
    <el-dialog :visible.sync="conditionDialog" width="85%">
        <el-row class="condition-title-box">
            <el-col class="condition-title-box-child">
                <p class="change-title-sentence">検索条件の</p>
                <p class="change-title-sentence">変更・追加</p>
            </el-col>
        </el-row>

        <!-- 検索条件を実際に選択するところ -->
        <el-row class="condition-action-box">
            <el-col class="change-action-box"><!-- 価格フォーム -->
                <?php if($_GET['puse'] == 0): ?><!-- 借りるなら「賃料」に買うなら「価格」に -->
                <el-col :span="24" tag="p" class="action-title">賃料</el-col>
                <?php else: ?>
                <el-col :span="24" tag="p" class="action-title">価格</el-col>
                <?php endif ?>
                <div>
                    <el-col :span="10" tag="p">
                        <el-select v-model="selectedOption.priceLowNum" placeholder="下限なし" clearable @change="changePriceNum('0')">
                            <!-- 上限の価格より小さい、または、上限の価格が未選択の時に表示 -->
                            <?php if($_GET['puse'] == 0): ?><!-- puseが0なら賃貸の価格表示を -->
                                <el-option v-for="item in sideQuery.pricePuse0" v-if="item.value <= queryValue.estate_priceHigh || selectedOption.priceHighNum == ''" :label="item.label" :value="item.value" :key="item.value"></el-option>
                            <?php else: ?><!-- puseが1なら購入の価格表示を -->
                                <el-option v-for="item in sideQuery.pricePuse1" v-if="item.value <= queryValue.estate_priceHigh || selectedOption.priceHighNum == ''" :label="item.label" :value="item.value" :key="item.value"></el-option>
                            <?php endif ?>
                        </el-select>
                    </el-col>
                    <el-col :span="4" tag="p" class="middle-border">〜</el-col>
                    <el-col :span="10" tag="p">
                        <el-select v-model="selectedOption.priceHighNum" placeholder="上限なし" clearable @change="changePriceNum('1')">
                            <!-- 下限の価格より大きい、または、下限の価格が未選択の時に表示 -->
                            <?php if($_GET['puse'] == 0): ?>
                                <el-option v-for="item in sideQuery.pricePuse0" v-if="item.value >= queryValue.estate_priceLow || selectedOption.priceLowNum == ''" :label="item.label" :value="item.value" :key="item.value"></el-option>
                            <?php else: ?>
                                <el-option v-for="item in sideQuery.pricePuse1" v-if="item.value >= queryValue.estate_priceLow || selectedOption.priceLowNum == ''" :label="item.label" :value="item.value" :key="item.value"></el-option>
                            <?php endif ?>
                        </el-select>
                    </el-col>
                </div>
            </el-col>

            <el-col class="change-action-box"><!-- 間取りフォーム -->
                <el-col :span="24" tag="p" class="action-title">間取り</el-col>
                <div>
                    <el-col :span="8" tag="p" class="floorPlanCheckbox"><el-checkbox @change="checkFloorPlan" v-model="sideQuery.floorPlan[0].planBool">{{ sideQuery.floorPlan[0]['label'] }}</el-checkbox></el-col>
                    <el-col :span="8" tag="p" class="floorPlanCheckbox"><el-checkbox @change="checkFloorPlan" v-model="sideQuery.floorPlan[1].planBool">{{ sideQuery.floorPlan[1]['label'] }}</el-checkbox></el-col>
                    <el-col :span="8" tag="p" class="floorPlanCheckbox"><el-checkbox @change="checkFloorPlan" v-model="sideQuery.floorPlan[2].planBool">{{ sideQuery.floorPlan[2]['label'] }}</el-checkbox></el-col>
                    <el-col :span="8" tag="p" class="floorPlanCheckbox"><el-checkbox @change="checkFloorPlan" v-model="sideQuery.floorPlan[3].planBool">{{ sideQuery.floorPlan[3]['label'] }}</el-checkbox></el-col>
                    <el-col :span="8" tag="p" class="floorPlanCheckbox"><el-checkbox @change="checkFloorPlan" v-model="sideQuery.floorPlan[4].planBool">{{ sideQuery.floorPlan[4]['label'] }}</el-checkbox></el-col>
                    <el-col :span="8" tag="p" class="floorPlanCheckbox"><el-checkbox @change="checkFloorPlan" v-model="sideQuery.floorPlan[5].planBool">{{ sideQuery.floorPlan[5]['label'] }}</el-checkbox></el-col>
                    <el-col :span="8" tag="p" class="floorPlanCheckbox"><el-checkbox @change="checkFloorPlan" v-model="sideQuery.floorPlan[6].planBool">{{ sideQuery.floorPlan[6]['label'] }}</el-checkbox></el-col>
                    <el-col :span="8" tag="p" class="floorPlanCheckbox"><el-checkbox @change="checkFloorPlan" v-model="sideQuery.floorPlan[7].planBool">{{ sideQuery.floorPlan[7]['label'] }}</el-checkbox></el-col>
                    <el-col :span="8" tag="p" class="floorPlanCheckbox"><el-checkbox @change="checkFloorPlan" v-model="sideQuery.floorPlan[8].planBool">{{ sideQuery.floorPlan[8]['label'] }}</el-checkbox></el-col>
                    <el-col :span="8" tag="p" class="floorPlanCheckbox"><el-checkbox @change="checkFloorPlan" v-model="sideQuery.floorPlan[9].planBool">{{ sideQuery.floorPlan[9]['label'] }}</el-checkbox></el-col>
                    <el-col :span="8" tag="p" class="floorPlanCheckbox"><el-checkbox @change="checkFloorPlan" v-model="sideQuery.floorPlan[10].planBool">{{ sideQuery.floorPlan[10]['label'] }}</el-checkbox></el-col>
                    <el-col :span="8" tag="p" class="floorPlanCheckbox"><el-checkbox @change="checkFloorPlan" v-model="sideQuery.floorPlan[11].planBool">{{ sideQuery.floorPlan[11]['label'] }}</el-checkbox></el-col>
                    <el-col :span="8" tag="p"><el-checkbox @change="checkFloorPlan" v-model="sideQuery.floorPlan[12].planBool">{{ sideQuery.floorPlan[12]['label'] }}</el-checkbox></el-col>
                    <el-col :span="8" tag="p"><el-checkbox @change="checkFloorPlan" v-model="sideQuery.floorPlan[13].planBool">{{ sideQuery.floorPlan[13]['label'] }}</el-checkbox></el-col>
                </div>
            </el-col>

            <el-col class="change-action-box"><!-- 土地面積フォーム -->
                <el-col :span="24" tag="p" class="action-title">土地面積</el-col>
                <div>
                    <el-col :span="10" tag="p">
                        <el-select v-model="selectedOption.landAreaLowNum" placeholder="下限なし" clearable @change="changeLandAreaNum('0')">
                            <!-- 上限の価格より小さい、または、上限の価格が未選択の時に表示 -->
                            <el-option v-for="item in sideQuery.landArea" v-if="item.value <= queryValue.land_areaHigh || selectedOption.landAreaHighNum == ''" :label="item.label" :value="item.value" :key="item.value"></el-option>
                        </el-select>
                    </el-col>
                    <el-col :span="4" tag="p" class="middle-border">〜</el-col>
                    <el-col :span="10" tag="p">
                        <el-select v-model="selectedOption.landAreaHighNum" placeholder="上限なし" clearable @change="changeLandAreaNum('1')">
                            <!-- 下限の価格より大きい、または、下限の価格が未選択の時に表示 -->
                            <el-option v-for="item in sideQuery.landArea" v-if="item.value >= queryValue.land_areaLow || selectedOption.landAreaLowNum == ''" :label="item.label" :value="item.value" :key="item.value"></el-option>
                        </el-select>
                    </el-col>
                </div>
            </el-col>

            <el-col class="change-action-box"><!-- 建物、専有面積フォーム -->
                <el-col :span="24" tag="p" class="action-title">建物・専有面積</el-col>
                <div>
                    <el-col :span="10" tag="p">
                        <el-select v-model="selectedOption.buildingAreaLowNum" placeholder="下限なし" clearable @change="changeBuildingAreaNum('0')">
                            <!-- 上限の価格より小さい、または、上限の価格が未選択の時に表示 -->
                            <el-option v-for="item in sideQuery.buildingArea" v-if="item.value <= queryValue.building_areaHigh || selectedOption.buildingAreaHighNum == ''" :label="item.label" :value="item.value" :key="item.value"></el-option>
                        </el-select>
                    </el-col>
                    <el-col :span="4" tag="p" class="middle-border">〜</el-col>
                    <el-col :span="10" tag="p">
                        <el-select v-model="selectedOption.buildingAreaHighNum" placeholder="上限なし" clearable @change="changeBuildingAreaNum('1')">
                            <!-- 下限の価格より大きい、または、下限の価格が未選択の時に表示 -->
                            <el-option v-for="item in sideQuery.buildingArea" v-if="item.value >= queryValue.building_areaLow || selectedOption.buildingAreaLow == ''" :label="item.label" :value="item.value" :key="item.value"></el-option>
                        </el-select>
                    </el-col>
                </div>
            </el-col>

            <el-col class="change-action-box"><!-- 徒歩何分フォーム -->
                <el-col :span="24" tag="p" class="action-title">駅徒歩</el-col>
                <div>
                    <el-col :span="24" tag="p">
                        <el-select class="select-width" v-model="selectedOption.transportTimeNum" placeholder="上限なし" clearable @change="changeTransportTimeNum">
                            <el-option v-for="item in sideQuery.transportTime" :label="item.label" :value="item.value" :key="item.value"></el-option>
                        </el-select>
                    </el-col>
                </div>
            </el-col>

            <el-col class="change-action-box"><!-- フリーワード検索フォーム -->
                <div>
                    <el-col :span="24" tag="p">
                        <el-input class="select-width" v-model="queryValue.freeword" placeholder="フリーワード検索" prefix-icon="el-icon-search"></el-input>
                    </el-col>
                </div>
            </el-col>

            <el-col class="search-btn-box"><!-- 検索ボタン -->
                <el-button class="search-btn" type="primary" icon="el-icon-search" plain round @click="searchClick">この条件で検索</el-button>
            </el-col>

        </el-row>
    </el-dialog><!-- ここまでダイアログ -->


    <?php endif ?>

    <el-main class="main">

        <el-row>
            <el-col class="page-title">検索結果</el-col>

            <!-- 該当する物件が存在しない時の処理 -->
            <?php if($total_house_count == 0 || $total_house_count == null): ?>
            <el-col tag="p" class="not-found">お探しの条件に一致する物件はございませんでした</el-col>

            <?php else: ?><!-- あったら -->
            <el-col :span="24" class="found-count">
                <p>該当する物件が<span class="found"><?php echo $total_house_count; ?></span>件見つかりました</p>
            </el-col>
            <el-col class="order-btn-box">
                <el-col :sm="24" :md="18" class="order-list">
                    <el-row type="left" justify="space-between">
                        <el-col :span="4" class="order-btn"><el-button size="small" class="order-el-btn" @click="orderClick('pickup')" <?php if($el_button_disabled[0] != null) { echo "type='info'"; } else {} ?>>オススメ</el-col>
                        <el-col :span="4" class="order-btn"><el-button size="small" class="order-el-btn" @click="orderClick('new')" <?php if($el_button_disabled[1] != null) { echo "type='info'";} else {} ?>>新着順</el-col>
                        <el-col :span="4" class="order-btn"><el-button size="small" class="order-el-btn" @click="orderClick('low')" <?php if($el_button_disabled[2] != null) { echo "type='info'";} else {} ?>>安い順</el-col>
                        <el-col :span="4" class="order-btn"><el-button size="small" class="order-el-btn" @click="orderClick('high')" <?php if($el_button_disabled[3] != null) { echo "type='info'";} else {} ?>>高い順</el-col>
                    </el-row>
                </el-col>
    
                <div class="transport-box">
                    <el-col :xs="10" :sm="10" :md="6" class="transport-time-select">
                            <el-select size="small" class="select-width" v-model="selectedOption.limitNum" placeholder="表示件数" @change="limitClick">
                                <el-option v-for="item in sideQuery.limit" :label="item.label" :value="item.value" :key="item.value"></el-option>
                            </el-select>
                    </el-col>
                </div>
            </el-col>
            <?php endif ?>
        </el-row>

        <!-- 該当する物件がなかったら表示しない -->
        <?php if($total_house_count != 0 || $total_house_count != null): ?>
        <el-row>
            <!-- 物件を表示する時のテンプレ -->
            <?php for($i = 0; $i < $use_id_count; $i++): ?>
            <el-col class="house-box">
                <?php
                    if($one_week_ago < $house_infos[$i]['update_datetime']){
                ?>
                <div class="ribbon-content">
                    <span class="ribbon">新着</span>
                </div>
                <?php
                    }
                ?>
                <el-col class="house-name">
                    <p style="margin-right: 10px;"><?php echo $tag_infos[$i]; ?></p>
                    <a href="./details.php?estate_code=<?php echo $house_infos[$i]['estate_code']; ?>"><?php echo $house_infos[$i]['estate_name']; ?></a>
                </el-col>
                <el-col class="house-info">
                    <!-- 画像情報 -->
                    <el-col :sm="24" :md="10" class="img-box">
                        <!-- メイン写真 -->
                        <el-col :span="24" class="main-photo-box">
                            <p class="main-img-parent">
                                <img src="<?php echo $main_url_arr[$i]; ?>" alt="物件のメイン写真">
                            </p>
                        </el-col>
                        
                        <!-- サブ写真 -->
                        <el-col :xs="0" :sm="0" :md="24" class="sub-photo-box">
                            <p class="sub-img-parent">
                                <img src="<?php echo $sub_url_arr[$i][0]; ?>" alt="">
                            </p>
                            <p class="sub-img-parent">
                                <img src="<?php echo $sub_url_arr[$i][1]; ?>" alt="">
                            </p>
                        </el-col>
                    </el-col>

                    <!-- テキスト情報 -->    
                    <el-col :sm="24" :md="14">
                        <el-col :span="24" class="house-description"><?php echo $house_infos[$i]['description']; ?></el-col>

                        <el-col :span="24" class="price-tag-box">
                            <el-col :span="6" class="list-item-title">
                                <?php
                                    if($_GET['puse'] == 1){ 
                                        echo "価格";
                                    }
                                    elseif ($_GET['puse'] == 0) {
                                        echo "賃料";
                                    }
                                ?>
                            </el-col>
                            <el-col :span="17" :offset="1" class="price">
                                <span class="price-num"><?php echo $house_infos[$i]['estate_price']; ?></span><span class="price-end">万円</span>
                            </el-col>
                        </el-col>

                        <el-col :span="24" class="list-item"><!-- 管理費 -->
                            <el-col :span="6" class="list-item-title">管理費</el-col>
                            <el-col :span="17" :offset="1" class="list-item-sentence"><?php echo $house_infos[$i]['management_fee']; ?></el-col>
                        </el-col>

                        <el-col :span="24" class="list-item"><!-- 敷金/礼金 -->
                            <el-col :span="6" class="list-item-title">敷金/礼金</el-col>
                            <el-col :span="17" :offset="1" class="list-item-sentence"><?php echo $house_infos[$i]['security_dep']; ?> / <?php echo $house_infos[$i]['key_money']; ?></el-col>
                        </el-col>

                        <el-col :span="24" class="list-item"><!-- 間取り -->
                            <el-col :span="6" class="list-item-title">間取り</el-col>
                            <el-col :span="17" :offset="1" class="list-item-sentence"><?php if($house_infos[$i]['estate_kbn'] != 0) {echo $house_infos[$i]['floor_plan_num']; echo $house_infos[$i]['floor_plan_kbn'];} else {echo '-';} ?></el-col>
                        </el-col>

                        <el-col :span="24" class="list-item"><!-- 交通 -->
                            <el-col :span="6" class="list-item-title">交通</el-col>
                            <el-col :span="17" :offset="1" class="list-item-sentence">
                                <el-col :span="24"><?php echo $transport_infos[$i]['transport_station']; ?> 徒歩<?php echo $transport_infos[$i]['transport_time']; ?>分</el-col>
                            </el-col>
                        </el-col>

                        <el-col :span="24" class="list-item"><!-- 所在地 -->
                            <el-col :span="6" class="list-item-title">所在地</el-col>
                            <el-col :span="17" :offset="1" class="list-item-sentence"><?php echo $house_infos[$i]['city_kbn']; ?><?php echo $house_infos[$i]['address_detail']; ?></el-col>
                        </el-col>

                        <!-- 詳細を見るボタン -->
                        <el-col :span="24" class="move-detail-page">
                            <el-link class="detail-link" href="./details.php?estate_code=<?php echo $house_infos[$i]['estate_code']; ?>" :underline="false" type="info"><el-button class="detail-button" icon="el-icon-document">詳細を見る</el-button></el-link>
                        </el-col>

                    </el-col>
                </el-col>
            </el-col>
            <?php endfor ?>
        </el-row>

        <el-row class="paging">
            <el-col>
                <!-- <el-pagination background layout="prev, pager, next" :total="" :page-size.sync="" :current-page=""></el-pagination> -->
                <el-pagination :current-page.sync="usePagination.currentPage" :page-size.sync="usePagination.pageSize" layout="prev, pager, next" :total="usePagination.total" background @current-change="paginationClick"></el-pagination>
            </el-col>
        </el-row>

        <?php endif ?>
    
    </el-main>

</el-container>

</el-col>



<!-- footerが入る <footer> 〜 </footer>が入る==== -->
<?php include './footer.php'; ?>
<!-- ========================================== -->

<script>
    let app = new Vue({
        el: "#app",
        data: {
            drawer: false,  //ハンバーガーメニューのためのプロパティ
            puse: 0, //最初の読み込み時に賃貸（0）か購入（1）かきめるフラグ
            conditionDialog: false,  //768px未満のときの検索条件ダイアログを表示するためのbool

            searchBasicUrl: "<?php echo $search_basic_url; ?>",  //表示件数が選択された時に使用するURL
            limitBasicUrl: "<?php echo $limit_basic_url; ?>",  //表示件数が選択された時に使用するURL
            paginationBasicUrl: "<?php echo $pagination_basic_url; ?>",  //ページネーションが選択された時に使用するURL
            orderBasicUrl: "<?php echo $order_basic_url; ?>",  //表示順序が選択された時に使用するURL
            nextUrl: "",  //動的に作成される次のURLを保存する

            usePagination: {  //ページネーションの表示に使用する
                currentPage: <?php if(isset($_GET['page'])){ echo (int)$_GET['page'] + 1;} else { echo 1; } ?>,  //$_GET['page']が送られていたら受け取る なければ初期は1ページなので1にする また数値でしか指定できないので文字列は変換する（キャスト使用）
                pageSize: <?php if(isset($_GET['limit'])) { echo (int)$_GET['limit']; } else { echo 10;} ?>,  //limit情報があったら数値変換を行い表示 ない場合は初期値である10を表示
                total: <?php if(isset($total_house_count)) { echo (int)$total_house_count; } else { echo $total_house_count; } ?>  //全部で物件がいくつあるかを入れる
            },

            selectedOption: {  //このページに飛んだ時にセレクトがセットされていたら反映させる（表示用）処理用はqueryValueの中にある~~~Numというプロパティ
                priceLowNum: "<?php if(isset($_GET['estate_priceLow'])) { echo $_GET['estate_priceLow'] . '万円';} ?>",  //表示用 価格下限を保存する 基本は数字が入るが前回の条件の「3万円」などの文字列が入ることもある
                priceHighNum: "<?php if(isset($_GET['estate_priceHigh'])) { echo $_GET['estate_priceHigh'] . '万円';} ?>",  //表示用 価格上限を保存する  基本は数字が入るが前回の条件の「3万円」などの文字列が入ることもある
                landAreaLowNum: "<?php if(isset($_GET['land_areaLow'])) { echo $_GET['land_areaLow'] . '㎡';} ?>",  //表示用 土地面積下限を保存する 基本は数字が入るが前回の条件の「50㎡」などの文字列が入ることもある
                landAreaHighNum: "<?php if(isset($_GET['land_areaHigh'])) { echo $_GET['land_areaHigh'] . '㎡';} ?>",  //表示用 土地面積上限を保存する 基本は数字が入るが前回の条件の「50㎡」などの文字列が入ることもある
                buildingAreaLowNum: "<?php if(isset($_GET['building_areaLow'])) { echo $_GET['building_areaLow'] . '㎡';} ?>",  //表示用 建物、専有面積下限を保存する  基本は数字が入るが前回の条件の「50㎡」などの文字列が入ることもある
                buildingAreaHighNum: "<?php if(isset($_GET['building_areaHigh'])) { echo $_GET['building_areaHigh'] . '㎡';} ?>",  //表示用 建物、専有面積上限を保存する  基本は数字が入るが前回の条件の「50㎡」などの文字列が入ることもある
                transportTimeNum: "<?php if(isset($_GET['transport_time'])) { echo $_GET['transport_time'] . '分以内';} ?>",  //表示用 基本は数字が入るが前回の条件の「3分以内」などの文字列が入ることもある
                limitNum: "<?php if(isset($_GET['limit'])){ echo $_GET['limit'] . '件表示';} ?>",  //表示用 基本は数字が入るが前回の条件の「30件表示」などの文字列が入ることもある

            },
            queryValue: {  //これを元に次のクエリ文を作る そのためプロパティ名は全てURL名に使用する名前と同じ
                //下記の情報は追加条件の指定に使うデータ
                estate_priceLow: "<?php if(isset($_GET['estate_priceLow'])) { echo $_GET['estate_priceLow'];} ?>",  //処理用 価格下限を保存する 数字しか入らない これを基準にセレクトオプションの表示とリンクの作成を行う
                estate_priceHigh: "<?php if(isset($_GET['estate_priceHigh'])) { echo $_GET['estate_priceHigh'];} ?>",  //処理用 価格上限を保存する 数字しか入らない これを基準にセレクトオプションの表示とリンクの作成を行う

                land_areaLow: "<?php if(isset($_GET['land_areaLow'])) { echo $_GET['land_areaLow'];} ?>",  //処理用 土地面積下限を保存する 数字しか入らない これを基準にセレクトオプションの表示とリンクの作成を行う
                land_areaHigh: "<?php if(isset($_GET['land_areaHigh'])) { echo $_GET['land_areaHigh'];} ?>",  //処理用 土地面積上限を保存する 数字しか入らない これを基準にセレクトオプションの表示とリンクの作成を行う

                building_areaLow: "<?php if(isset($_GET['building_areaLow'])) { echo $_GET['building_areaLow'];} ?>",  //処理用 建物、専有面積下限を保存する 数字しか入らない これを基準にセレクトオプションの表示とリンクの作成を行う
                building_areaHigh: "<?php if(isset($_GET['building_areaHigh'])) { echo $_GET['building_areaHigh'];} ?>",  //処理用 建物、専有面積上限を保存する 数字しか入らない これを基準にセレクトオプションの表示とリンクの作成を行う

                transport_time: "<?php if(isset($_GET['transport_time'])) { echo $_GET['transport_time'];} ?>",  //処理用 価格下限を保存する 数字しか入らない これを基準にセレクトオプションの表示とリンクの作成を行う

                floor_plan: null,

                limit: "<?php if(isset($_GET['limit'])){ echo $_GET['limit'];} ?>",  //処理用 数字しか入らない これを基準にセレクトオプションの表示とリンクの作成を行う
                order: "<?php if(isset($_GET['order'])){ echo $_GET['order'];} ?>",  //処理用 文字列しか入らない これを基準にセレクトオプションの表示とリンクの作成を行う
                page: "<?php if(isset($_GET['page'])){ echo $_GET['page'];} ?>",  //処理用 数字しか入らない これを基準にセレクトオプションの表示とリンクの作成を行う

                freeword: "<?php if(isset($_GET['freeword'])){ echo $_GET['freeword'];} ?>",

                //上記以外の情報をあれば受け取る
                este: "<?php if(isset($_GET['este'])) { echo $_GET['este'];} ?>",
                city: "<?php if(isset($_GET['city'])) { echo $_GET['city'];} ?>",
                feed: "<?php if(isset($_GET['feed'])) { echo $_GET['feed'];} ?>",
                seed: "<?php if(isset($_GET['seed'])) { echo $_GET['seed'];} ?>",
                keey: "<?php if(isset($_GET['keey'])) { echo $_GET['keey'];} ?>",
                floor_plan: "<?php if(isset($_GET['floor_plan'])) { echo $_GET['floor_plan'];} ?>",
                age: "<?php if(isset($_GET['age'])) { echo $_GET['age'];} ?>",
                pang: "<?php if(isset($_GET['pang'])) { echo $_GET['pang'];} ?>",
                bile: "<?php if(isset($_GET['bile'])) { echo $_GET['bile'];} ?>",
                baed: "<?php if(isset($_GET['baed'])) { echo $_GET['baed'];} ?>",
                auck: "<?php if(isset($_GET['auch'])) { echo $_GET['auch'];} ?>",
                pet: "<?php if(isset($_GET['pet'])) { echo $_GET['pet'];} ?>",
                fole: "<?php if(isset($_GET['fole'])) { echo $_GET['fole'];} ?>",
                inee: "<?php if(isset($_GET['inee'])) { echo $_GET['inee'];} ?>",
                denk: "<?php if(isset($_GET['denk'])) { echo $_GET['denk'];} ?>",
                inry: "<?php if(isset($_GET['inry'])) { echo $_GET['inry'];} ?>",
                frnt: "<?php if(isset($_GET['frnt'])) { echo $_GET['frnt'];} ?>",
                reed: "<?php if(isset($_GET['reed'])) { echo $_GET['reed'];} ?>",
                reon: "<?php if(isset($_GET['reon'])) { echo $_GET['reon'];} ?>",
                apce: "<?php if(isset($_GET['apce'])) { echo $_GET['apce'];} ?>",
                guor: "<?php if(isset($_GET['guor'])) { echo $_GET['guor'];} ?>",
                shre: "<?php if(isset($_GET['shre'])) { echo $_GET['shre'];} ?>",
                ders: "<?php if(isset($_GET['ders'])) { echo $_GET['ders'];} ?>",
                prht: "<?php if(isset($_GET['prht'])) { echo $_GET['prht'];} ?>",
                floor_num_a: "<?php if(isset($_GET['floor_num_a'])) { echo $_GET['floor_num_a'];} ?>",
                floor_num_b: "<?php if(isset($_GET['floor_num_b'])) { echo $_GET['floor_num_b'];} ?>",
                floor_num_c: "<?php if(isset($_GET['floor_num_c'])) { echo $_GET['floor_num_c'];} ?>",
                aion: "<?php if(isset($_GET['aion'])) { echo $_GET['aion'];} ?>",
                flat: "<?php if(isset($_GET['flat'])) { echo $_GET['flat'];} ?>",
                mone: "<?php if(isset($_GET['mone'])) { echo $_GET['mone'];} ?>",
                flng: "<?php if(isset($_GET['flng'])) { echo $_GET['flng'];} ?>",
                elor: "<?php if(isset($_GET['elor'])) { echo $_GET['elor'];} ?>",
                sera: "<?php if(isset($_GET['sera'])) { echo $_GET['sera'];} ?>",
                deox: "<?php if(isset($_GET['deox'])) { echo $_GET['deox'];} ?>",
                coen: "<?php if(isset($_GET['coen'])) { echo $_GET['coen'];} ?>",
                elve: "<?php if(isset($_GET['elve'])) { echo $_GET['elve'];} ?>",
                muve: "<?php if(isset($_GET['muve'])) { echo $_GET['muve'];} ?>",
                syen: "<?php if(isset($_GET['syen'])) { echo $_GET['syen'];} ?>",
                gale: "<?php if(isset($_GET['gale'])) { echo $_GET['gale'];} ?>",
                shet: "<?php if(isset($_GET['shet'])) { echo $_GET['shet'];} ?>",
                reat: "<?php if(isset($_GET['reat'])) { echo $_GET['reat'];} ?>",
                bary: "<?php if(isset($_GET['bary'])) { echo $_GET['bary'];} ?>",
                sher: "<?php if(isset($_GET['sher'])) { echo $_GET['sher'];} ?>",
                coom: "<?php if(isset($_GET['coom'])) { echo $_GET['coom'];} ?>",
                roth: "<?php if(isset($_GET['roth'])) { echo $_GET['roth'];} ?>",
                mate: "<?php if(isset($_GET['mate'])) { echo $_GET['mate'];} ?>",
                loft: "<?php if(isset($_GET['loft'])) { echo $_GET['loft'];} ?>",
                catv: "<?php if(isset($_GET['catv'])) { echo $_GET['catv'];} ?>",
                csna: "<?php if(isset($_GET['csna'])) { echo $_GET['csna'];} ?>",
                bsna: "<?php if(isset($_GET['bsna'])) { echo $_GET['bsna'];} ?>",
                woly: "<?php if(isset($_GET['woly'])) { echo $_GET['woly'];} ?>",
                elok: "<?php if(isset($_GET['elok'])) { echo $_GET['elok'];} ?>",
                neke: "<?php if(isset($_GET['neke'])) { echo $_GET['neke'];} ?>",
                imle: "<?php if(isset($_GET['imle'])) { echo $_GET['imle'];} ?>",
                bree: "<?php if(isset($_GET['bree'])) { echo $_GET['bree'];} ?>",
                maty: "<?php if(isset($_GET['maty'])) { echo $_GET['maty'];} ?>"
            },
            sideQuery: {
                pricePuse0: [  //賃貸用の賃料data
                    {label: "3万円", value: 3},
                    {label: "4万円", value: 4},
                    {label: "5万円", value: 5},
                    {label: "6万円", value: 6},
                    {label: "7万円", value: 7},
                    {label: "8万円", value: 8},
                    {label: "9万円", value: 9},
                    {label: "10万円", value: 10},
                    {label: "11万円", value: 11},
                    {label: "12万円", value: 12},
                    {label: "13万円", value: 13},
                    {label: "14万円", value: 14},
                    {label: "15万円", value: 15},
                ], 
                pricePuse1: [  //購入用の価格data
                    {label: "1000万円", value: 1000},
                    {label: "2000万円", value: 2000},
                    {label: "3000万円", value: 3000},
                    {label: "4000万円", value: 4000},
                    {label: "5000万円", value: 5000},
                    {label: "6000万円", value: 6000},
                    {label: "7000万円", value: 7000},
                    {label: "8000万円", value: 8000},
                    {label: "9000万円", value: 9000},
                    {label: "1億円", value: 10000},
                ], 
                floorPlan: [  //間取りdata  押されたらplanBoolがtrueになる
                    {planBool: <?php var_export($floor_plan_bool[0][0]); ?>, label: "１R", value: "1,1"},
                    {planBool: <?php var_export($floor_plan_bool[1][0]); ?>, label: "１K", value: "1,2"},
                    {planBool: <?php var_export($floor_plan_bool[2][0]); ?>, label: "１DK", value: "1,3"},
                    {planBool: <?php var_export($floor_plan_bool[3][0]); ?>, label: "１LDK", value: "1,4"},
                    {planBool: <?php var_export($floor_plan_bool[4][0]); ?>, label: "２K", value: "2,2"},
                    {planBool: <?php var_export($floor_plan_bool[5][0]); ?>, label: "２DK", value: "2,3"},
                    {planBool: <?php var_export($floor_plan_bool[6][0]); ?>, label: "２LDK", value: "2,4"},
                    {planBool: <?php var_export($floor_plan_bool[7][0]); ?>, label: "３K", value: "3,2"},
                    {planBool: <?php var_export($floor_plan_bool[8][0]); ?>, label: "３DK", value: "3,3"},
                    {planBool: <?php var_export($floor_plan_bool[9][0]); ?>, label: "３LDK", value: "3,4"},
                    {planBool: <?php var_export($floor_plan_bool[10][0]); ?>, label: "４K", value: "4,2"},
                    {planBool: <?php var_export($floor_plan_bool[11][0]); ?>, label: "４DK", value: "4,3"},
                    {planBool: <?php var_export($floor_plan_bool[12][0]); ?>, label: "４LDK", value: "4,4"},
                    {planBool: <?php var_export($floor_plan_bool[13][0]); ?>, label: "５K以上", value: "5,5"}
                ], 
                landArea: [  //土地面積下限data
                    {label: "50㎡", value: 50},
                    {label: "60㎡", value: 60},
                    {label: "70㎡", value: 70},
                    {label: "80㎡", value: 80},
                    {label: "90㎡", value: 90},
                    {label: "100㎡", value: 100},
                    {label: "110㎡", value: 110},
                    {label: "120㎡", value: 120},
                    {label: "130㎡", value: 130},
                    {label: "140㎡", value: 140},
                    {label: "150㎡", value: 150},
                    {label: "200㎡", value: 200},
                    {label: "300㎡", value: 300},
                    {label: "400㎡", value: 400},
                    {label: "500㎡", value: 500},
                ], 
                buildingArea: [  //建物、専有面積下限data
                    {label: "50㎡", value: 50},
                    {label: "60㎡", value: 60},
                    {label: "70㎡", value: 70},
                    {label: "80㎡", value: 80},
                    {label: "90㎡", value: 90},
                    {label: "100㎡", value: 100},
                    {label: "110㎡", value: 110},
                    {label: "120㎡", value: 120},
                    {label: "130㎡", value: 130},
                    {label: "140㎡", value: 140},
                    {label: "150㎡", value: 150},
                    {label: "200㎡", value: 200},
                    {label: "300㎡", value: 300},
                    {label: "400㎡", value: 400},
                    {label: "500㎡", value: 500},
                ], 
                transportTime: [  //駅徒歩data
                    {label: "3分以内", value: 3},
                    {label: "5分以内", value: 5},
                    {label: "10分以内", value: 10},
                    {label: "15分以内", value: 15},
                    {label: "20分以内", value: 20}
                ], 
                limit: [  //表示件数data
                    {label: "10件表示", value: 10},
                    {label: "20件表示", value: 20},
                    {label: "30件表示", value: 30},
                    {label: "40件表示", value: 40},
                    {label: "50件表示", value: 50}
                ], 

            }
        },
        methods: {
            changePriceNum(flg) {  //セレクトが変更された時にqueryValueのestate_priceLowかestate_priceHighを変更するメソッド
                if(flg == 0) {  //0ならlowの値の変更
                    this.queryValue.estate_priceLow = this.selectedOption.priceLowNum;
                } else {  //それ以外ならhighの値の変更
                    this.queryValue.estate_priceHigh = this.selectedOption.priceHighNum;
                }
            },
            checkFloorPlan(val) {  //間取りが選択された時にqueryValueのfloor_planを
                let floor_plan_count = null;  //sideQuery.floorPlanの要素数を入れるための変数
                floor_plan_count = this.sideQuery.floorPlan.length;  //要素数を代入
                let items = [];  //配列 これをあとでqueryValueのfloor_planに入れる
                let item = null;
                for(let i = 0; i < floor_plan_count; i++) {  //sideQueryのfloorPlanを全て回す
                    if(this.sideQuery.floorPlan[i].planBool) {  //floorPlanがtrueだったら
                        item = this.sideQuery.floorPlan[i].value;
                        items.push(item);
                    }
                }
                this.queryValue.floor_plan = items;  //for文で作られた配列をqueryValue.floor_planに反映
            },
            changeLandAreaNum(flg) {  //土地面積セレクトを押下した時に実行
                if(flg == 0) {  //0ならlowの値の変更
                    this.queryValue.land_areaLow = this.selectedOption.landAreaLowNum;
                } else {  //それ以外ならhighの値の変更
                    this.queryValue.land_areaHigh = this.selectedOption.landAreaHighNum;
                }
            },
            changeBuildingAreaNum(flg) {   //建物・専有面積セレクトを押下した時に実行
                if(flg == 0) {  //0ならlowの値の変更
                    this.queryValue.building_areaLow = this.selectedOption.buildingAreaLowNum;
                } else {  //それ以外ならhighの値の変更
                    this.queryValue.building_areaHigh = this.selectedOption.buildingAreaHighNum;
                }
            },
            changeTransportTimeNum() {  //駅徒歩セレクトが押下された時に実行
                this.queryValue.transport_time = this.selectedOption.transportTimeNum;
            },
            createUrl() {  //queryValueを元にURLを作成するメソッド → ./estatelist.php?puse=0&estate_priceLow=10 ~~~~~のようなリンクを作る
                for (let key in this.queryValue) {
                    if(this.queryValue[key] != "") {  //valueが空じゃない時だけ
                        this.nextUrl += `&${key}=${this.queryValue[key]}`;
                    }
                }
            },
            nextMove() {  //作られたURLを元に画面遷移する
                window.location.href = this.nextUrl;
            },

            //下記メソッドの4パターンで画面遷移が行われる
            searchClick() {  //1 「この条件で検索」を押下した時
                this.queryValue.page = 0;  //pageの数を0に

                this.nextUrl = this.searchBasicUrl;  //for文で次のURLを追記する
                this.createUrl();
                this.nextMove();
            },
            limitClick() {  //2 「表示件数」を変更した時
                this.queryValue.limit = this.selectedOption.limitNum;  //limitの数を変更 押された表示件数の数字に変更
                this.queryValue.page = 0;  //pageの数を0に

                //リンクの作成
                this.nextUrl = this.limitBasicUrl;  //ファイル上部で作ったlimitを除いたurlを代入
                this.nextUrl += `&limit=${this.queryValue.limit}&page=${this.queryValue.page}`;  //この処理ではURL末尾が &limit=10&page=0 などのようになるイメージ
                this.nextMove();
            },
            paginationClick(page) {  //3 「ページネーションを押下した時
                this.queryValue.page = page - 1;  //pageの変更 pageが0から始まっているので-1をする

                //リンクの作成
                this.nextUrl = this.paginationBasicUrl;  //ファイル上部で作ったorderを除いたurlを代入
                this.nextUrl += `&page=${this.queryValue.page}`;  //この処理ではURL末尾が &page=0 などのようになるイメージ
                
                this.nextMove();  //上記で作成したURLを元に画面遷移させる
            },
            orderClick(text) {  //4 表示順序を変更した時
                this.queryValue.order = text;  //orderの変更
                this.queryValue.page = 0;  //pageの数を0に

                //リンクの作成（検索条件は表示順序のみ変える）
                this.nextUrl = this.orderBasicUrl;  //ファイル上部で作ったorderを除いたurlを代入
                this.nextUrl += `&order=${this.queryValue.order}&page=${this.queryValue.page}`;  //この処理ではURL末尾が &order=high&page=0 のようになるイメージ
                
                this.nextMove();  //上記で作成したリンクを元に画面遷移させる
            }
        
        },
    });
    // window.onload = function() { 
    //     const body = document.getElementById("body");
    //     setTimeout(function() {
    //         body.style.display = "block";
    //     }, 100);
    // }
</script>

<link rel="stylesheet" type="text/css" href="../../www/css/estatelist.css">
