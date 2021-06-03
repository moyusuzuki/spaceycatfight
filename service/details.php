<?php
    //GetDataクラスの取得
    include './system/GetData.php';
    include './system/db_info.php';  //DB接続のため dbConnect関数呼び出し

    /**
     * @return array $result 接続して条件にあうデータを取得する
     */
    function getAllData($estate_data, $detail_data, $transport_data, $all_img_arr, $out_img_arr, $in_img_arr, $plan_img_arr, $other_img_arr, $not_found) {
        //データベース接続
        $dbh = dbConnect();
        $estate_code = $_GET['estate_code'];  //とりあえず条件をidのみとする 

        //t_estate t_estate_detail テーブル  公開かつ削除されていないことをここでも確認
        $sql = "SELECT * FROM t_estate INNER JOIN t_estate_detail ON t_estate.estate_id = t_estate_detail.estate_id WHERE t_estate_detail.estate_code = '{$estate_code}' AND t_estate.delete_flg = 0 AND t_estate.display_flg = 1;";
        $stmt = $dbh->query($sql);
        $detail_data = $stmt->fetchall(PDO::FETCH_ASSOC);
        $estate_id = $detail_data[0]['estate_id'];

        if(empty($detail_data)) {  //$detail_dataが空の時に変数に代入して、htmlでこの変数を使う
            $not_found = "お探しの物件はございませんでした。";
        } else {

            //t_estate_transport テーブル
            $sql = "SELECT * FROM t_estate_transport WHERE estate_id = {$estate_id} AND transport_delete_flg = 0 ORDER BY display_order ASC";
            $stmt = $dbh->query($sql);
            $transport_data = $stmt->fetchall(PDO::FETCH_ASSOC);
            

            //t_estate_photo テーブル 全て
            $sql = "SELECT photo_url FROM t_estate_photo WHERE estate_id = {$estate_id} AND delete_flg = 0 ORDER BY display_order ASC";
            $stmt = $dbh->query($sql);
            $all_img_arr = $stmt->fetchall(PDO::FETCH_ASSOC);

            //t_estate_photo テーブル　外観
            $sql = "SELECT photo_url FROM t_estate_photo WHERE estate_id = {$estate_id} AND delete_flg = 0 AND estate_photo_category = 0 ORDER BY display_order ASC";
            $stmt = $dbh->query($sql);
            $out_img_arr = $stmt->fetchall(PDO::FETCH_ASSOC);

            //t_estate_photo テーブル　内装
            $sql = "SELECT photo_url FROM t_estate_photo WHERE estate_id = {$estate_id} AND delete_flg = 0 AND estate_photo_category = 1 ORDER BY display_order ASC";
            $stmt = $dbh->query($sql);
            $in_img_arr = $stmt->fetchall(PDO::FETCH_ASSOC);

            //t_estate_photo テーブル　間取り
            $sql = "SELECT photo_url FROM t_estate_photo WHERE estate_id = {$estate_id} AND delete_flg = 0 AND estate_photo_category = 2 ORDER BY display_order ASC";
            $stmt = $dbh->query($sql);
            $plan_img_arr = $stmt->fetchall(PDO::FETCH_ASSOC);

            //t_estate_photo テーブル　その他
            $sql = "SELECT photo_url FROM t_estate_photo WHERE estate_id = {$estate_id} AND delete_flg = 0 AND estate_photo_category = 3 ORDER BY display_order ASC";
            $stmt = $dbh->query($sql);
            $other_img_arr = $stmt->fetchall(PDO::FETCH_ASSOC);
        }

        $dbh=null;

        return array($estate_data, $detail_data, $transport_data, $all_img_arr, $out_img_arr, $in_img_arr, $plan_img_arr, $other_img_arr, $not_found);
    }

    $not_found = null;  //これで物件データがあったか方法を探す
    $estate_data = null;  //estateデータ
    $detail_data = null;  //detail
    $transport_data = null;  //transport
    $all_img_arr = null;  //写真情報全てを入れる配列
    $out_img_arr = null;  //外観情報全てを入れる配列
    $in_img_arr = null;  //内装情報全てを入れる配列
    $plan_img_arr = null;  //間取り情報全てを入れる配列
    $other_img_arr = null;  //その他情報全てを入れる配列
    $all_img = [];  //全ての画像のurlのみを入れる配列
    $out_img = [];  //外観の画像のurlのみを入れる配列
    $in_img = [];  //内装の画像のurlのみを入れる配列
    $plan_img = [];  //間取りの画像のurlのみを入れる配列
    $other_img = [];  //その他の画像のurlのみを入れる配列

    //データベースから家の詳細情報取得
    list($estate_data, $detail_data, $transport_data, $all_img_arr, $out_img_arr, $in_img_arr, $plan_img_arr, $other_img_arr, $not_found) = getAllData($estate_data, $detail_data, $transport_data, $all_img_arr, $out_img_arr, $in_img_arr, $plan_img_arr, $other_img_arr, $not_found);
    
    for($i = 0; $i < count($all_img_arr); $i++) {  //urlを配列に入れる処理
        $all_img[] = $all_img_arr[$i]['photo_url'];
    }

    for($i = 0; $i < count($out_img_arr); $i++) {  //urlを配列に入れる処理
        $out_img[] = $out_img_arr[$i]['photo_url'];
    }

    for($i = 0; $i < count($in_img_arr); $i++) {  //urlを配列に入れる処理
        $in_img[] = $in_img_arr[$i]['photo_url'];
    }

    for($i = 0; $i < count($plan_img_arr); $i++) {  //urlを配列に入れる処理
        $plan_img[] = $plan_img_arr[$i]['photo_url'];
    }

    for($i = 0; $i < count($other_img_arr); $i++) {  //urlを配列に入れる処理
        $other_img[] = $other_img_arr[$i]['photo_url'];
    }
    
    //タグを格納するための配列
    $tag_info_array = array();

    //詳細画面下部に出力する情報を入れるための空の配列を用意
    $option_info_array = array();

    //タグ取得のためのクラス生成とメソッド使用
    $use_get_data = new GetData;
    $tag_info_array = $use_get_data->getTagPlain($detail_data, $tag_info_array);

    //関数を利用してクラスのメソッドに全て通す
    list($detail_data, $option_info_array) = useClass($detail_data, $option_info_array);

    //jsonで変換
    $j_all_img = json_encode($all_img);
    $j_out_img = json_encode($out_img);
    $j_in_img = json_encode($in_img);
    $j_plan_img = json_encode($plan_img);
    $j_other_img = json_encode($other_img);

