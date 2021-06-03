<?php

    include './system/GetData.php';
    include './system/db_info.php';  //DB接続のため dbConnect関数呼び出し

    /**
     * @return array $result 接続して条件にあうデータを取得する
     */
    function getAllData() {
        //データベース接続
        $dbh = dbConnect();

        $sql = "SELECT * FROM t_estate_detail LEFT JOIN t_estate ON t_estate_detail.estate_id = t_estate.estate_id WHERE  t_estate.display_flg = 1 AND t_estate.delete_flg = 0 AND t_estate_detail.pickup_flg = 1 ORDER BY t_estate_detail.registered_datetime DESC LIMIT 3;";
    
        $stmt = $dbh->query($sql);

        $result = $stmt->fetchall(PDO::FETCH_ASSOC);

        $dbh = null;

        return $result;
    }

    function getPhotoData($id) {
        //データベース接続
        $dbh = dbConnect();

        $sql = "SELECT photo_url FROM t_estate_photo WHERE estate_id = ".$id." AND main_flg = 1";
    
        $stmt = $dbh->query($sql);

        $result = $stmt->fetchall(PDO::FETCH_ASSOC);

        $dbh = null;

        return $result;
    }

    function getTransportData($id) {
        //データベース接続
        $dbh = dbConnect();

        $sql = "SELECT transport_station, transport_time FROM t_estate_transport WHERE estate_id = $id ORDER BY display_order ASC LIMIT 1";

        $stmt = $dbh->query($sql);

        $result = $stmt->fetchall(PDO::FETCH_ASSOC);

        $dbh = null;

        return $result;
    }

    //データベースから家の詳細情報取得s
    //配列の中に配列を入れる
    $detail_data = getAllData();
    $detail_count = count($detail_data);

    $tag_arr = [];
    $arr = [];
    $option_arr = [];
    $option_arr_box = [];
    $getData = New GetData();
    $tag_arr = $getData->getTagDark($detail_data,$tag_arr);
    $photoUrl=[];
    $transport=[];
    // SQLで読み取るデータを1つずつ配列に格納
    foreach($detail_data as $value) {
        $arr[] = $value;
        list($arr, $option_arr) = useClass($arr, $option_arr);
        $option_arr_box[] = $option_arr;
        $arr = [];
        $option_arr = [];
    }

    for($i = 0; $i < count($detail_data); $i++){
        $photoUrl[]=getPhotoData($detail_data[$i]['estate_id']);
        $transport[]=getTransportData($detail_data[$i]['estate_id']);
    }
?>

<?php include "./header.php"; ?>

