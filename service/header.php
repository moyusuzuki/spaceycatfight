<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- リセットCSS -->
    <link rel="stylesheet" type="text/css" href="../../www/css/destyle.css">
    <!-- vue.jsのCDN読み込み -->
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <!-- Element index.css読み込み -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/element-ui/2.14.1/theme-chalk/index.min.css">
    <!-- Element index.js読み込み -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/element-ui/2.14.1/index.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../../www/css/header.css">
    <link rel="stylesheet" type="text/css" href="../../www/css/footer.css">
    <link rel="icon" type="image/x-icon" href="../../favicon.ico">
    <title>JUST FIT ESTATE</title>
</head>

<body id="body">
    <div id="app">
        
        <el-container>
            <img id="top_image" src="../../www/img/top.jpg"></img>
            <el-col :xs="24" :sm="{span:20,offset:2}" class="wrapper">
                
                <el-header id="pc-header" height="auto" style="padding: 0px">
                    <!-- headerの上部 -->
                    <el-row style="padding-top:15px;padding-bottom:28px;background-color:white;">
                        <!-- ヘッダーのロゴ 768px以下の時に消える -->
                        <el-col :span="6" :offset="1" :xs="0" class="rogo">
                            <el-link href="../../src/service/index.php" :underline="false">
                                <el-image src="../../www/img/JUST-FIT_ロゴ.png"></el-image>
                                <el-col :xs="0" :span="24" v-bind:style="{ color:'#ED701A',fontSize:'1.2rem' }">
                                    株式会社ジャストフィット
                                </el-col>
                            </el-link>
                        </el-col>

                                                <!-- 通常のヘッダー -->
                        <el-col :span="7" :offset="3" :xs="0" style="padding-top:24px;">
                            <el-col :span="20" :xs="0" class="number">
                                <el-link href="tel:+0474418733" class="el-icon-phone-outline" type="warning"
                                    v-bind:style="{ color:'#ED701A' }">047-436-8642</el-link>
                            </el-col>
                            <el-col :span="4" :xs="0" class="directpass">(直通)</el-col>
                            <el-col :span="20" :xs="0" class="fax">FAX：047-436-8643</el-col>
                        </el-col>
                        <a href="../../src/service/contact.php" v-bind:style="{ fontSize:'1.0rem'}">
                            <el-col :span="5" :offset="1" :xs="0" class="message">
                                <i class="el-icon-message" style="padding-right:12px;"></i>お問い合わせ
                            </el-col>
                        </a>

                        <!-- ヘッダーのロゴとハンバーガーメニュー -->
                        <el-col class="xs-header">
                            <el-row type="flex" justify="space-between">
                                <el-col :span="12">
                                    <a href="./index.php"><img class="xs-header-logo"
                                            src="../../www/img/JUST-FIT_ロゴ.png"></img></a>
                                </el-col>
                                <el-col :span="3">
                                    <p @click="drawer = true"><img class="xs-header-hum" src="../../www/img/hum.png">
                                    </p>
                                </el-col>
                            </el-row>
                        </el-col>
                        <!-- ハンバーガーメニューが押された時に表示されるもの -->
                        <el-drawer title="I am the title" :visible.sync="drawer" :with-header="false" size="60%">
                            <nav class="hum-nav">
                                <el-row class="hum-ul" tag="ul">
                                    <a href="./index.php">
                                        <el-col class="hum-li li-odd" tag="li"><i class="el-icon-s-home"></i> ホーム
                                        </el-col>
                                    </a>
                                    <a href="./conditions.php?puse=1">
                                        <el-col class="hum-li li-even" tag="li"><i class="el-icon-s-home"></i> 物件を買う
                                        </el-col>
                                    </a>
                                    <a href="./conditions.php?puse=0">
                                        <el-col class="hum-li li-odd" tag="li"><i class="el-icon-s-home"></i> 物件を借りる
                                        </el-col>
                                    </a>
                                    <a href="./companyOverview.php">
                                        <el-col class="hum-li li-even" tag="li"><i class="el-icon-s-home"></i> 会社概要
                                        </el-col>
                                    </a>
                                    <a href="./news_list.php">
                                        <el-col class="hum-li li-odd" tag="li"><i class="el-icon-s-home"></i> ニューストピック
                                        </el-col>
                                    </a>
                                    <a href="./contact.php">
                                        <el-col class="hum-li li-even"><i class="el-icon-s-home"></i> お問い合わせ</el-col>
                                    </a>
                                    <el-col class="hum-li hum-contact" tag="li">
                                        <p><a href="tel:+0474418733"
                                                class="el-icon-phone-outline hum-phone">047-436-8642</a></p>
                                        <p class="hum-fax">FAX：047-436-8643</p>
                                    </el-col>
                                </el-row>
                            </nav>
                        </el-drawer>
                    </el-row>

                    <!--header下部の選択箇所 768px以下の端末の時消える-->
                    <el-row style="background-color:white;font-family:HGP創英角ｺﾞｼｯｸUB;">
                        <el-col :span="6" :xs="0" class="nav_col" ref="navHome">
                            <el-link href="index.php" :underline="false">
                                <div class="grid-content" id="navHome">ホーム</div>
                            </el-link>
                        </el-col>
                        <el-col :span="6" :xs="0" class="nav_col" ref="navPur">
                            <el-link href="conditions.php?puse=1" :underline="false">
                                <div class="grid-content" id="navPur">物件を買う</div>
                            </el-link>
                        </el-col>
                        <el-col :span="6" :xs="0" class="nav_col" ref="navRen">
                            <el-link href="conditions.php?puse=0" :underline="false">
                                <div class="grid-content" id="navRen">物件を借りる</div>
                            </el-link>
                        </el-col>
                        <el-col :span="6" :xs="0" class="nav_col" ref="navCon">
                            <el-link href="companyOverview.php" :underline="false">
                                <div class="grid-content" id="navCom">会社概要</div>
                            </el-link>
                        </el-col>
                    </el-row>
                </el-header>


                <!-- 768px以下のヘッダー -->
                <el-header class="xs-header">
                    <el-row type="flex" justify="space-between">
                        <el-col :span="12">
                            <a href="./index.php"><img class="xs-header-logo"
                                    src="../../www/img/JUST-FIT_ロゴ.png"></img></a>
                        </el-col>
                        <el-col :span="3">
                            <p @click="drawer = true"><img class="xs-header-hum" src="../../www/img/hum.png"></p>
                        </el-col>
                    </el-row>
                </el-header>
                <!-- ハンバーガーメニューが押された時に表示されるもの -->
                <el-drawer title="I am the title" :visible.sync="drawer" :with-header="false" size="50%">
                    <nav class="hum-nav">
                        <el-row class="hum-ul" tag="ul">
                            <a href="./index.php">
                                <el-col class="hum-li li-odd" tag="li"><i class="el-icon-s-home"></i> ホーム</el-col>
                            </a>
                            <a href="./conditions.php?puse=1">
                                <el-col class="hum-li li-even" tag="li"><i class="el-icon-s-home"></i> 物件を買う</el-col>
                            </a>
                            <a href="./conditions.php?puse=0">
                                <el-col class="hum-li li-odd" tag="li"><i class="el-icon-s-home"></i> 物件を借りる</el-col>
                            </a>
                            <a href="./companyOverview.php">
                                <el-col class="hum-li li-even" tag="li"><i class="el-icon-s-home"></i> 会社概要</el-col>
                            </a>
                            <a href="./contact.php">
                                <el-col class="hum-li li-odd"><i class="el-icon-s-home"></i> お問い合わせ</el-col>
                            </a>
                            <el-col class="hum-li hum-contact" tag="li">
                                <p><a href="tel:+0474418733" class="el-icon-phone-outline hum-phone">047-436-8642</a>
                                </p>
                                <p class="hum-fax">FAX：047-436-8643</p>
                            </el-col>
                        </el-row>
                    </nav>
                </el-drawer>
