<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
    <title>確認画面</title>
    
</head>

<body>
<!-- </header>までが入る ======================== -->
<?php 
    include "./header.php";
?>
<!-- ========================================== -->
<form action="d_thanks.php" method="POST">
<div class="top">
<div class="top_title"><p>お問い合わせ</p></div>
</div>
<div class="item_wrap">
<table>
        <tbody>
            <tr> 
                <th>
                    お問い合わせ内容
                </th>
                <td>
                <p><?php echo $_POST['contents']?>
                    <input type="hidden" name="user_name" value="<?php echo $_POST['contents']?>"></p>
                </td>
                </tr>
                <tr>
                <th>
                    お名前
                </th>
                <td>
                    <p><?php echo $_POST['user_name']?>
                    <input type="hidden" name="user_name" value="<?php echo $_POST['user_name']?>"></p>
                </td>
                </tr>
                <tr>
                <th>
                    メールアドレス
                </th>
                <td>
                　　<p><?php echo $_POST['email']?>
                    <input type="hidden" name="email" value="<?php echo $_POST['email']?>"></p>
                </td>
                </tr> 
  
        </tbody>
    </table>
</div>

<el-main class="submit">
<el-button native-type="submit">送信</el-button>
</el-main>
</form>


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

<link rel="stylesheet" href="../../www/css/d_check.css">

    
</body>
</html>