<el-main class="main">
    <el-row>
        <el-col :sm="0" :xs="24" class="sp">
            <div class="introl" style="margin-bottom: 5px">
                <span class='intro-title-sentence'>『これでいい』</span>
                <span class='intro-title-sub'>ではなく</span>
                <span class='intro-title-sentence'>『これがいい』</span>
            </div>
            <div class='intro-title-sub' style="margin-bottom: 5px">
                とおっしゃって頂く物件選定から
            </div>
            <div class='intro-title-sub' style="margin-bottom: 5px">
                不動産のお取引を通じて
            </div>
            <div class="intro-title-sub" style="margin-bottom: 5px">
                お客様の人生の中で大きな転機となるタイミングに
            </div>
            <div class="intro-title-sub" style="margin-bottom: 5px">
                ”安心して、すべてお任せ頂けること”をモットーに、
            </div>
            <div class="intro-title-sub" style="margin-bottom: 5px">
                弊社の持つ幅広いネットワークを駆使してスムーズに
            </div>
            <div class="intro-title-sub" style="margin-bottom: 5px">
                不動産全般の業務をさせて頂きます。
            </div>
        </el-col>

        <el-col :span="24" :xs="0" class="not-sp">
            <div class="introl" style="margin-bottom: 20px">
                <span class='intro-title-sentence'>『これでいい』</span>
                <span class='intro-title-sub'>ではなく</span>
                <span class='intro-title-sentence'>『これがいい』</span>
                <span class='intro-title-sub'>とおっしゃって頂く物件選定から、不動産のお取引を通じて、</span>
            </div>
            <div class="intro-title-sub" style="margin-bottom: 20px">
                お客様の人生の中で大きな転機となるタイミングに”安心して、すべてお任せ頂けること”をモットーに、
            </div>
            <div class="intro-title-sub" style="margin-bottom: 20px">
                弊社の持つ幅広いネットワークを駆使してスムーズに不動産全般の業務をさせて頂きます。
            </div>
        </el-col>
    </el-row>
    <!-- 物件一覧に飛ぶボタン -->
    <div class="info-tag">
        <el-row class="search_conditions" type="flex" justify="center">
            <el-col :span="24" style="text-align:center; margin-bottom: 30px; padding-top: 10px;">
                <div class="search"><i class="el-icon-search"></i>検索</div>
            </el-col>
        </el-row>
        <el-row style="margin:10px 0px;" class="housebotton">購入物件</el-row>
        <el-row type="flex" justify="center">
            <el-col style="margin-bottom:40px;">
                <div class="housebotton">
                    <!-- スマホじゃない時 -->
                    <div class="not-sp">
                        <el-button @click="pageMove4" class="search-button">新築戸建
                        </el-button>
                        <el-button @click="pageMove3" class="search-button">中古戸建
                        </el-button>
                        <el-button @click="pageMove2" class="search-button"><el-row>アパート・</el-row>
                            <el-row>マンション</el-row>
                        </el-button>
                        <el-button @click="pageMove0" class="search-button">土地
                        </el-button>
                    </div>

                    <!-- スマホの時のみ -->
                    <p class="sp">
                        <el-button @click="pageMove4" class="search-button">新築戸建
                        </el-button>
                        <el-button @click="pageMove3" class="search-button">中古戸建
                        </el-button>
                    </p>
                    <p class="sp">
                        <el-button @click="pageMove2" class="search-button"><el-row>アパート・</el-row>
                            <el-row>マンション</el-row>
                        </el-button>
                        <el-button @click="pageMove0" class="search-button">土地
                        </el-button>
                    </p>
                </div>
            </el-col>
        </el-row>
        
        <el-row style="margin-bottom:10px;" class="housebotton">賃貸物件</el-row>
        <el-row justify="space-around">
            <el-col>
                <div class="housebotton">
                    <!-- スマホじゃない時 -->
                    <div class="not-sp">
                        <el-button @click="pageMoveRent4" class="search-button">新築戸建
                        </el-button>
                        <el-button @click="pageMoveRent3" class="search-button">中古戸建
                        </el-button>
                        <el-button @click="pageMoveRent2" class="search-button"><el-row>アパート・</el-row>
                            <el-row>マンション</el-row>
                        </el-button>
                        <el-button @click="pageMoveRent1" class="search-button">事務所
                        </el-button>
                    </div>

                    <!-- スマホの時のみ -->
                    <p class="sp">
                        <el-button @click="pageMoveRent4" class="search-button">新築戸建
                        </el-button>
                        <el-button @click="pageMoveRent3" class="search-button">中古戸建
                        </el-button>
                    </p>
                    <p class="sp">
                        <el-button @click="pageMoveRent2" class="search-button"><el-row>アパート・</el-row>
                            <el-row>マンション</el-row>
                        </el-button>
                        <el-button @click="pageMoveRent1" class="search-button">事務所
                        </el-button>
                    </p>
                </div>
            </el-col>
        </el-row>
    </div>

    <el-row>
        <el-col style="text-decoration: underline;margin-bottom: 20px;">
            <p class="Pick-up-sentence">PICK UP</p>
        </el-col>
    </el-row>

    <!-- 768px以上の時 Pick-upのカードをv-forで回す -->
    <?php if($detail_count <= 2): ?>
    <el-row class="not-sp" type="flex" justify="space-around">
    <?php else: ?>
    <el-row class="not-sp" type="flex" justify="space-between">
    <?php endif ?>

        <?php for($i = 0; $i < count($detail_data); $i++){?>
        <el-col :span="7" style="margin-bottom:30px;">
            <a href="../service/details.php?estate_code=<?php echo $detail_data[$i]['estate_code'];?>"
                :underline="false">
                <el-card :body-style="{ padding: '0px' }" class="card">
                    <el-row type="flex" justify="center">
                        <img class="estate-image" src=<?php echo $photoUrl[$i][0]['photo_url'];?>></img>
                    </el-row>
                    <el-row style="padding: 14px;">
                        <el-row style="margin-bottom:10px;">
                            <el-col :span="12"><?php echo $tag_arr[$i]; ?></el-col>
                            <el-col :span="12" style="text-align:right;">
                            <?php echo preg_replace('/\.?0+$/', '', number_format($detail_data[$i]['estate_price'], 2)); ?>万円</el-col>
                        </el-row>
                        <div>●<?php echo $detail_data[$i]['address_detail']; ?></div>
                        <div>●<?php echo $transport[$i][0]['transport_station'];?></div>
                        <div style="text-align:right;">徒歩<?php echo $transport[$i][0]['transport_time'];?>分</div>
                        <div>●<?php echo $option_arr_box[$i][1];?></div>
                        <div>●<?php echo $option_arr_box[$i][2];?></div>
                        <div>●<?php echo $option_arr_box[$i][3];?></div>
                    </el-row>
                </el-card>
            </a>
        </el-col>
    <?php } ?>
    </el-row>

    <!-- 767px以下の時 Pick-upのカードをv-forで回す -->
    <?php if($detail_count <= 2): ?>
    <el-row class="sp">
    <?php else: ?>
    <el-row class="sp">
    <?php endif ?>

        <?php for($i = 0; $i < count($detail_data); $i++){?>
        <el-col :xs="24" style="margin-bottom:30px;">
            <a href="../service/details.php?estate_code=<?php echo $detail_data[$i]['estate_code'];?>"
                :underline="false">
                <el-card :body-style="{ padding: '0px' }" class="card">
                    <el-row type="flex" justify="center">
                        <img class="estate-image" src=<?php echo $photoUrl[$i][0]['photo_url'];?>></img>
                    </el-row>
                    <el-row style="padding: 14px;">
                        <el-row style="margin-bottom:10px;">
                            <el-col :span="12"><?php echo $tag_arr[$i]; ?></el-col>
                            <el-col :span="12" style="text-align:right;">
                            <?php echo preg_replace('/\.?0+$/', '', number_format($detail_data[$i]['estate_price'], 2)); ?>万円</el-col>
                        </el-row>
                        <div>●<?php echo $detail_data[$i]['address_detail']; ?></div>
                        <div>●<?php echo $transport[$i][0]['transport_station'];?></div>
                        <div style="text-align:right;">徒歩<?php echo $transport[$i][0]['transport_time'];?>分</div>
                        <div>●<?php echo $option_arr_box[$i][1];?></div>
                        <div>●<?php echo $option_arr_box[$i][2];?></div>
                        <div>●<?php echo $option_arr_box[$i][3];?></div>
                    </el-row>
                </el-card>
            </a>
        </el-col>
    <?php } ?>
    </el-row>


        <el-row style="margin-bottom:30px;">
            <el-col class="summerize">
                <el-link v-bind:href=querypick :underline="false">
                    <el-button><i class="el-icon-d-arrow-right"></i>一覧を見る</el-button>
                </el-link>
            </el-col>
        </el-row>

        <el-row style="margin-bottom:10px;">
            <el-col style="text-decoration: underline">
                <p class="Pick-up-sentence">NEWS</p>
            </el-col>
        </el-row>
        <!-- newsの画面と連携はさせていないので更新がつながっています -->
        <el-row type="flex" justify="center" style="margin-bottom:20px;">
            <el-col :xs="24" :span="20">
                <a href="./news_list_item1.php" :underline="false">
                    <el-row class="news-link" type="flex" justify="space-between" style="margin-bottom:50px;">
                        <el-image class="news-image" fit="contain" src="../../www/img/news1.png">
                        </el-image>
                        <el-col :span="18" class="news-contents">
                            <div class="news-data">2020.12.17</div>
                            <div>株式会社ジャストフィットの不動産サイトをオープンいたしました。</div>
                        </el-col>
                    </el-row>
                </a>
            </el-col>
        </el-row>

        <el-row>
            <el-col class="summerize" style="margin-bottom:10px;">
                <el-button>
                    <el-link href="news_list.php" :underline="false"><i class="el-icon-d-arrow-right"></i>一覧を見る
                    </el-link>
                </el-button>
            </el-col>
        </el-row>

        <el-row type="flex" justify="center">
            <el-col :span="20">
                <meta charset="UTF-8">
                <p>
                    <div style="margin-bottom:100px"></div>
                </p>
            </el-col>

        </el-row>

