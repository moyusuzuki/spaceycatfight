                <el-footer class="footer" height="auto">
                    <el-row class="footer_row">
                        <el-col :sm="6" :offset="1" :xs="12" class="footer-logo">
                            <el-link href="../../src/service/index.php" :underline="false" type="info">
                                <el-image src="../../www/img/JUST-FIT_ロゴ.png"></el-image>
                                <el-col :span="24" :xs="0" v-bind:style="{ fontSize:'1.3rem' }">
                                    株式会社ジャストフィット
                                </el-col>
                                <el-col :sm="0" :xs="24" class="footer-k">株式会社ジャストフィット</el-col>
                            </el-link>
                        </el-col>
                        <el-col :span="4" :offset="2" :xs="0">
                            <ul>
                                <li style="margin-bottom:20px;">
                                    <el-link icon="el-icon-arrow-right" type="info" href="../../src/service/index.php">ホーム</el-link>
                                </li>
                                <li style="margin-bottom:20px;">
                                    <el-link icon="el-icon-arrow-right" type="info" href="../../src/service/conditions.php?puse=1">物件を買う
                                    </el-link>
                                </li>
                                <li style="margin-bottom:20px;">
                                    <el-link icon="el-icon-arrow-right" type="info" href="../../src/service/conditions.php?puse=0">
                                        物件を借りる</el-link>
                                </li>
                            </ul>
                        </el-col>
                        <el-col :span="4" :xs="0">
                            <ul>
                                <li style="margin-bottom:20px;">
                                    <el-link icon="el-icon-arrow-right" type="info" href="../../src/service/companyOverview.php">会社概要
                                    </el-link>
                                </li>
                                <li style="margin-bottom:20px;">
                                    <el-link icon="el-icon-arrow-right" type="info" href="../../src/service/news_list.php">ニューストピック
                                    </el-link>
                                </li>
                                <li style="margin-bottom:20px;">
                                    <el-link icon="el-icon-arrow-right" type="info" href="../../src/service/estatelist.php?piup=1&order=pickup&limit=10&page=0">PICK
                                        UP物件</el-link>
                                </li>
                            </ul>
                        </el-col>
                        <el-col :span="4" :xs="0">
                            <ul>
                                <li style="margin-bottom:20px;">
                                    <el-link icon="el-icon-arrow-right" type="info" href="../../src/service/contact.php">お問い合わせ
                                    </el-link>
                                </li>
                            </ul>
                        </el-col>
                    </el-row>
                    <el-row class="xs-footer-cc">
                        <el-col :sm="0" :xs="24" style="font-size:0.8rem;">
                            <div>Copyright©　株式会社ジャストフィット</div>
                        </el-col>
                    </el-row>
                    <el-row class="footer-cc">
                        <el-col :span="24" :xs="0" style="text-align:center;">
                            <small>Copyright©　株式会社ジャストフィット｜不動産売買 ・ 賃貸 ・ 賃貸管理 ・ システムエンジニアリングサービス , 2021 All Rights Reserved.</small> 
                        </el-col>
                    </el-row>
                </el-footer>
            </el-col>
            </el-container>
        </div>
    </body>