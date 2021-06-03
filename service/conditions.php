<?php include "./header.php"; ?>

<?php

$puse = $_GET['puse'];

if ($puse == '1' || $puse == '0') :

?>

<el-main class="main">
    <el-row class="link-map-box">
        <el-col :span="24" class="link-map">
            <el-link href="./index.php" type="primary">ホーム</el-link> <i class="el-icon-arrow-right"></i> <template
                v-if="puseChecker(<?php echo $puse; ?>)">物件を買う</template><template v-else>物件を借りる</template>
        </el-col>
    </el-row>

    <el-form ref="form" :model="form">
        <div class="conditions_area">
            <el-row class="house" style="margin-bottom: 30px">
                <el-col :span="24" class="house_search" justify="center">検索条件</el-col>
            </el-row>

            <?php if ($puse == '1') : ?>
            <el-row>
                <el-col :span="23" :offset="1" class="type">建物の種類</el-col>
            </el-row>
            <el-row>
                <el-col :span="22" :offset="2">
                    <el-form-item>
                        <el-checkbox-group v-model="form.este" @change="areaHider,clickSubmit()">
                            <el-checkbox label="4" name="este">新築戸建</el-checkbox>
                            <el-checkbox label="3" name="este">中古戸建</el-checkbox>
                            <el-checkbox label="2" name="este">アパート・マンション</el-checkbox>
                            <el-checkbox label="0" name="este">土地</el-checkbox>
                        </el-checkbox-group>
                    </el-form-item>
                </el-col>
            </el-row>
            <?php endif ?>


            <?php if ($puse == '0') : ?>
            <el-row>
                <el-col :span="23" :offset="1" class="type">建物の種類</el-col>
                <el-col :span="22" :offset="2">
                    <el-form-item>
                        <el-checkbox-group v-model="form.este" @change="areaHider,clickSubmit()">
                            <el-checkbox label="4" name="este">新築戸建</el-checkbox>
                            <el-checkbox label="3" name="este">中古戸建</el-checkbox>
                            <el-checkbox label="2" name="este">アパート・マンション</el-checkbox>
                            <el-checkbox label="1" name="este">事務所</el-checkbox>
                        </el-checkbox-group>
                    </el-form-item>
                </el-col>
            </el-row>
            <?php endif ?>

            <el-row>
                <el-col :span="23" :offset="1" class="type">市を選択</el-col>
            </el-row>
            <el-row>
                <el-col :span="22" :offset="2">
                    <el-form-item>
                        <el-checkbox-group v-model="form.city" @change="clickSubmit()">
                            <el-checkbox label="0" name="city">船橋</el-checkbox>
                            <el-checkbox label="1" name="city">鎌ケ谷</el-checkbox>
                            <el-checkbox label="2" name="city">市川</el-checkbox>
                            <el-checkbox label="3" name="city">白井</el-checkbox>
                            <el-checkbox label="4" name="city">松戸</el-checkbox>
                        </el-checkbox-group>
                    </el-form-item>
                </el-col>
            </el-row>

            <?php if ($puse == '1') : ?>
            <el-row>
                <el-col :span="23" :offset="1" class="type">価格</el-col>
            </el-row>
            <el-row>
                <el-col :xs="{span:8,offset:2}" :sm="{span:6,offset:2}" :md="{span:4,offset:2}">
                    <el-form-item　style="margin-bottom:0px;">
                        <el-select v-model="form.estate_priceLow" clearable placeholder="下限なし" @change="clickSubmit()">
                            <template v-for="item in pricePuse">
                                <template v-if=isLower(item.value,form.estate_priceHigh)>
                                    <el-option :key="item.value" :label="item.label" :value="item.value">
                                    </el-option>
                                </template>
                            </template>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="1" :offset="1" class="conditions_type">～</el-col>
                <el-col :xs="{span:8,offset:1}" :sm="{span:6,offset:1}" :md="{span:4,offset:1}">
                    <el-form-item　style="margin-bottom:0px;">
                        <el-select v-model="form.estate_priceHigh" clearable placeholder="上限なし" @change="clickSubmit()">
                            <template v-for="item in pricePuse">
                                <template v-if=isHigher(item.value,form.estate_priceLow)>
                                    <el-option :key="item.value" :label="item.label" :value="item.value">
                                    </el-option>
                                </template>
                            </template>
                        </el-select>
                    </el-form-item>
                </el-col>
            </el-row>
            <?php endif ?>
            <?php if ($puse == '0') : ?>
            <el-row>
                <el-col :span="23" :offset="1" class="type">賃料</el-col>
            </el-row>
            <el-row>
                <el-col :xs="{span:8,offset:2}" :sm="{span:6,offset:2}" :md="{span:4,offset:2}">
                    <el-form-item　style="margin-bottom:0px;">
                        <el-select v-model="form.estate_priceLow" clearable placeholder="下限なし" @change="clickSubmit()">
                            <template v-for="item in priceNpuse">
                                <template v-if=isLower(item.value,form.estate_priceHigh)>
                                    <el-option :key="item.value" :label="item.label" :value="item.value">
                                    </el-option>
                                </template>
                            </template>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="1" :offset="1" class="conditions_type">～</el-col>
                <el-col :xs="{span:8,offset:1}" :sm="{span:6,offset:1}" :md="{span:4,offset:1}">
                    <el-form-item　style="margin-bottom:0px;">
                        <el-select v-model="form.estate_priceHigh" clearable placeholder="上限なし" @change="clickSubmit()">
                            <template v-for="item in priceNpuse">
                                <template v-if=isHigher(item.value,form.estate_priceLow)>
                                    <el-option :key="item.value" :label="item.label" :value="item.value">
                                    </el-option>
                                </template>
                            </template>
                        </el-select>
                        </el-select>
                    </el-form-item>
                </el-col>
            </el-row>
            <?php endif ?>
            <el-row>
                <el-col :span="22" :offset="2">
                    <el-form-item>
                        <el-checkbox label="1" v-model="form.feed" name="feed" @change="clickSubmit()">
                            管理費・共益費込み</el-checkbox>
                        <el-checkbox label="1" v-model="form.seed" name="seed" @change="clickSubmit()">敷金なし
                        </el-checkbox>
                        <el-checkbox label="1" v-model="form.keey" name="keey" @change="clickSubmit()"> 礼金なし
                        </el-checkbox>
                        </el-checkbox-group>
                    </el-form-item>
                </el-col>
            </el-row>

            <el-row>
                <el-col :span="23" :offset="1" class="type">間取り</el-col>
            </el-row>
            <el-row style="margin-bottom:20px;">
                <el-col :span="22" :offset="2" style="margin-right:20px;">
                    <el-form-item style="margin-bottom:0px;">
                        <el-checkbox-group v-model="form.floor_plan" @change="clickSubmit()">
                            <el-row>
                                <el-checkbox label=1,1 name="floor_plan">ワンルーム</el-checkbox>
                                <el-checkbox label=1,2 name="floor_plan">1K</el-checkbox>
                                <el-checkbox label=1,3 name="floor_plan">1DK</el-checkbox>
                                <el-checkbox label=1,4 name="floor_plan">1LDK</el-checkbox>
                                <el-checkbox label=2,2 name="floor_plan">2K</el-checkbox>
                                <el-checkbox label=2,3 name="floor_plan">2DK</el-checkbox>
                                <el-checkbox label=2,4 name="floor_plan">2LDK</el-checkbox>
                            </el-row>
                            <el-row>
                                <el-checkbox label=3,2 name="floor_plan">3K</el-checkbox>
                                <el-checkbox label=3,3 name="floor_plan">3DK</el-checkbox>
                                <el-checkbox label=3,4 name="floor_plan">3LDK</el-checkbox>
                                <el-checkbox label=4,2 name="floor_plan">4K</el-checkbox>
                                <el-checkbox label=4,3 name="floor_plan">4DK</el-checkbox>
                                <el-checkbox label=4,4 name="floor_plan">4LDK</el-checkbox>
                                <el-checkbox label=5,5 name="floor_plan">5K以上</el-checkbox>
                            </el-row>
                        </el-checkbox-group>
                    </el-form-item>
                </el-col>
            </el-row>



            <template v-if="!hideLand">
                <el-row>
                    <el-col :span="23" :offset="1" class="type">土地面積</el-col>
                </el-row>
                <el-row>
                    <el-col :xs="{span:8,offset:2}" :sm="{span:6,offset:2}" :md="{span:4,offset:2}">
                        <el-form-item>
                            <el-select v-model="form.land_areaLow" clearable placeholder="下限なし" @change="clickSubmit()">
                                <template v-for="item in landArea">
                                    <template v-if=isLower(item.value,form.land_areaHigh)>
                                        <el-option :key="item.value" :label="item.label" :value="item.value">
                                        </el-option>
                                    </template>
                                </template>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="1" :offset="1" class="conditions_type">～</el-col>
                    <el-col :xs="{span:8,offset:1}" :sm="{span:6,offset:1}" :md="{span:4,offset:1}">
                        <el-form-item>
                            <el-select v-model="form.land_areaHigh" clearable placeholder="上限なし"
                                @change="clickSubmit()">
                                <template v-for="item in landArea">
                                    <template v-if=isHigher(item.value,form.land_areaLow)>
                                        <el-option :key="item.value" :label="item.label" :value="item.value">
                                        </el-option>
                                    </template>
                                </template>
                            </el-select>
                            </el-select>
                        </el-form-item>
                    </el-col>
                </el-row>
            </template>

            <template v-if="!hideBuild">
                <el-row>
                    <el-col :span="23" :offset="1" class="type">建物面積
                        <small>(専有面積)</small>
                    </el-col>
                </el-row>
                <el-row>
                    <el-col :xs="{span:8,offset:2}" :sm="{span:6,offset:2}" :md="{span:4,offset:2}">
                        <el-form-item>
                            <el-select v-model="form.building_areaLow" clearable placeholder="下限なし"
                                @change="clickSubmit()">
                                <template v-for="item in buildingArea">
                                    <template v-if=isLower(item.value,form.building_areaHigh)>
                                        <el-option :key="item.value" :label="item.label" :value="item.value">
                                        </el-option>
                                    </template>
                                </template>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="1" :offset="1" class="conditions_type">～</el-col>
                    <el-col :xs="{span:8,offset:1}" :sm="{span:6,offset:1}" :md="{span:4,offset:1}">
                        <el-form-item>
                            <el-select v-model="form.building_areaHigh" clearable placeholder="上限なし"
                                @change="clickSubmit()">
                                <template v-for="item in buildingArea">
                                    <template v-if=isHigher(item.value,form.building_areaLow)>
                                        <el-option :key="item.value" :label="item.label" :value="item.value">
                                        </el-option>
                                    </template>
                                </template>
                            </el-select>
                            </el-select>
                        </el-form-item>
                    </el-col>
                </el-row>
            </template>

            <el-row>
                <el-col :span="23" :offset="1" class="type">駅徒歩</el-col>
            </el-row>
            <el-row>
                <el-col :span="22" :offset="2">
                    <el-form-item>
                        <el-radio-group v-model="form.transport_time" @change="clickSubmit()">
                            <el-radio :label="1" name="transport_time">指定なし</el-radio>
                            <el-radio :label="3" name="transport_time">3分以内</el-radio>
                            <el-radio :label="5" name="transport_time">5分以内</el-radio>
                            <el-radio :label="10" name="transport_time">10分以内</el-radio>
                            <el-radio :label="15" name="transport_time">15分以内</el-radio>
                            <el-radio :label="20" name="transport_time">20分以内</el-radio>
                        </el-radio-group>
                    </el-form-item>
                </el-col>
            </el-row>


            <el-row>
                <el-col :span="23" :offset="1" class="type">築年数</el-col>
            </el-row>
            <el-row>
                <el-col :span="22" :offset="2">
                    <el-form-item>
                        <el-radio-group v-model="form.age" @change="clickSubmit()">
                            <el-radio :label="1" name="age">指定なし</el-radio>
                            <el-radio :label="3" name="age">3年以内</el-radio>
                            <el-radio :label="5" name="age">5年以内</el-radio>
                            <el-radio :label="10" name="age">10年以内</el-radio>
                            <el-radio :label="15" name="age">15年以内</el-radio>
                            <el-radio :label="20" name="age">20年以内</el-radio>
                        </el-radio-group>
                    </el-form-item>
                </el-col>
                </el-col>
            </el-row>

            <el-row>
                <el-col class="free-word" :xs="24" :span="19">
                    <el-form-item>
                        <el-input v-model="form.freeword" placeholder="フリーワード検索" prefix-icon="el-icon-search" clearable
                            @blur="clickSubmit()">
                        </el-input>
                    </el-form-item>
                </el-col>
            </el-row>

            <el-row style="margin-bottom:30px;">
                <el-col :span="22" :offset="1" class="acc">
                    <el-collapse accordion>
                        <el-collapse-item>
                            <template slot="title">
                                <div class="titleconditions">
                                    <i class="el-icon-minus"></i>追加条件を指定
                                </div>
                            </template>
                            <div class="addconditions">


                                <el-row style="padding-top: 20px; margin-bottom:10px;">
                                    <el-col :span="22" :offset="1" class="detail-serch-title">▼ 物件設備</el-col>
                                </el-row>
                                <el-row>
                                    <el-col :span="22" :offset="2">
                                        <el-form-item style="margin-bottom:0px;">
                                            <el-checkbox label="1" v-model="form.aion" name="aion"
                                                @change="clickSubmit()">
                                                エアコン
                                            </el-checkbox>
                                            <el-checkbox label="1" v-model="form.flat" name="flat"
                                                @change="clickSubmit()">
                                                床暖房</el-checkbox>
                                            <el-checkbox label="1" v-model="form.mone" name="mone"
                                                @change="clickSubmit()">
                                                TVモニター付インターホン
                                            </el-checkbox>
                                            <el-checkbox label="1" v-model="form.pang" name="pang"
                                                @change="clickSubmit()">
                                                駐車場
                                            </el-checkbox>
                                            <el-checkbox label="1" v-model="form.bile" name="bile"
                                                @change="clickSubmit()">
                                                駐輪場
                                            </el-checkbox>
                                        </el-form-item>
                                    </el-col>
                                </el-row>
                                <el-row>
                                    <el-col :span="22" :offset="2">
                                        <el-form-item>
                                            <el-checkbox label="1" v-model="form.flng" name="flng"
                                                @change="clickSubmit()">
                                                フローリング
                                            </el-checkbox>
                                            <el-checkbox label="1" v-model="form.elor" name="elor"
                                                @change="clickSubmit()">
                                                エレベーター
                                            </el-checkbox>
                                            <el-checkbox label="1" v-model="form.sera" name="sera"
                                                @change="clickSubmit()">
                                                防犯カメラ
                                            </el-checkbox>
                                            <el-checkbox label="1" v-model="form.deox" name="deox"
                                                @change="clickSubmit()">
                                                宅配BOX
                                            </el-checkbox>
                                            <el-checkbox label="1" v-model="form.auck" name="auck"
                                                @change="clickSubmit()">
                                                オートロック
                                            </el-checkbox>
                                        </el-form-item>
                                    </el-col>
                                </el-row>

                                <el-row style="margin-bottom:10px;">
                                    <el-col :span="22" :offset="1" class="detail-serch-title">▼ キッチン</el-col>
                                </el-row>
                                <el-row>
                                    <el-col :span="22" :offset="2">
                                        <el-form-item style="margin-bottom:0px;">
                                            <el-checkbox label="1" v-model="form.coen" name="coen"
                                                @change="clickSubmit()">
                                                カウンターキッチン
                                            </el-checkbox>
                                            <el-checkbox label="1" v-model="form.elve" name="elve"
                                                @change="clickSubmit()">
                                                IHコンロ
                                            </el-checkbox>
                                            <el-checkbox label="1" v-model="form.muve" name="muve"
                                                @change="clickSubmit()">
                                                コンロ２口以上
                                            </el-checkbox>
                                            <el-checkbox label="1" v-model="form.syen" name="syen"
                                                @change="clickSubmit()">
                                                システムキッチン
                                            </el-checkbox>
                                        </el-form-item>
                                    </el-col>
                                </el-row>
                                <el-row>
                                    <el-col :span="22" :offset="2">
                                        <el-form-item>
                                            <el-checkbox label="1" v-model="form.gale" name="gale"
                                                @change="clickSubmit()">
                                                ガスコンロ対応
                                            </el-checkbox>
                                        </el-form-item>
                                    </el-col>
                                </el-row>

                                <el-row style="margin-bottom:10px;">
                                    <el-col :span="22" :offset="1" class="detail-serch-title">▼ バス・トイレ</el-col>
                                </el-row>
                                <el-row>
                                    <el-col :span="22" :offset="2" style="margin-bottom:22px;">
                                        <el-checkbox label="1" v-model="form.shet" name="shet" @change="clickSubmit()">
                                            シャワートイレ</el-checkbox>
                                        <el-checkbox label="1" v-model="form.reat" name="reat" @change="clickSubmit()">
                                            追い焚き</el-checkbox>
                                        <el-checkbox label="1" v-model="form.bary" name="bary" @change="clickSubmit()">
                                            浴室乾燥</el-checkbox>
                                        <el-checkbox label="1" v-model="form.sher" name="sher" @change="clickSubmit()">
                                            シャンプードレッサー
                                        </el-checkbox>
                                        <el-checkbox label="1" v-model="form.baed" name="baed" @change="clickSubmit()">
                                            バス・トイレ別
                                        </el-checkbox>
                                    </el-col>
                                </el-row>

                                <el-row style="margin-bottom:10px;">
                                    <el-col :span="22" :offset="1" class="detail-serch-title">▼ 室内設備</el-col>
                                </el-row>
                                <el-row>
                                    <el-col :span="22" :offset="2" style="margin-bottom:22px;">
                                        <el-checkbox label="1" v-model="form.denk" name="denk" @change="clickSubmit()">
                                            独立洗面台
                                        </el-checkbox>
                                        <el-checkbox label="1" v-model="form.inry" name="inry" @change="clickSubmit()">
                                            室内洗濯機置き場
                                        </el-checkbox>
                                        <el-checkbox label="1" v-model="form.apce" name="apce" @change="clickSubmit()">
                                            家電付
                                        </el-checkbox>
                                    </el-col>
                                </el-row>

                                <el-row style="margin-bottom:10px;">
                                    <el-col :span="22" :offset="1" class="detail-serch-title">▼ 位置</el-col>
                                </el-row>
                                <el-row>
                                    <el-col :span="22" :offset="2" style="margin-bottom:22px;">
                                        <el-checkbox label="1" v-model="form.coom" name="coom" @change="clickSubmit()">
                                            角部屋</el-checkbox>
                                        <el-checkbox label="1" v-model="form.roth" name="roth" @change="clickSubmit()">
                                            南向き
                                        </el-checkbox>
                                        <el-checkbox label="1" v-model="form.floor_num_a" name="floor_num_a"
                                            @change="clickSubmit()">１階
                                        </el-checkbox>
                                        <el-checkbox label="1" v-model="form.floor_num_b" name="floor_num_b"
                                            @change="clickSubmit()">
                                            ２階以上
                                        </el-checkbox>
                                        <el-checkbox label="1" v-model="form.floor_num_c" name="floor_num_c"
                                            @change="clickSubmit()">最上階
                                        </el-checkbox>
                                    </el-col>
                                </el-row>

                                <el-row style="margin-bottom:10px;">
                                    <el-col :span="22" :offset="1" class="detail-serch-title">▼ 部屋の形状</el-col>
                                </el-row>
                                <el-row>
                                    <el-col :span="22" :offset="2" style="margin-bottom:22px;">
                                        <el-checkbox label="1" v-model="form.mate" name="mate" @change="clickSubmit()">
                                            メゾネット</el-checkbox>
                                        <el-checkbox label="1" v-model="form.loft" name="loft" @change="clickSubmit()">
                                            ロフト付き</el-checkbox>
                                    </el-col>
                                </el-row>

                                <el-row style="margin-bottom:10px;">
                                    <el-col :span="22" :offset="1" class="detail-serch-title">▼ 放送・通信</el-col>
                                </el-row>
                                <el-row>
                                    <el-col :span="22" :offset="2" style="margin-bottom:22px;">
                                        <el-checkbox label="1" v-model="form.catv" name="catv" @change="clickSubmit()">
                                            CATV</el-checkbox>
                                        <el-checkbox label="1" v-model="form.csna" name="csna" @change="clickSubmit()">
                                            CSアンテナ</el-checkbox>
                                        <el-checkbox label="1" v-model="form.bsna" name="bsna" @change="clickSubmit()">
                                            BSアンテナ</el-checkbox>
                                        <el-checkbox label="1" v-model="form.inee" name="inee" @change="clickSubmit()">
                                            インターネット無料
                                        </el-checkbox>
                                    </el-col>
                                </el-row>

                                <el-row style="margin-bottom:10px;">
                                    <el-col :span="22" :offset="1" class="detail-serch-title">▼ その他条件</el-col>
                                </el-row>
                                <el-row>
                                    <el-col :span="22" :offset="2" style="margin-bottom:22px;">
                                        <el-checkbox label="1" v-model="form.woly" name="woly" @change="clickSubmit()">
                                            女性専用</el-checkbox>
                                        <el-checkbox label="1" v-model="form.elok" name="elok" @change="clickSubmit()">
                                            高齢者相談</el-checkbox>
                                        <el-checkbox label="1" v-model="form.neke" name="neke" @change="clickSubmit()">
                                            新婚向け</el-checkbox>
                                        <el-checkbox label="1" v-model="form.imle" name="imle" @change="clickSubmit()">
                                            即入居可</el-checkbox>
                                        <el-checkbox label="1" v-model="form.bree" name="bree" @change="clickSubmit()">
                                            仲介手数料ゼロ</el-checkbox>
                                    </el-col>
                                </el-row>
                                <el-row>
                                    <el-col :span="22" :offset="2" style="margin-bottom:22px;">
                                        <el-checkbox label="1" v-model="form.maty" name="maty" @change="clickSubmit()">
                                            管理物件</el-checkbox>
                                        <el-checkbox label="1" v-model="form.pet" name="pet" @change="clickSubmit()">
                                            ペット可(相談含)
                                        </el-checkbox>
                                        <el-checkbox label="1" v-model="form.fole" name="fole" @change="clickSubmit()">
                                            分譲タイプ
                                        </el-checkbox>
                                        <el-checkbox label="1" v-model="form.frnt" name="frnt" @change="clickSubmit()">
                                            フリーレント
                                        </el-checkbox>
                                        <el-checkbox label="1" v-model="form.reed" name="reed" @change="clickSubmit()">
                                            リフォーム済み
                                        </el-checkbox>
                                    </el-col>
                                </el-row>
                                <el-row>
                                    <el-col :span="22" :offset="2" style="margin-bottom:22px;">
                                        <el-checkbox label="1" v-model="form.reon" name="reon" @change="clickSubmit()">
                                            リノベーション
                                        </el-checkbox>
                                        <el-checkbox label="1" v-model="form.guor" name="guor" @change="clickSubmit()">
                                            保証人不要
                                        </el-checkbox>
                                        <el-checkbox label="1" v-model="form.shre" name="shre" @change="clickSubmit()">
                                            ルームシェア</el-checkbox>
                                        <el-checkbox label="1" v-model="form.ders" name="ders" @change="clickSubmit()">
                                            デザイナーズ
                                        </el-checkbox>
                                        <el-checkbox label="1" v-model="form.prht" name="prht" @change="clickSubmit()">
                                            初期費用10万円以下
                                        </el-checkbox>
                                    </el-col>
                                </el-row>
                            </div>
                        </el-collapse-item>
                    </el-collapse>
                </el-col>
            </el-row>

            <el-row style="margin-bottom:30px;">
                <el-col class="search">
                    <el-link v-bind:href=queryparm :underline="false">
                        <el-button round class="b_search">
                            <i class="el-icon-search"></i>
                            この条件で検索
                        </el-button>
                    </el-link>
                </el-col>
            </el-row>
        </div>
        </el-col>
    </el-form>
