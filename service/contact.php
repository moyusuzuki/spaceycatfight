<?php
    date_default_timezone_set('Asia/Tokyo');  //時間の設定を東京に

    $user_name = null;//ユーザの名前
    $user_frigana = null;//ユーザのフリガナ
    $phone_number = null;//電話番号
    $desired_time = null;//電話希望時間帯
    $user_email = null;//Email
    $contact_contents = null;//問合せ内容
    $send_result_true = false;//送信成功時true
    $send_result_false = false;//送信失敗時true

    //情報が送信されたら
    if(!empty($_POST['user_name'])) {
        //お問い合わせフォームの情報を変数へ代入
        $user_name = $_POST['user_name'];
        $user_frigana = $_POST['user_frigana'];
        $phone_number = $_POST['phone_number'];
        $desired_time = $_POST['desired_time'];
        $user_email = $_POST['user_email'];
        $contact_contents = $_POST['contact_contents'];

        //サニタイズ化
        $user_name = htmlspecialchars($user_name, ENT_QUOTES);
        $user_name = preg_replace('/\\r\\n|\\n|\\r/','',$user_name);

        $user_frigana = htmlspecialchars($user_frigana, ENT_QUOTES);
        $user_frigana = preg_replace('/\\r\\n|\\n|\\r/','',$user_frigana);

        $phone_number = htmlspecialchars($phone_number, ENT_QUOTES);
        $phone_number = preg_replace('/\\r\\n|\\n|\\r/','',$phone_number);

        $desired_time = htmlspecialchars($desired_time, ENT_QUOTES);
        $desired_time = preg_replace('/\\r\\n|\\n|\\r/','',$desired_time);

        $user_email = htmlspecialchars($user_email, ENT_QUOTES);
        $user_email = preg_replace('/\\r\\n|\\n|\\r/','',$user_email);

        $contact_contents = htmlspecialchars($contact_contents, ENT_QUOTES);

        //メール送信の文字設定
        mb_language("Japanese");
        mb_internal_encoding("UTF-8");

        //送信に必要な内容設定
        $to = "c-ito@just-fit.net";  //宛先
        $from = $user_email;
        $subject = "【物件お問い合わせ】";
        $body = <<<EOT
        【名前】{$user_name}様
        【フリガナ】{$user_frigana}様
        【電話番号】{$phone_number}
        【電話希望時間帯】{$desired_time}時
        【Email】{$user_email}
        【内容】
        {$contact_contents}
        EOT;

        //送信処理
        if(mb_send_mail($to, $subject, $body, "From {$from}")) {
            $send_result = true;//送信成功したら
        } else {
            $send_result = false;//送信失敗したら
        }



    }
 ?>
<!-- </header>までが入る ======================== -->
<?php 
    include "./header.php";
?>
<!-- ========================================== -->



<el-main class="main">

<!-- 送信が完了したらメッセージを表示する -->
<?php if($send_result_true): ?>
<template>
    <el-alert title="お問い合わせ内容の送信が完了しました。" type="success" show-icon></el-alert>
</template>
<?php endif ?>
<!-- 送信が失敗したらメッセージを表示する -->
<?php if($send_result_false): ?>
<template>
    <el-alert title="お問い合わせ内容の送信が失敗しました。" type="error" show-icon></el-alert>
