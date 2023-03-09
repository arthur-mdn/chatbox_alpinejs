<?php
session_start();
if (!isset($_SESSION['UserId'])) {
    header('Location: login.php');
    die();
} else {


?>

<html>
<head>
    <link rel="stylesheet" href="public/css/style.css">
    <script defer src="public/js/cdn.min.js"></script>
    <script defer src="public/js/script.js"></script>
</head>
<body>
<script>
    const logged_user = <?php echo $_SESSION['UserId'];?>;
</script>

    <div id="main-selector">

    </div>
    <div id="main">

    </div>
</body>
</html>
<?php
}
?>