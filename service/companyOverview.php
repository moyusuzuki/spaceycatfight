<!-- </header>までが入る ======================== -->
<?php 
    include "./header.php";
?>
<!-- ========================================== -->

<el-main class="main">
    <el-row>

        <el-col :span="24" class="page-link">
            <el-link href="./index.php" type="primary">ホーム</el-link> <i class="el-icon-arrow-right"></i>会社概要
        </el-col>

        <el-col :span="24">
            <h3 class="page-title">会社概要</h3>
        </el-col>

        <el-col :span="24">

            <el-col :span="24" class="info-list">
                <el-row type="flex" justify="space-between">
                    <el-col :span="4">
                        <p class="title">社名</p>
                    </el-col>
                    <el-col :span="19">
                        <p>株式会社ジャストフィット</p>
                    </el-col>
                </el-row>
            </el-col>
            <el-col :span="24" class="info-list">
                <el-row type="flex" justify="space-between">
                    <el-col :span="4">
                        <p class="title">設立年月日</p>
                    </el-col>
                    <el-col :span="19">
                        <p>平成18年10月30日</p>
                    </el-col>
                </el-row>
            </el-col>
            <el-col :span="24" class="info-list">
                <el-row type="flex" justify="space-between">
                    <el-col :span="4">
                        <p class="title">資本金</p>
                    </el-col>
                    <el-col :span="19">
                        <p>10,000,000円</p>
                    </el-col>
                </el-row>
            </el-col>
            <el-col :span="24" class="info-list">
                <el-row type="flex" justify="space-between">
                    <el-col :span="4">
                        <p class="title">役員</p>
                    </el-col>
                    <el-col :span="19">
                        <p>代表取締役社長 岡部 智孝</p>
                        <p>取締役　　　　 細貝 一貴</p>
                    </el-col>
                </el-row>
            </el-col>
            <el-col :span="24" class="info-list">
                <el-row type="flex" justify="space-between">
                    <el-col :span="4">
                        <p class="title">所在地</p>
                    </el-col>
                    <el-col :span="19">
                        <p>〒273-0135 千葉県鎌ケ谷市中佐津間2-1-3</p>
                        <p>TEL:047-436-8642 (直通)</p>
                        <p>FAX:047-436-8643</p>
                    </el-col>
                </el-row>
            </el-col>
            <el-col :span="24" class="info-list">
                <el-row type="flex" justify="space-between">
                    <el-col :span="4">
                        <p class="title">顧問税理士</p>
                    </el-col>
                    <el-col :span="19">
                        <p>顧問税理士<宍倉信行税理士事務所>
                        </p>
                        <p>宍倉 信行</p>
                    </el-col>
                </el-row>
            </el-col>
            <el-col :span="24" class="info-list">
                <el-row type="flex" justify="space-between">
                    <el-col :span="4">
                        <p class="title">提携司法書士</p>
                    </el-col>
                    <el-col :span="19">
                        <p>提携司法書士<松岡合同事務所>
                        </p>
                        <p>赤田 紀明</p>
                    </el-col>
                </el-row>
            </el-col>
            <el-col :span="24" class="info-list">
                <el-row type="flex" justify="space-between">
                    <el-col :span="4" class="title">
                        <p>取引銀行</p>
                    </el-col>
                    <el-col :span="19">
                        <p>りそな銀行 津田沼支店</p>
                    </el-col>
                </el-row>
            </el-col>
            <el-col :span="24" class="info-list">
                <el-row type="flex" justify="space-between">
                    <el-col :span="4" class="title">
                        <p>事業内容</p>
                    </el-col>
                    <el-col :span="19">
                        <el-row tag="ul">
                            <el-col tag="li">
                                <p> ・ 宅地建物取引業法に基づく宅地建物取引業</p>
                            </el-col>
                            <el-col tag="li">
                                <p> ・ 不動産の売買、賃貸借、管理及びその仲介業務</p>
                            </el-col>
                            <el-col tag="li">
                                <p> ・ 不動産コンサルティング業</p>
                            </el-col>
                            <el-col tag="li">
                                <p> ・ 一般労働者派遣事業</p>
                            </el-col>
                            <el-col tag="li">
                                <p> ・ 広告代理業</p>
                            </el-col>
                            <el-col tag="li">
                                <p> ・ 経営コンサルタント業</p>
                            </el-col>
                            <el-col tag="li">
                                <p> ・ システムエンジニアリング・サービス事業</p>
                            </el-col>
                            <el-col tag="li">
                                <p> ・ Webコンテンツ制作事業</p>
                            </el-col>
                        </el-row>
                    </el-col>
                </el-row>
            </el-col>
            <el-col :span="24" class="info-list">
                <el-row type="flex" justify="space-between">
                    <el-col :span="4" class="title">
                        <p>登録免許番号</p>
                    </el-col>
                    <el-col :span="19">
                        <p>宅地建物取引業者 千葉県知事免許(2)第16277号</p>
                    </el-col>
                </el-row>
            </el-col>
            <el-col :span="24" class="info-list">
                <el-row type="flex" justify="space-between">
                    <el-col :span="4" class="title">
                        <p>所属団体</p>
                    </el-col>
                    <el-col :span="19">
                        <p>(社)全国宅地建物取引業保証協会</p>
                    </el-col>
                </el-row>
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
        },
        methods: {
            window: onload = function () {
                document.getElementById("navCom").style.backgroundColor = '#fbdac8';
            }
        }
    })
</script>
<!-- 自作のcss読み込み -->
<link rel="stylesheet" href="../../www/css/companyOverview.css">

</html>