</template>
<?php endif ?>

    <el-row>
        <el-col :span="24" class="link-map">
            <el-link href="./index.php" type="primary">ホーム</el-link> <i class="el-icon-arrow-right"></i> お問い合わせ
        </el-col>


        <el-col class="form-bg" :span="24">

            <div class="form-items">


                <el-form name="form" method="post" :model="ruleForm" :rules="rules" ref="ruleForm" label-width="120px"
                    label-position="top">

                    <!-- お名前記入欄 -->
                    <el-form-item label="お名前" prop="userName">
                        <el-input name="user_name" v-model="ruleForm.userName"></el-input>
                    </el-form-item>

                    <!-- フリガナ記入欄 -->
                    <el-form-item label="フリガナ" prop="userFrigana">
                        <el-input name="user_frigana" v-model="ruleForm.userFrigana"></el-input>
                    </el-form-item>

                    <!-- 電話番号記入欄 -->
                    <el-form-item label="電話番号" prop="phoneNumber">
                        <el-input name="phone_number" v-model="ruleForm.phoneNumber"></el-input>
                    </el-form-item>

                    <!-- 希望時間選択 -->
                    <el-row type="flex" justify="space-between">
                        <el-col :xs="0" :span="10"></el-col>
                        <el-col :xs="24" :span="14">
                            <el-time-select name="desired_time" class="desired-time" v-model="desiredTime"
                                :picker-options="{
                            start: '08:00', step: '00:30', end: '21:30'}" placeholder="電話希望時間帯をご選択ください">
                            </el-time-select>
                        </el-col>
                    </el-row>

                    <!-- Email記入欄 -->
                    <el-form-item label="Email" prop="userEmail">
                        <el-input name="user_email" v-model="ruleForm.userEmail"></el-input>
                    </el-form-item>

                    <!-- お問い合わせ内容記入欄 -->
                    <el-form-item label="お問い合わせ内容" prop="contactContents">
                        <el-input name="contact_contents" type="textarea" class="contact-contents"
                            v-model="ruleForm.contactContents">
                        </el-input>
                    </el-form-item>

                    <!-- 個人情報の取り扱いについて -->
                    <el-col class="personal-info">
                        <p>1. 個人情報の取扱い</p>
                        <br>
                        <p>1. お客様の個人情報の収集にあたっては、情報主体に対して収集目的を明らかにし、同意をいただいた上で収集します。収集した個人情報は利用範囲を限定し、適切に取り扱います。</p>
                        <br>
                        <p>2. 個人情報への不正アクセス、又は破壊、漏洩等のリスクに関しましては、合理的な安全対策を講じると共に予防並びに是正に関する機能を保有して、適切な個人情報保護対策を実施します。</p>
                    </el-col>

                    <el-col class="agree-check">
                        <el-form-item prop="agreeCheck" :show-message="false">
                            <el-checkbox-group v-model="ruleForm.agreeCheck">
                                <el-checkbox label="同意する"></el-checkbox>
                            </el-checkbox-group>
                        </el-form-item>
                    </el-col>

                    <el-col class="submit-btn-box">
                        <el-form-item>
                            <el-button class="submit-btn" type="warning" plain @click="formSubmit('ruleForm')">送信
                            </el-button>
                        </el-form-item>
                    </el-col>


                </el-form>

            </div>

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
        drawer: false,  //ハンバーガーメニューのためのプロパティ
        
        desiredTime: '',
        ruleForm: {
            userName: '',
            userFrigana: '',
            phoneNumber: '',
            userEmail: '',
            contactContents: '',
            agreeCheck: []
        },
        rules: {
            userName: [{
                    required: true,
                    message: 'お名前をご記入ください',
                    trigger: 'change'
                },
                {
                    min: 1,
                    max: 100,
                    message: '100文字以内でご記入ください'
                }
            ],
            userFrigana: [{
                    required: true,
                    message: 'フリガナをご記入ください',
                    trigger: 'change'
                },
                {
                    min: 1,
                    max: 100,
                    message: '100文字以内でご記入ください'
                }
            ],
            phoneNumber: [{
                required: true,
                message: '電話番号をご記入ください',
                trigger: 'change'
            }],
            userEmail: [{
                    required: true,
                    message: 'Emailをご記入ください',
                    trigger: 'change'
                },
                {
                    min: 1,
                    max: 100,
                    message: '100文字以内でご記入ください'
                },
                {
                    type: 'email',
                    message: 'メールアドレス以外のご入力はできません',
                }
            ],
            contactContents: [{
                    required: true,
                    message: 'お問い合わせ内容をご記入ください',
                    trigger: 'change'
                },
                {
                    min: 1,
                    max: 3000,
                    message: '3000文字以内でご記入ください'
                }
            ],
            agreeCheck: [{
                required: true,
                message: '',
                trigger: 'change'
            }]
        }
    },
    methods: {
        formSubmit(formName) {
            this.$refs[formName].validate((valid) => {
                if (valid) {
                    this.$confirm('本当に送信してもよろしいでしょうか。', '送信確認', {
                        confirmButtonText: 'OK',
                        cancelButtonText: 'Cancel',
                        type: 'info'
                    }).then(() => {
                        document.form.submit();
                    }).catch(() => {
                        return false;
                    });
                } else {
                    this.$message({
                        showClose: true,
                        message: '入力内容に誤りがあるため送信できませんでした。',
                        type: 'error'
                    });
                    return false;
                }
            });
        },
    }

})
</script>
<!-- 自作のcss読み込み -->
<link rel="stylesheet" href="../../www/css/contact.css?<?php echo date('Ymd-His'); ?>">

</html>