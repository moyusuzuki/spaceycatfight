<!-- </header>までが入る ======================== -->
<?php include './header.php'; ?>
<!-- ========================================== -->

<el-main class="main">

    <el-row>
        <el-col :span="24" class="link-map">
            <el-link href="./index.php" type="primary">ホーム</el-link> <i class="el-icon-arrow-right"></i>
            <el-link href="./news_list.php" type="primary">ニュースリスト</el-link> <i class="el-icon-arrow-right"></i>
            株式会社ジャストフィットの不動産サイトをオープンいたしました
        </el-col>
        <el-col :span="24">
            <h2 class="page-title">NEWS</h2>

            <el-col tag="h3" :span="22" :offset="1" class="news-name">株式会社ジャストフィットの不動産サイトをオープンいたしました</el-col>

            <el-col tag="p" :span="22" :offset="1" class="news-date">2020.12.17</el-col>

            <el-col class="news-content" :span="22" :offset="1">
                <el-divider></el-divider>

                <p>日ごろから弊社サービスをご愛顧いただき、誠にありがとうございます。</p>
                <p>このたび、株式会社ジャストフィットの不動産サイトがオープンいたしました。</p>
                <p><span class="this-good">『これでいい』</span>ではなく<span class="this-good">『これがいい』</span>とおっしゃって頂く物件選定から</p>
                <p>不動産のお取引を通じて、 お客様の人生の中で大きな転機となるタイミングに”安心して、すべてお任せ頂けること”をモットーに</p>
                <p>弊社の持つ幅広いネットワークを駆使してスムーズに不動産全般の業務をさせて頂きます。</p>
                <p>今後ともよろしくお願いいたします。</p>

            </el-col>
        </el-col>
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
            drawer: false, //ハンバーガーメニューのためのプロパティ
        }
    })
</script>
<!-- 自作のcss読み込み -->
<link rel="stylesheet" href="../../www/css/news_list_item.css">

</html>