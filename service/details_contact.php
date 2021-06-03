<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
    <title>JUST FIT  ESTATE</title>
</head>

<body>
<!-- </header>までが入る ======================== -->
<?php 
    include "./header.php";
?>
<!-- ========================================== -->
<form action="d_check.php" method="post">
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
                    <ul>
                        <li>
                        <input type="radio" name="contents" value="最新の空席情報を知りたい">最新の空席状況を知りたい
                        </li>
                        <li>
                        <input type="radio" name="contents" value="初期費用を知りたい">初期費用を知りたい
                        </li>
                        <li>
                        <input type="radio" name="contents" value="実際に見学したい">実際に見学したい
                        </li>
                    </ul>
                </td>
                </tr>
                <tr>
                <th>
                    お名前
                </th>
                <td>
                <input type="text" name="user_name" value="<?php if(!empty($_POST['user_name']))
                {echo $_POST['user_name'];}?>">
                </td>
                </tr>
                <tr>
                <th>
                    メールアドレス
                </th>
                <td>
                <input type="mail" name="email" value="<?php if(!empty($_POST['email']))
                {echo $_POST['email'];}?>">
                </td>
                </tr> 
  
        </tbody>
    </table>
</div>

<el-main class="submit">
<el-button native-type="submit">確認画面へ進む</el-button>
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

<link rel="stylesheet" href="../../www/css/d_contact.css">


    
</body>

</html>