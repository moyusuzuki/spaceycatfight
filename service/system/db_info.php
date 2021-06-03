<?php

    /**
     * @return dbh データベース接続したものを返す
     */
    function dbConnect() {
        $S_AD = $_SERVER['SERVER_ADDR'];
        $R_AD = $_SERVER['REMOTE_ADDR'];

        //本番だったら
        if(substr($S_AD,0,mb_strrpos($S_AD,'.')) != substr($R_AD,0,mb_strrpos($R_AD,'.'))) {
            $dsn = 'mysql:host=mysql133.phy.lolipop.lan;dbname=LAA0773156-justfitdb;charset=utf8';
            $user = 'LAA0773156';
            $pass = 'Dv7s9WPh';
        } else {  //ローカルだったら
            $dsn = 'mysql:host=localhost;dbname=justfitestate;charset=utf8';
            $user = 'justfitestate';
            $pass = 'justfitestate';
        }
        

        try{
            $dbh = new PDO($dsn, $user, $pass,[
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            $dbh->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true); 
        } catch (PDOException $e) {
            echo '接続失敗' . $e->getMessage();
            exit();
        }
        return $dbh;
    }

?>