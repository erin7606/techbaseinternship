<!DOCKTYPE HTML>
<html lang = "ja">
    <head>
        <meta charset = "utf-8">
        <title>掲示板5-2</title>
    </head>
    <body>
        <?php

          $dsn = 'mysql:dbname=データベース名;host=localhost';
          $user = 'ユーザー名';
          $password = 'パスワード';
          $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE=> PDO::ERRMODE_WARNING));

          $sql = "CREATE TABLE IF NOT EXISTS tbtest5_2"
	      ." ("
	      . "id INT AUTO_INCREMENT PRIMARY KEY,"
	      . "show_name char(32),"
          . "comment TEXT,"
          . "password1 TEXT,"
          . "now_date TEXT"
	      .");";
          $stmt = $pdo->query($sql);
        
         //ここからは書き込み機能
          if(isset($_POST["normal"])==true){ //送信ボタンが押されたら
             $signal = $_POST["signal"]; 
             if(empty($signal)){ //編集確認用の欄が空であれば
                 $show_name = $_POST["name"];
                 $comment = $_POST["comment"];
                 $password1 = $_POST["password1"];
                 $now_date = date("Y-m-d H:i:s");
                 if($show_name !="" && $comment !=""){ //名前とコメント欄ともに空でなければ
                     $sql = $pdo -> prepare("INSERT INTO tbtest5_2 (show_name, comment, 
                     password1, now_date) VALUES(:show_name, :comment, :password1, :now_date)");
                     $sql -> bindParam(':show_name', $show_name, PDO::PARAM_STR);
                     $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                     $sql -> bindParam(':password1', $password1, PDO::PARAM_STR);
                     $sql -> bindParam(':now_date', $now_date, PDO::PARAM_STR);
                     $sql -> execute();
                 }
                 else{echo "<font color=\"red\">名前とコメントを入力してください</font>";
                 }
             }
           }
         //フォーム欄から書き込めました

         //ここからは削除機能

          if(isset($_POST["delete2"])==true){ //削除ボタンが押されたら
              $sql = 'SELECT * FROM tbtest5_2';
              $stmt = $pdo->prepare($sql);                  
              $stmt->bindParam(':id', $id, PDO::PARAM_INT); 
              $stmt->execute();           
              $results = $stmt->fetchAll(); //全てを配列に入れる
              foreach ($results as $row){
		    
                 $delete = $_POST["delete"];
                 $password2 = $_POST["password2"];
           
                 if($delete == $row['id'] && $row['password1'] == ""){ //もともとパスワードがない場合 
                     echo "<font color=\"red\">パスワードのないコメントは削除できません</font>";}
                 elseif($delete == $row['id'] && $password2 == $row['password1']){ //削除番号とパスワードが合っていれば
                     $id = $delete;
                     $sql = 'delete from tbtest5_2 where id=:id';
                     $stmt = $pdo->prepare($sql);
                     $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                     $stmt->execute();}
                 elseif($delete == $row['id'] && $password2 != $row['password1']){
                     echo "<font color=\"red\">削除番号とパスワードが一致しません</font>"; //番号あるいはパスワードが合わない場合
                 }
              }
           }
         //指定された行を削除できました

         //ここからは編集パスワード確認機能

           if(isset($_POST["edit2"])==true){ //編集ボタンが押されたら
                $sql = 'SELECT * FROM tbtest5_2';
                $stmt = $pdo->prepare($sql);                  
                $stmt->bindParam(':id', $id, PDO::PARAM_INT); 
                $stmt->execute();           
                $results = $stmt->fetchAll();
                foreach($results as $row){
                     $edit = $_POST["edit"];
                     $password3 = $_POST["password3"];
                     if($edit == $row['id'] && $row['password1'] == ""){ //もともとパスワードがない場合
                        echo "<font color=\"red\">パスワードのないコメントは編集できません</font>";
                    }
                     elseif($edit == $row['id'] && $password3 == $row['password1']){
                        //編集番号とパスワードが合っていれば
                        $data0 = $row['id'];
                        $data1 = $row['show_name'];
                        $data2 = $row['comment'];
                    }
                     elseif($edit == $row['id'] && $password3 != $row['password1']){
                         echo "<font color=\"red\">編集番号とパスワードが一致しません</font>";
                    }
                }
            }
         //編集したい内容をフォーム欄に反映できました。
         
         //ここからはupdate文を使って、指定行の内容を編集する。

           if(isset($_POST["normal"])==true){//送信ボタンが押されれば
                 $signal = $_POST["signal"];
                 if($signal !=""){ //編集確認欄が空欄でない場合
                    $id = $signal;
                    $show_name = $_POST["name"];
                    $comment = $_POST["comment"];
                    $password1 = $_POST["password1"];
                    $now_date = date("Y-m-d H:i:s");
                    if($_POST["name"] != "" && $_POST["comment"] !=""){ //名前とコメントが空欄でない場合
                      $sql = 'UPDATE tbtest5_2 SET show_name=:show_name,
                      comment=:comment,password1=:password1,now_date=:now_date WHERE id=:id';
                      $stmt = $pdo->prepare($sql);
                      $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
                      $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
                      $stmt -> bindParam(':show_name', $show_name, PDO::PARAM_STR);
                      $stmt -> bindParam(':password1', $password1, PDO::PARAM_STR);
                      $stmt -> bindParam(':now_date', $now_date, PDO::PARAM_STR);
                      $stmt -> execute();
                    }
                    else{echo"<font color=\"red\">名前とコメントを入力してください</font>";}
                 }
                 
            }
        //編集機能終わり
        
        ?>
            
        <form method = "POST" action = "">
        名前、コメント、パスワードを入力してください<br>
        <input type = "text" name = "name"
        placeholder = "名前"
        value = "<?php if (!empty($data1)) echo($data1); ?>"><br>
        <input type = "text" name = "comment"
        placeholder = "コメント"
        value = "<?php  if (!empty($data2)) echo($data2); ?>"><br>
        <input type = "text" name = "password1"
        placeholder = "パスワード"><br>
        ⋆パスワードのないコメントは後から編集、削除できません
        <br>
        <input type = "submit" name = "normal" value = "送信">
        <br>
        
        <input type = "hidden" name = "signal"
        value = "<?php  if (!empty($data0)) echo($data0); ?>" ><br>
        
        <br>
        <input type = "text" name ="delete"
        placeholder = "投稿番号"><br>
        <input type = "text" name = "password2"
        placeholder = パスワード><br>
        <input type = "submit" name = "delete2" value = "削除">
        <br>
        <br>
        <input type = "text" name = "edit"
        placeholder = "投稿番号"><br>
        <input type = "text" name = "password3"
        placeholder = "パスワード"><br>
        <input type = "submit" name = "edit2" value = "編集">
        <hr>
        </form> 

        
        <?php
        //ブラウザ表示機能
         $sql = 'SELECT * FROM tbtest5_2';
         $stmt = $pdo->prepare($sql);                  
         $stmt->bindParam(':id', $id, PDO::PARAM_INT); 
         $stmt->execute();           
         $results = $stmt->fetchAll();
         foreach ($results as $row){
            echo $row['id'].',';
            echo $row['show_name'].',';
            echo $row['comment'].',';
            echo $row['password1'].',';
            echo $row['now_date'].'<br>';}
        //データをブラウザに表示できました
        ?>
    </body>
</html>