?>


<!-- </header>までが入る ======================== -->
<?php 
    include "./header.php";
?>
<!-- ========================================== -->

<p class="link-map" style="padding: 20px;">
    <el-link href="./index.php" type="primary">ホーム</el-link><i class="el-icon-arrow-right"></i>
    <el-link v-if="ifPuse == 1" href="./conditions.php?puse=1" type="primary">物件を買う</el-link><i v-if="ifPuse == 1" class="el-icon-arrow-right"></i>
    <el-link v-if="ifPuse == 0" href="./conditions.php?puse=0" type="primary">物件を借りる</el-link><i v-if="ifPuse == 0" class="el-icon-arrow-right"></i>▲<?php echo $detail_data[0]['estate_name']; ?>▼
</p>


<?php if(!empty($not_found)): ?><!-- 物件コードが存在しない時表示する -->
    <el-main class="main">
        <p><?php echo $not_found; ?></p>
    </el-main>

<?php else: ?>
<el-main class="main">

    <!-- 紹介文と画像が入る　mainの一番上の項目 -->
    <el-row class="flex-two-items" type="flex" justify="space-between">
        


        <el-col :xs="24" :span="16" class="left">
            <!-- 紹介文 -->

            <el-col :span="24">
                <!-- 物件タイトル -->
                <?php echo $tag_info_array[0]; ?>
                <h3 class="intro-title-sentence"><?php echo $detail_data[0]['estate_name']; ?></h3>
            </el-col>

            <el-row>
                <!-- 情報更新日・次回更新予定日とタグが一つ入る -->
                <el-col :span="14">
                    <!-- 情報更新日・次回更新予定日 -->
                    <p class="update-date">情報更新日
                        <span
                            class="update-date-s update-first-span"><?php if(!empty($detail_data[0]['update_datetime'])){echo $detail_data[0]['update_datetime'];}else{echo date('Y/m/d',strtotime($detail_data[0]['registered_datetime']));}  ?></span>
                    </p>
                </el-col>
            </el-row>

            <el-col :span="24" class="three-list">
                <!-- 価格・交通・所在地 -->

                <el-col :span="24" class="list-menu list-menu-mb">
                    <!--価格 -->
                    <el-col :span="3">
                        <el-tag size="small" class="list-tag">価格</el-tag>
                    </el-col>
                    <el-col :span="15">
                        <span id="price"
                            class="three-list-span red big-money"><?php echo preg_replace('/\.?0+$/', '', number_format($detail_data[0]['estate_price'], 2)); ?></span>
                        <span class="money-after red">万円</span>
                    </el-col>
                </el-col>

                <el-col :span="24" class="list-menu list-menu-mb">
                    <!-- 交通 -->
                    <el-col :span="3">
                        <el-tag size="small" class="list-tag">交通</el-tag>
                    </el-col>
                    <el-col :span="15">
                        <span class="three-list-span"><?php echo $transport_data[0]['transport_station']; ?></span>
                        <span class="three-list-span">徒歩<?php echo $transport_data[0]['transport_time']; ?>分</span>
                    </el-col>
                </el-col>

                <el-col :span="24" class="list-menu">
                    <!-- 所在地 -->
                    <el-col :span="3">
                        <el-tag size="small" class="list-tag">所在地</el-tag>
                    </el-col>
                    <el-col :span="15">
                        <span class="three-list-span"><?php echo $detail_data[0]['city_kbn']; ?><?php echo $detail_data[0]['address_detail']; ?></span>
                    </el-col>
                </el-col>
            </el-col>

        </el-col><!-- 紹介文 .left -->
        <el-main class="send_btn">　    
            <el-button type="warning">
                <a href="../../src/service/details_contact.php">この物件について問い合わせる</a>
            </el-button>
        </el-main>

    </el-row>




        

    <!-- 画像の分類選択バー -->
    <el-menu class="img-list" default-active="1" mode="horizontal" text-color="white" active-text-color="#373434"
        background-color="#ED701A">

        <el-col :span="5" class="img-list-item">
            <el-menu-item index="1" class="img-list-li" @click="setAllImg">全て({{ imgNum.allImg }})</el-menu-item>
        </el-col>
        <el-col :span="4" class="img-list-item">
            <el-menu-item v-if="imgNum.outImg != 0" index="2" class="img-list-li" @click="setOutImg">外観({{ imgNum.outImg }})</el-menu-item>
            <el-menu-item v-else index="2" class="img-list-li" @click="setOutImg" disabled>外観({{ imgNum.outImg }})</el-menu-item>
        </el-col>
        <el-col :span="4" class="img-list-item">
            <el-menu-item v-if="imgNum.inImg != 0" index="3" class="img-list-li" @click="setInImg">内装({{ imgNum.inImg }})</el-menu-item>
            <el-menu-item v-else index="3" class="img-list-li" @click="setInImg" disabled>内装({{ imgNum.inImg }})</el-menu-item>
        </el-col>
        <el-col :span="6" class="img-list-item">
            <el-menu-item v-if="imgNum.planImg != 0" index="4" class="img-list-li" @click="setPlanImg">間取り・区画({{ imgNum.planImg }})</el-menu-item>
            <el-menu-item v-else index="4" class="img-list-li" @click="setPlanImg" disabled>間取り・区画({{ imgNum.planImg }})</el-menu-item>
        </el-col>
        <el-col :span="5" class="img-list-item img-list-item-last">
            <el-menu-item v-if="imgNum.otherImg != 0" index="5" class="img-list-li" @click="setOtherImg">その他({{ imgNum.otherImg }})</el-menu-item>
            <el-menu-item v-else index="5" class="img-list-li" @click="setOtherImg" disabled>その他({{ imgNum.otherImg }})</el-menu-item>
        </el-col>

    </el-menu>


    <!-- 画像が大きく表示される部分 -->
    <el-row type="flex" justify="center" class="big-image">
        <el-col :xs="4" @click="goLeft" class="slider-icon-left slider-item xs-move-item"><i @click="goLeft"
                class="el-icon-arrow-left icon"></i></el-col>

        <el-col :span="16" class="big-image-item"><img :src="bigImg" alt=""></el-col>

        <el-col :xs="4" @click="goRight" class="slider-icon-right slider-item xs-move-item"><i @click="goRight"
                class="el-icon-arrow-right icon"></i></el-col>
    </el-row>


    <!-- 画像の候補が出てくる 768px以下の時表示しない  -->
    <el-row class="img-slider" type="flex" justify="space-around">
        <el-col @click="goLeft" :span="1" class="slider-icon-left slider-item"><i @click="goLeft"
                class="el-icon-arrow-left icon"></i></el-col>

        <el-col :span="22" id="slider">
            <el-row id="clicked" type="flex" justify="space-around" class="four-slider">
                <el-col tag="p" class="four-slider-first" :span="5" v-for="item in selectImgPreview" :key="item">
                    <!-- スライダーで画像の表示 -->
                    <p class="img-item" @click="preMoveBig(item)"><img :src="item" alt=""></p>
                </el-col>
            </el-row>
        </el-col>

        <el-col @click="goRight" :span="1" class="slider-icon-right slider-item"><i @click="goRight"
                class="el-icon-arrow-right icon"></i></el-col>
    </el-row>


    <el-row class="home-details-info">
        <!-- 物件詳細情報が掲載される部分 -->
        <el-col :span="24">
            <h4 class="sub-title">物件詳細情報</h4>
        </el-col>


        <table border="1" class="table">
            <tr>
                <th><p>所在地</p></th>
                <td colspan="3"><p>千葉県<?php echo $detail_data[0]['city_kbn']; ?><?php echo $detail_data[0]['address_detail']; ?></p></td>
            </tr>
            <tr>
                <th><p>交通</p></th>
                <td colspan="3">
                    <?php foreach($transport_data as $value): ?>
                        <p><?php echo $value['transport_station']; ?>　徒歩<?php echo $value['transport_time']; ?>分　</span></p>
                    <?php endforeach ?>
                </td>
            </tr>
            <tr>
                <th><p>間取</p></th>
                <td colspan="3"><p><?php echo $detail_data[0]['floor_plan_num']; ?><?php echo $detail_data[0]['floor_plan_kbn']; ?></p></td>
            </tr>
            <tr>
                <th><p>土地面積</p></th>
                <td colspan="3"><p><?php echo $detail_data[0]['land_area']; ?></p></td>
            </tr>
            <tr>
                <th><p>建物面積</p></th>
                <td colspan="3"><p><?php echo $detail_data[0]['building_area']; ?></p></td>
            </tr>

            <!-- ここから下はスマホ以外の時の表示方法　------------------------------------------ -->
            <tr v-if="winWidth > 480">
                <th><p>構造・規模</p></th>
                <td>
                    <p>
                        <?php if($detail_data[0]['estate_kbn'] != '土地'): ?>
                            <?php echo $detail_data[0]['floor_num']; ?> / <?php echo $detail_data[0]['story_num']; ?>
                        <?php else: ?>
                            -
                        <?php endif ?>
                    </p>
                </td>
                <th><p>築年</p></th>
                <td><p><?php if(!empty($detail_data[0]['age'])){echo $detail_data[0]['age'];}else{echo '-';} ?></p></td>
            </tr>

            <tr v-if="winWidth > 480">
                <th><p>入居可能日</p></th>
                <td><p><?php echo $detail_data[0]['room_available_day']; ?></p></td>
                <th><p>保証人</p></th>
                <td><p><?php echo $detail_data[0]['guarantor_flg']; ?></p></td>
            </tr>

            <tr v-if="winWidth > 480">
                <th><p>敷金</p></th>
                <td><p><?php echo $detail_data[0]['security_dep']; ?></p></td>
                <th><p>礼金</p></th>
                <td><p><?php echo $detail_data[0]['key_money']; ?></p></td>
            </tr>

            <tr v-if="winWidth > 480">
                <th><p>管理費</p></th>
                <td><p><?php echo $detail_data[0]['management_fee']; ?></p></td>
                <th><p>共益費</p></th>
                <td><p><?php echo $detail_data[0]['condo_fee']; ?></p></td>
            </tr>

            <tr v-if="winWidth > 480">
                <th><p>駐車場</p></th>
                <td><p><?php echo $detail_data[0]['parking_fee']; ?></p></td>
                <th><p>駐輪場</p></th>
                <td><p><?php echo $detail_data[0]['bicycle_fee']; ?></p></td>
            </tr>

            <tr v-if="winWidth > 480">
                <th><p>小学校区</p></th>
                <td>
                    <p>
                        <?php if($detail_data[0]['elementary_school'] != null): ?>
                            <?php echo $detail_data[0]['elementary_school']; ?>
                        <?php else: ?>
                            -
                        <?php endif ?>
                    </p>
                </td>
                <th><p>中学校区</p></th>
                <td>
                    <p>
                        <?php if($detail_data[0]['middle_school'] != null): ?>
                            <?php echo $detail_data[0]['middle_school']; ?>
                        <?php else: ?>
                            -
                        <?php endif ?>
                    </p>
                </td>
            </tr>

            <tr v-if="winWidth > 480">
                <th><p>取引態様</p></th>
                <td><p><?php echo $detail_data[0]['conditions_kbn']; ?></p></td>
                <th><p>物件コード</p></th>
                <td><p><?php echo $detail_data[0]['estate_code']; ?></p></td>
            </tr>
            <!-- ここから上はスマホ以外の時の表示方法　------------------------------------------ -->

            <!-- ここから下はスマホの時の表示方法　------------------------------------------ -->
            <tr v-if="winWidth <= 480">
                <th><p>構造・規模</p></th>
                <td>
                    <p>
                        <?php if($detail_data[0]['estate_kbn'] != '土地'): ?>
                            <?php echo $detail_data[0]['floor_num']; ?> / <?php echo $detail_data[0]['story_num']; ?>
                        <?php else: ?>
                            -
                        <?php endif ?>
                    </p>
                </td>
            </tr>
            <tr v-if="winWidth <= 480">
                <th><p>築年</p></th>
                <td><p><?php if(!empty($detail_data[0]['age'])){echo $detail_data[0]['age'];}else{echo '-';} ?></p></td>
            </tr>

            <tr v-if="winWidth <= 480">
                <th><p>入居可能日</p></th>
                <td><p><?php echo $detail_data[0]['room_available_day']; ?></p></td>
            </tr>
            <tr v-if="winWidth <= 480">
                <th><p>保証人</p></th>
                <td><p><?php echo $detail_data[0]['guarantor_flg']; ?></p></td>
            </tr>

            <tr v-if="winWidth <= 480">
                <th><p>敷金</p></th>
                <td><p><?php echo $detail_data[0]['security_dep']; ?></p></td>
            </tr>
            <tr v-if="winWidth <= 480">
                <th><p>礼金</p></th>
                <td><p><?php echo $detail_data[0]['key_money']; ?></p></td>
            </tr>

            <tr v-if="winWidth <= 480">
                <th><p>管理費</p></th>
                <td><p><?php echo $detail_data[0]['management_fee']; ?></p></td>
            </tr>
            <tr v-if="winWidth <= 480">
                <th><p>共益費</p></th>
                <td><p><?php echo $detail_data[0]['condo_fee']; ?></p></td>
            </tr>

            <tr v-if="winWidth <= 480">
                <th><p>駐車場</p></th>
                <td><p><?php echo $detail_data[0]['parking_fee']; ?></p></td>
            </tr>
            <tr v-if="winWidth <= 480">
                <th><p>駐輪場</p></th>
                <td><p><?php echo $detail_data[0]['bicycle_fee']; ?></p></td>
            </tr>

            <tr v-if="winWidth <= 480">
                <th><p>小学校区</p></th>
                <td>
                    <p>
                        <?php if($detail_data[0]['elementary_school'] != null): ?>
                            <?php echo $detail_data[0]['elementary_school']; ?>
                        <?php else: ?>
                            -
                        <?php endif ?>
                    </p>
                </td>
            </tr>
            <tr v-if="winWidth <= 480">
                <th><p>中学校区</p></th>
                <td>
                    <p>
                        <?php if($detail_data[0]['middle_school'] != null): ?>
                            <?php echo $detail_data[0]['middle_school']; ?>
                        <?php else: ?>
                            -
                        <?php endif ?>
                    </p>
                </td>
            </tr>

            <tr v-if="winWidth <= 480">
                <th><p>取引態様</p></th>
                <td><p><?php echo $detail_data[0]['conditions_kbn']; ?></p></td>
            </tr>
            <tr v-if="winWidth <= 480">
                <th><p>物件コード</p></th>
                <td><p><?php echo $detail_data[0]['estate_code']; ?></p></td>
            </tr>
            <!-- ここから上はスマホの時の表示方法　------------------------------------------ -->

            

        </table>

    </el-row>


    <!-- おまけ情報 -->
    <el-row class="house-chara-box">
        <el-col :span="24">
            <h4 class="sub-title">部屋の特徴・設備</h4>
        </el-col>

        <el-col class="house-chara">
            <p>
                <?php foreach($option_info_array as $value): ?>
                <span><?php echo $value . "　"; ?></span>
                <?php endforeach ?>
            </p>
        </el-col>
    </el-row>


    <!--　地図表示 -->
    <el-row>
        <el-col :span="24">
            <h4 class="sub-title">周辺地図</h4>
        </el-col>
        <el-col>
            <el-row type="flex" justify="center" class="surrounding-map">
                <el-col :xs="24" :span="18" class="map-box">
                    <?php echo $detail_data[0]['simple_map']; ?>
                </el-col>
            </el-row>
        </el-col>
    </el-row>

