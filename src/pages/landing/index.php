<?php
session_start();
include(__DIR__ . '/../../../server/db.php');

if(isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'user') {
        header("Location: ../home/index.php");
        exit();
    } else {
        header("Location: ../adminDashboard");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Wonderpets - Find Your Perfect Pet</title>
        <link rel="stylesheet" href="index.css">
        <link rel="stylesheet" href="components/header/header.css">
        <link rel="stylesheet" href="components/hero/hero.css">
    </head>
    <body>
        <?php include 'components/header/header.php'; ?>
        <main>
            <?php include 'components/hero/hero.php'; ?>
        </main>
    </body>
</html>