</el-main>

        </el-col>

<?php include "./footer.php"; ?>

<script>
    var app = new Vue({
        el: "#app",
        data: {
            drawer: false,  //ハンバーガーメニューのためのプロパティ

            estate4: location.href.substring(0, location.href.indexOf('index')) +
                'estatelist.php?puse=1&este=4',
            estate3: location.href.substring(0, location.href.indexOf('index')) +
                'estatelist.php?puse=1&este=3',
            estate2: location.href.substring(0, location.href.indexOf('index')) +
                'estatelist.php?puse=1&este=2',
            estate0: location.href.substring(0, location.href.indexOf('index')) +
                'estatelist.php?puse=1&este=0',
            rentestate4: location.href.substring(0, location.href.indexOf('index')) +
                'estatelist.php?puse=0&este=4',
            rentestate3: location.href.substring(0, location.href.indexOf('index')) +
                'estatelist.php?puse=0&este=3',
            rentestate2: location.href.substring(0, location.href.indexOf('index')) +
                'estatelist.php?puse=0&este=2',
            rentestate1: location.href.substring(0, location.href.indexOf('index')) +
                'estatelist.php?puse=0&este=1',
            querypick: location.href.substring(0, location.href.indexOf('index')) +
                'estatelist.php?piup=1&order=pickup&limit=10&page=0'
        },
        methods: {
            window: onload = function () {
                    document.getElementById("top_image").style.display = 'block';
                    document.getElementById("navHome").style.backgroundColor = '#fbdac8';
            },
            pageMove4() {
                window.location.href = this.estate4;
            },
            pageMove3() {
                window.location.href = this.estate3;
            },
            pageMove2() {
                window.location.href = this.estate2;
            },
            pageMove0() {
                window.location.href = this.estate0;
            },
            pageMoveRent4() {
                window.location.href = this.rentestate4;
            },
            pageMoveRent3() {
                window.location.href = this.rentestate3;
            },
            pageMoveRent2() {
                window.location.href = this.rentestate2;
            },
            pageMoveRent1() {
                window.location.href = this.rentestate1;
            },

        }
    })
</script>

<script>
    var main_visual_height = document.getElementById('pc-header').offsetHeight;
    var main_margin = document.documentElement.clientHeight - main_visual_height + 50;
    var main = document.getElementsByClassName('main');
    main[0].style.marginTop = main_margin + 'px';
    window.addEventListener('resize', resizeWindow);
    function resizeWindow(event){
        var main_visual_height = document.getElementById('pc-header').offsetHeight;
        var main_margin = document.documentElement.clientHeight - main_visual_height + 50;
        var main = document.getElementsByClassName('main');
        main[0].style.marginTop = main_margin + 'px';
    }
</script>

<!-- 自作のcss読み込み -->
<link rel="stylesheet" href="../../www/css/index.css">

</html>