</el-main>
<?php endif ?>

</el-col>

<!-- footerが入る <footer> 〜 </footer>が入る==== -->
<?php include './footer.php'; ?>
<!-- ========================================== -->

<!-- 自作のjs読み込み-->
<script>
let app = new Vue({
    el: "#app",
    data: {
        drawer: false,  //ハンバーガーメニューのためのプロパティ
        
        ifPuse: '<?php echo $detail_data[0]['purchase_flg']; ?>',
        winWidth: window.outerWidth,
        img: {
            //全ての画像をいれる
            allImg: <?php echo $j_all_img; ?>,
            outImg: <?php echo $j_out_img; ?>, //外観の画像をいれる
            inImg: <?php echo $j_in_img; ?>, //内観の画層を入れる
            planImg: <?php echo $j_plan_img; ?>, //間取りの画像を入れる
            otherImg: <?php echo $j_other_img; ?> //その他の画像を入れる
        },
        imgNum: {
            allImg: 0,
            outImg: 0,
            inImg: 0,
            planImg: 0,
            otherImg: 0
        },
        bigImgFlg: 0, //大きい画像を表示するためのフラグ []の中身
        selectImg: null, //imgの５種類のどれかを入れるための入れる　大きい画像の下にある4つの画像
        bigImg: null, //実際に画像のURLが一つだけ入るプロパティ  selectImg[bigImgFlg]で指定される

        selectImgNum: 0, //selectImgの要素が何個あるか
        selectImgPreview: [],
        bigImgForFlg: 0,
        preMoveBigFlg: 0,
        preClickFlg: 0, //クリックされ他時に配列の次の流れを決めるためのフラグ
        preBorderFlg: 0, //ボーダーの色決めのためのフラグ
        clickedItems: 0
    },
    methods: {
        imgNumCheck() { //最初に画像の種類別の数を表示するためのメソッド
            this.imgNum.allImg = this.img.allImg.length; //全て

            this.imgNum.outImg = this.img.outImg.length; //外観

            this.imgNum.inImg = this.img.inImg.length; //内容

            this.imgNum.planImg = this.img.planImg.length; //間取り

            this.imgNum.otherImg = this.img.otherImg.length; //その他

        },
        forSetFun() { //種類を選択された時に大きい画像と下の画像をセットするためのメソッド
            this.bigImgFlg = 0;
            this.bigImg = this.selectImg[this.bigImgFlg];
            this.selectImgNum = this.selectImg.length;
        },
        forMakeArray() { //下に表示される画像の処理メソッド
            this.bigImg = this.selectImg[this.bigImgFlg];
            this.selectImgPreview = [];
            this.bigImgForFlg = this.bigImgFlg;
            switch (this.selectImg.length - this.bigImgFlg) {
                case 1:
                    this.selectImgPreview.push(this.selectImg[this.bigImgFlg]);
                    break;
                case 2:
                    for (i = 0; i < 2; i++) {
                        if (i == 2) {
                            this.bigImgForFlg = this.bigImgFlg;
                            break;
                        }
                        this.selectImgPreview.push(this.selectImg[this.bigImgForFlg]);
                        this.bigImgForFlg++;
                    }
                    break;
                case 3:
                    for (i = 0; i < 3; i++) {
                        if (i == 3) {
                            this.bigImgForFlg = this.bigImgFlg;
                            break;
                        }
                        this.selectImgPreview.push(this.selectImg[this.bigImgForFlg]);
                        this.bigImgForFlg++;
                    }
                    break;
                default:
                    for (i = 0; i < this.selectImgNum; i++) {
                        if (i == 4) {
                            this.bigImgForFlg = this.bigImgFlg;
                            break;
                        }
                        this.selectImgPreview.push(this.selectImg[this.bigImgForFlg]);
                        this.bigImgForFlg++;
                    }
            }

        },
        setAllImg() { //
            if (this.img.allImg.length != 0) {
                this.selectImg = this.img.allImg;
                this.forSetFun(); //共通部分の処理関数実行  画像配列をセット
                this.forMakeArray(); //共通部分の処理関数実行　実際に表示する配列を作る
            }
        },
        setOutImg() {
            if (this.img.outImg.length != 0) {
                this.selectImg = this.img.outImg;
                this.forSetFun(); //共通部分の処理関数実行
                this.forMakeArray(); //共通部分の処理関数実行　実際に表示する配列を作る
            }
        },
        setInImg() {
            if (this.img.inImg.length != 0) {
                this.selectImg = this.img.inImg;
                this.forSetFun(); //共通部分の処理関数実行
                this.forMakeArray(); //共通部分の処理関数実行　実際に表示する配列を作る
            }
        },
        setPlanImg() {
            if (this.img.planImg.length != 0) {
                this.selectImg = this.img.planImg;
                this.forSetFun(); //共通部分の処理関数実行
                this.forMakeArray(); //共通部分の処理関数実行　実際に表示する配列を作る
            }
        },
        setOtherImg() {
            if (this.img.otherImg.length != 0) {
                this.selectImg = this.img.otherImg;
                this.forSetFun(); //共通部分の処理関数実行
                this.forMakeArray(); //共通部分の処理関数実行　実際に表示する配列を作る
            }
        },
        goLeft() {
            if (this.bigImgFlg != 0) { //bigImgFlgが0じゃないときIkkouIkkou
                this.bigImgFlg -= 1;
                this.forMakeArray();
            }
        },
        goRight() {
            if (this.bigImgFlg < this.selectImg.length - 1) {
                this.bigImgFlg += 1;
                this.forMakeArray();
                console.log(this.selectImgPreview);
            }
        },
        preMoveBig(value) { //画像が押された時に大きいものに反映させるためメソッド
            this.preMoveBigFlg = this.selectImg.indexOf(value); //引数を元に配列の中から該当のものを探し番号を返す
            this.bigImg = this.selectImg[this.preMoveBigFlg]; //大きく表示するためのセット
            this.bigImgFlg = this.preMoveBigFlg;
            // this.selectImgNum = this.selectImg.length; //selectImgの要素数もセット
            // this.con();
        },
        handleResize: function() {
            // resizeのたびにこいつが発火するので、ここでやりたいことをやる
            this.winWidth = window.innerWidth;
        }

    },
    mounted: function () {
        window.addEventListener('resize', this.handleResize)
    },
    beforeDestroy: function () {
        window.removeEventListener('resize', this.handleResize)
    }
})

//これより下のjsコードはインスタンス化した後のセット処理  この最初の設定がないとエラーおきます
app.bigImgFlg = 0; //フラグ
app.selectImg = app.img.allImg; //配列
app.bigImg = app.selectImg[app.bigImgFlg]; //大きく表示する画像
app.selectImgNum = app.selectImg.length; //配列の要素数を取得

//
app.bigImgForFlg = app.bigImgFlg; //表示する配列の作成　最大4つ
for (i = 0; i < app.selectImgNum; i++) {
    if (i == 4) { //配列は最大4つしか要素を入れたくないので5回目回ったら抜ける
        app.bigImgForFlg = app.bigImgFlg; //  bigImgForFlgをbigImgFlgを基準に初期化
        break;
    }
    app.selectImgPreview.push(app.selectImg[app.bigImgForFlg]);
    app.bigImgForFlg++;
}

//最初に画像の設定をする
window.onload = app.imgNumCheck;

</script>

<!-- 自作のcss読み込み -->
<link rel="stylesheet" href="../../www/css/details.css?<?php echo date('Ymd-His'); ?>">

</html>