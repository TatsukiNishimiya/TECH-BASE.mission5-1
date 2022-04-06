<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>m5-1</title>
    </head>
    <body>
        <?php
        //データベース接続
            $dsn="mysql:dbname=データベース名;host=localhost";
            $user="ユーザー名";
            $password="パスワード";
            $pdo= new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            
        //テーブル作成
            $sql="CREATE TABLE IF NOT EXISTS mission5"  //テーブル名はmission5
            ."("
            ."id INT AUTO_INCREMENT PRIMARY KEY,"   //id(投稿番号)の項目を設定  INT~KEYまでは自動で数が増えていく設定らしい
            ."name char(32),"   //name(名前)の項目を設定    char(32)は32文字までっていう設定らしい
            ."comment TEXT,"    //comment(コメント)の項目を設定  TEXTはいっぱい文字書ける
            ."date char(32),"   //date(日付)の項目を設定
            ."password char(32)"    //password(パスワード)の項目を設定
            .");";
            $stmt=$pdo->query($sql);
            // echo "テーブル作成<br>";
            
        //編集番号送る
            if(!empty($_POST["edit"])){
                if(!empty($_POST["edit_pass"])){
                    $edit=$_POST["edit"];
                    $edit_pass=$_POST["edit_pass"];
                    
                    $sql="SELECT * FROM mission5 WHERE id=:id";
                    $stmt=$pdo->prepare($sql);
                    $stmt->bindParam(":id", $edit, PDO::PARAM_INT);
                    $stmt->execute();
                    $results=$stmt->fetchAll();
                    if($results==true){         //送った投稿が在る
                        if($results[0]["password"]==$edit_pass){    //パスワードが合ってる
                            foreach($results as $row){
                                $num=$row["id"];
                                $name=$row["name"];
                                $comment=$row["comment"];
                                $pass=$row["password"];
                            }
                        }else{      //パスワードが違う
                            $num="";
                            $name="";
                            $comment="";
                            $pass="";
                            $info="パスワードが違います。";
                        }
                    }else{      //送った投稿が無い
                        $num="";
                        $name="";
                        $comment="";
                        $pass="";
                        $info="投稿がありません。";
                    }
                }else{      //パスワードが空
                    $num="";
                    $name="";
                    $comment="";
                    $pass="";
                    $info="パスワードが空。";
                }
            }else{      //フォームが空
                $num="";
                $name="";
                $comment="";
                $pass="";
            }
        ?>
        <form action="" method="post">
            <input type="text" name="name" placeholder="名前" value="<?php echo $name;?>"><br>
            <input type="text" name="comment" placeholder="コメント" value="<?php echo $comment;?>"><br>
            <input type="hidden" name="edit_number" value="<?php echo $num;?>">
            <input type="text" name="password" placeholder="パスワード" value="<?php echo $pass;?>">
            <input type="submit" name="submit"><br>
            <br>
            <input type="number" name="delete" placeholder="削除対象番号"><br>
            <input type="text" name="delete_pass" placeholder="パスワード">
            <input type="submit" name="delete_submit" value="削除"><br>
            <br>
            <input type="number" name="edit" placeholder="編集対象番号"><br>
            <input type="text" name="edit_pass" placeholder="パスワード">
            <input type="submit" name="edit_submit" value="編集">
        </form>
        <?php
        //投稿
            if(!empty($_POST["name"]) && !empty($_POST["comment"])){
                if(!empty($_POST["password"])){
                    $name=$_POST["name"];
                    $comment=$_POST["comment"];
                    $date=date("Y年m月d日 H:i:s");
                    $password=$_POST["password"];
                //編集投稿
                    if(!empty($_POST["edit_number"])){
                        $sql="UPDATE mission5 SET name=:name, comment=:comment, date=:date, password=:password WHERE id=:id";
                        $stmt=$pdo->prepare($sql);
                        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
                        $stmt->bindParam(":name", $name, PDO::PARAM_STR);
                        $stmt->bindParam(":comment", $comment, PDO::PARAM_STR);
                        $stmt->bindParam(":date", $date, PDO::PARAM_STR);
                        $stmt->bindParam(":password", $password, PDO::PARAM_STR);
                        $id=$_POST["edit_number"];
                        $stmt->execute();
                        $info="編集成功";
                //新規投稿
                    }else{
                        $sql=$pdo->prepare("INSERT INTO mission5 (name, comment, date, password) VALUES (:name, :comment, :date, :password)");
                        $sql->bindParam(":name", $name, PDO::PARAM_STR);
                        $sql->bindParam(":comment", $comment, PDO::PARAM_STR);
                        $sql->bindParam(":date", $date, PDO::PARAM_STR);
                        $sql->bindParam(":password", $password, PDO::PARAM_STR);
                        $sql->execute();
                        $info="投稿成功";
                    }
                }else{
                    $info="パスワードが空。";
                }
            }
            
        //削除
            if(!empty($_POST["delete"])){
                if(!empty($_POST["delete_pass"])){
                    $id=$_POST["delete"];
                    $delete_pass=$_POST["delete_pass"];
                    
                    $sql="SELECT * FROM mission5 where id=:id";
                    $stmt=$pdo->prepare($sql);
                    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
                    $stmt->execute();
                    $result=$stmt->fetchAll();
                    if($result==true){
                        if($result[0]["password"]==$delete_pass){
                            $sql="delete from mission5 where id=:id";
                            $stmt=$pdo->prepare($sql);
                            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
                            $stmt->execute();
                            $info="削除完了";
                        }else{
                            $info="パスワードが違います。";
                        }
                    }else{
                        $info="投稿がありません。";
                    }
                }else{
                    $info="パスワードが空。";
                }
            }
            
        //エラーとか諸々表示
            if(!empty($info)){
                echo $info."<br>";
                echo "<br>";
            }else{
                echo "<br>";
                echo "<br>";
            }
            
        //ブラウザに表示
            $sql="SELECT * FROM mission5";
            $stmt=$pdo->query($sql);
            $results=$stmt->fetchAll();
            foreach($results as $line){
                echo $line["id"]." ".$line["name"]." ".$line["comment"]." ".$line["date"];
                echo "<br>";
            }
            // echo "表示";
        ?>
    </body>
</html>