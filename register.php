<?php
session_start();
if(isset($_SESSION['UserId'])) {
    header('Location: index.php');
    die();
}
require ('config.php');
//var_dump(		 password_hash('abc', PASSWORD_DEFAULT));
if(isset($_POST['username']) and isset($_POST['password']) and isset($_POST['firstname']) and isset($_POST['lastname'])) {
//    var_dump($_POST);

    $query = $conn2->prepare("
                        SELECT * FROM users WHERE UserLogin = ? and UserStatut = 0 ;
							");
    $query->bindValue(1, $_POST['username']);
    $query->execute();
    $result = $query->fetchAll(PDO::FETCH_ASSOC);
//    var_dump($result);
    if(!empty($result)){
        echo 'Déjà un compte avec ce nom d\'utilisateur.';
    }else{
        $newId = $conn2->query("SELECT MAX(UserId) + 1 FROM users")->fetchColumn();

        $query = $conn2->prepare("
                        INSERT INTO users (UserId, UserLogin, UserPassword, UserNom, UserPrenom) VALUES (?, ?, ?, ?, ?) ;
							");
        $query->bindValue(1, $newId);
        $query->bindValue(2, $_POST['username']);
        $query->bindValue(3, password_hash($_POST['password'], PASSWORD_DEFAULT));
        $query->bindValue(4, $_POST['lastname']);
        $query->bindValue(5, $_POST['firstname']);
        $query->execute();

        $_SESSION['UserId'] = $newId;
        header('Location: index.php');
        die();
    }
}
?>

<html>
<head>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
<form method="post">
    <label>Prénom</label><br>
    <input type="text" maxlength="32" name="firstname"><br>
    <label>Nom de famille</label><br>
    <input type="text" maxlength="32" name="lastname"><br>
    <label>Nom d'utilisateur</label><br>
    <input type="text" maxlength="32" name="username"><br>
    <label>Mot de passe</label><br>
    <input type="password" name="password"><br><br>
    <input type="submit" value="Créer un compte"><br>
    <a href="login.php">Se connecter</a>
</form>

</body>
</html>
