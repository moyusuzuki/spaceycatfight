<!-- </header>までが入る ======================== -->
<?php include './header.php'; ?>
<!-- ========================================== -->

<el-main class="main">

<el-row>
<el-col :span="24" class="link-map">
   <el-link href="./index.php" type="primary">ホーム</el-link> <i class="el-icon-arrow-right"></i> ニュースリスト
</el-col>

    <el-col :span="24" class="page-title">NEWS</el-col>


        <el-row>
        <a href="./news_list_item1.php" :underline="false">
        <el-col :span="20" :offset="2" class="newslist">
        <el-row class="news-link" type="flex" justify="space-between">
            <el-col :span="6">
            <el-image class="news-image" fit="contain" src="../../www/img/news1.png"></el-image>
            </el-col>
                <el-col　:span="16" :offset="2" class="news-contents">
                    <p class="news-data">2020.12.17</p>
                    <p>株式会社ジャストフィットの不動産サイトをオープンいたしました。</p>
                </el-col>
        </el-row>
        </el-col>
        </a>
        </el-row>

</el-row>
</el-main>


<!-- footerが入る <footer> 〜 </footer>が入る==== -->
<?php include './footer.php'; ?>
<!-- ========================================== -->

<!-- 自作のjs読み込み-->
<script>
    new Vue({
        el: "#app",
        data: {
            drawer: false,  //ハンバーガーメニューのためのプロパティ
        }
    })
</script>
<!-- 自作のcss読み込み -->
<link rel="stylesheet" href="../../www/css/news_list.css">

</html>