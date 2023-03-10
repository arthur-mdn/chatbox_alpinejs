<?php
session_start();
if(isset($_SESSION['UserId'])) {
    header('Location: index.php');
    die();
}
require ('config.php');
//var_dump(		 password_hash('abc', PASSWORD_DEFAULT));
if(isset($_POST['username']) and isset($_POST['password'])) {
//    var_dump($_POST);

    $query = $conn2->prepare("
                        SELECT * FROM users WHERE UserLogin = ? and UserStatut = 0 ;
							");
    $query->bindValue(1, $_POST['username']);
    $query->execute();
    $result = $query->fetchAll(PDO::FETCH_ASSOC);
//    var_dump($result);
    if(empty($result)){
        echo 'Introuvable';
    }else{
        $result = $result[0];
//        var_dump($result);
//        var_dump(password_verify($_POST['password'],$result['UserPassword']));
        if(password_verify($_POST['password'],$result['UserPassword'])){
            $_SESSION['UserId'] = $result['UserId'];
            header('Location: index.php');
            die();
        }else{
            echo 'mauvais mdp';
        }
    }
}
?>

<html>
<head>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
<form method="post">
    <label>Nom d'utilisateur</label><br>
    <input type="text" maxlength="32" name="username"><br>
    <label>Mot de passe</label><br>
    <input type="password" name="password"><br><br>
    <input type="submit"><br>
    <a href="register.php">Créer un compte</a>
</form>

</body>
</html>