</el-main>

<?php endif ?>

<?php include "./footer.php"; ?>

<script>
var app = new Vue({
    el: "#app",
    data: {
        drawer: false,  //ハンバーガーメニューのためのプロパティ

        queryparm: location.href.substring(0, location.href.indexOf('conditions')) +
            'estatelist.php?puse=' + <?php echo $puse; ?> + '&order=pickup&limit=10&page=0',
        hideLand: false,
        hideBuild: false,
        pricePuse: [{
            label: "1000万円",
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
            value: 10000.0
        }],
        priceNpuse: [{
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
        }],

        landArea: [{
            label: '50㎡',
            value: 50
        }, {
            label: '60㎡',
            value: 60
        }, {
            label: '70㎡',
            value: 70
        }, {
            label: '80㎡',
            value: 80
        }, {
            label: '90㎡',
            value: 90
        }, {
            label: '100㎡',
            value: 100
        }, {
            label: '110㎡',
            value: 110
        }, {
            label: '120㎡',
            value: 120
        }, {
            label: '130㎡',
            value: 130
        }, {
            label: '140㎡',
            value: 140
        }, {
            label: '150㎡',
            value: 150
        }, {
            label: '200㎡',
            value: 200
        }, {
            label: '300㎡',
            value: 300
        }, {
            label: '400㎡',
            value: 400
        }, {
            label: '500㎡',
            value: 500
        }],

        buildingArea: [{
            label: '50㎡',
            value: 50
        }, {
            label: '60㎡',
            value: 60
        }, {
            label: '70㎡',
            value: 70
        }, {
            label: '80㎡',
            value: 80
        }, {
            label: '90㎡',
            value: 90
        }, {
            label: '100㎡',
            value: 100
        }, {
            label: '110㎡',
            value: 110
        }, {
            label: '120㎡',
            value: 120
        }, {
            label: '130㎡',
            value: 130
        }, {
            label: '140㎡',
            value: 140
        }, {
            label: '150㎡',
            value: 150
        }, {
            label: '200㎡',
            value: 200
        }],




        form: {
            freeword: '',
            este: [],
            city: [],
            estate_priceLow: '',
            estate_priceHigh: '',
            feed: [],
            seed: [],
            keey: [],
            floor_plan: [],
            land_areaLow: '',
            land_areaHigh: '',
            building_areaLow: '',
            building_areaHigh: '',
            transport_time: 1,
            age: 1,
            pang: [],
            bile: [],
            baed: [],
            auck: [],
            pet: [],
            fole: [],
            inee: [],
            denk: [],
            inry: [],
            frnt: [],
            reed: [],
            reon: [],
            apce: [],
            guor: [],
            shre: [],
            ders: [],
            prht: [],
            floor_num_a: [],
            floor_num_b: [],
            floor_num_c: [],
            aion: [],
            flat: [],
            mone: [],
            flng: [],
            elor: [],
            sera: [],
            deox: [],
            coen: [],
            elve: [],
            muve: [],
            syen: [],
            gale: [],
            shet: [],
            reat: [],
            bary: [],
            sher: [],
            coom: [],
            roth: [],
            mate: [],
            loft: [],
            catv: [],
            csna: [],
            bsna: [],
            woly: [],
            elok: [],
            neke: [],
            imle: [],
            bree: [],
            maty: []
        }
    },
    methods: {
        window: onload = function() {
            let puse = <?php echo $puse; ?>;
            if (puse == 0) {
                document.getElementById("navRen").style.backgroundColor = '#fbdac8';
            } else {
                document.getElementById("navPur").style.backgroundColor = '#fbdac8';
            }
        },
        clickSubmit: function() {
            let estate = '';
            var eof = new RegExp('$');
            for (key in this.form) {
                if (this.form[key] != '') {
                    estate = estate + key + '=' + this.form[key] + '&';
                }
            }
            estate = this.urlSubmit() + '&' + estate;

            if (estate.indexOf('transport_time=1&') !== -1) {
                estate = estate.replace('transport_time=1&', "");
            } else if (estate.indexOf('transport_time=1'.eof) !== -1) {
                estate = estate.replace('transport_time=1'.eof, "");
            }
            if (estate.indexOf('age=1&') !== -1) {
                estate = estate.replace('age=1&', "");
            } else if (estate.indexOf('age=1'.eof) !== -1) {
                estate = estate.replace('age=1'.eof, "");
            }

            this.queryparm = estate.slice(0, -1);
            console.clear();
            console.log(this.queryparm);
        },
        urlSubmit: function() {
            var url = location.href;
            var result = url.indexOf('conditions');
            var http = url.substring(0, result);
            return http + 'estatelist.php?puse=' + <?php echo $puse; ?> + '&order=pickup&limit=10&page=0';
        },
        isLower: function(value, higher) {
            if (higher == '') {
                return true;
            } else if (value <= higher) {
                return true;
            } else {
                return false;
            }
        },
        isHigher: function(value, lower) {
            if (lower == '') {
                return true;
            } else if (value >= lower) {
                return true;
            } else {
                return false;
            }
        },
        areaHider: function() {
            if (this.form.este.includes('2') && !(this.form.este.includes('0') || this.form.este
                    .includes('1') || this.form.este.includes('3') || this.form.este.includes('4')
                )) {
                this.form.land_areaLow = '';
                this.form.land_areaHigh = '';
                this.hideLand = true;
            } else {
                this.hideLand = false;
            }

            if (this.form.este.includes('0') && !(this.form.este.includes('1') || this.form.este
                    .includes('2') || this.form.este.includes('3') || this.form.este.includes('4')
                )) {
                this.form.building_areaLow = '';
                this.form.building_areaHigh = '';
                this.hideBuild = true;
            } else {
                this.hideBuild = false;
            }
        },
        puseChecker: function(puse) {
            if (puse == 0) {
                return false;
            } else {
                return true;
            }
        }
    }
})
</script>

<link rel="stylesheet" type="text/css" href="../../www/css/conditions.css">

</html>