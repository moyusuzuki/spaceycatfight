<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>お問い合わせ完了</title>
</head>
<body>
<!-- </header>までが入る ======================== -->
<?php 
    include "./header.php";
?>
<!-- ========================================== -->

    <div><h1>お問い合わせ完了</h1></div>
    <p>お問い合わせいただきまして誠にありがとうございました。<br>
        内容を確認の上、回答をさせていただきます。<br>
        今しばらくお待ちくださいませ。</p>

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

<link rel="stylesheet" href="../../www/css/d_thanks.css">

</body>
</html>