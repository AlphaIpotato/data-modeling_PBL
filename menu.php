<?php
session_start();

// Check if restaurant ID is provided
if (!isset($_GET['restaurantID'])) {
    header("Location: index.php");
    exit();
}

$restaurantID = $_GET['restaurantID'];

$host = "localhost";
$user = "user";
$password = "12345";
$database = "sample";

// MySQL connection setup
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("MySQL connection failed: " . $conn->connect_error);
}

// Query to get menu items for the selected restaurant
$menuQuery = "SELECT * FROM 메뉴 WHERE 가게ID = '$restaurantID'";
$menuResult = $conn->query($menuQuery);

// Initialize menu items array
$menuItems = [];

if ($menuResult->num_rows > 0) {
    while ($menuItem = $menuResult->fetch_assoc()) {
        $menuItems[] = $menuItem;
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>가게 메뉴</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
        }

        h1 {
            text-align: center;
        }

        form {
            max-width: 600px;
            margin: 0 auto;
        }

        div {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
        }

        h3 {
            margin: 0;
        }

        label {
            font-size: 14px;
        }

        button {
            position: absolute;
            left: 20px;
            top: 20px;
            padding: 10px;
            font-size: 16px;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <h1>가게 메뉴</h1>

    <form method="post" action="checkout.php">
        <?php foreach ($menuItems as $item): ?>
            <div>
                <h3><?= $item['메뉴이름'] ?></h3>
                <p>가격: <?= $item['가격'] ?></p>
                <p><?= $item['메뉴정보'] ?></p>
                <label for="quantity_<?= $item['메뉴ID'] ?>">수량:</label>
                <input type="number" name="quantity_<?= $item['메뉴ID'] ?>" id="quantity_<?= $item['메뉴ID'] ?>" value="0" min="0" max="5">
            </div>
        <?php endforeach; ?>

        <input type="submit" value="주문하기">
    </form>

    <!-- 뒤로가기 버튼 추가 -->
    <button onclick="goBack()">뒤로가기</button>

    <script>
        function goBack() {
            window.history.back();
        }
    </script>

</body>
</html>