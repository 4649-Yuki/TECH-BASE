<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-2</title>
</head>
<body>
<?php
    // DB接続設定
    $dsn = 'mysql:dbname=データベース名;host=localhost';
    $user = 'XXXUSER';
    $password = 'XXXPASSWARD';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    $sql = "CREATE TABLE IF NOT EXISTS bsstable"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name CHAR(32),"
    . "str TEXT,"
    . "date DATETIME,"
    . "pass TEXT"
    .");";
    $stmt = $pdo->query($sql);

    if(!empty($_POST["name"]) && !empty($_POST["str"]) && !empty($_POST["pass_full"])){

        $name = $_POST["name"];
        $str = $_POST["str"];
        $pass = $_POST["pass_full"];
        $date = date("Y/n/j G:i:s");

        //編集機能書き出し
        if(!empty($_POST["edit_num"])){
            $edit_num = $_POST["edit_num"];

            $id = $edit_num;
            $stmt = $pdo->prepare('UPDATE bsstable SET name=:name, str=:str, date=:date, pass=:pass WHERE id=:id');
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':str', $str, PDO::PARAM_STR);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        }else{
            //新規投稿
            $stmt = $pdo->prepare("INSERT INTO bsstable (name, str, date, pass) VALUES (:name, :str, :date, :pass)");
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':str', $str, PDO::PARAM_STR);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
            $stmt->execute();

            $lastId = $pdo->lastInsertId();
        }
    }else if(!empty($_POST["rnum"]) && !empty($_POST["pass_del"])){ 
        $rnum = $_POST["rnum"];
        $pass_del = $_POST["pass_del"];

        $id = $rnum;
        $stmt = $pdo->prepare('SELECT * FROM bsstable WHERE id = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();

        if($result['pass'] == $pass_del){
            $stmt = $pdo->prepare('DELETE FROM bsstable WHERE id=:id');
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            echo "パスワードが違います。"."<br>";
        }
    }
    if(!empty($_POST["edit"]) && !empty($_POST["pass_edit"])){//編集機能準備
        $edit_num = $_POST["edit"];
        $pass_edit = $_POST["pass_edit"];

        $id = $edit_num;
        $stmt = $pdo->prepare('SELECT * FROM bsstable WHERE id = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();

        if($result['pass'] == $pass_edit){
            $edit_name = $result['name'];
            $edit_str = $result['str'];
        }else{
            echo "パスワードが違います。"."<br>";
        }
    }

?>
<form action=" " method="post">
    <input type="text" name="name" placeholder="名前" value = <?php if(!empty($edit_name)){echo $edit_name;}?>><br>
    <input type="text" name="str" placeholder="コメント" value = <?php if(!empty($edit_str)){echo $edit_str;}?>><br>
    <input type="text" name="pass_full" placeholder="パスワード">
    <input type="hidden" name="edit_num" placeholder="編集したい行番号" value = <?php if(!empty($edit_num)){echo $edit_num;}?>>
    <input type="submit" name="submit"><br><br>

    <input type="number" name="rnum" placeholder="行番号"><br>
    <input type="text" name="pass_del" placeholder="認証パスワード">
    <input type="submit" name="submit" value = "削除"><br><br>

    <input type="number" name="edit" placeholder="行番号"><br>
    <input type="text" name="pass_edit" placeholder="認証パスワード">
    <input type="submit" name="submit" value = "編集"><br><br>
</form>
<?php
    //表示
    $sql = 'SELECT * FROM bsstable';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る
        echo $row['id'].',';
        echo $row['name'].',';
        echo $row['str'].',';
        echo $row['date'].',';
        echo $row['pass'].'<br>';
        echo "<hr>";
    }
?>
</body>
</html>
