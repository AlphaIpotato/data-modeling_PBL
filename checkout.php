<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 선택한 메뉴와 수량을 받아옴
    $selectedMenu = [];

    foreach ($_POST as $key => $value) {
        if (strpos($key, 'quantity_') !== false) {
            $menuID = substr($key, strlen('quantity_'));
            $quantity = (int)$value;

            // 메뉴 ID와 수량을 배열에 저장
            $selectedMenu[$menuID] = $quantity;
        }
    }

    // 결제 수단, 요청사항, 주소
    $paymentMethod = isset($_POST['payment_method']) ? htmlspecialchars($_POST['payment_method']) : '';
    $requestNote = isset($_POST['request_note']) ? htmlspecialchars($_POST['request_note']) : '';
    $address = isset($_POST['address']) ? htmlspecialchars($_POST['address']) : '';

    // 선택한 메뉴 정보 및 총 금액을 세션에 저장
    $_SESSION['selectedMenu'] = $selectedMenu;
    $_SESSION['paymentMethod'] = $paymentMethod;
    $_SESSION['requestNote'] = $requestNote;
    $_SESSION['address'] = $address;
}

$host = "localhost";
$user = "user"; // 사용자 이름
$password = "12345"; // 비밀번호
$database = "sample"; // 데이터베이스 이름

// MySQL 연결 설정
$conn = new mysqli($host, $user, $password, $database);

// 연결 확인
if ($conn->connect_error) {
    die("MySQL 연결 실패: " . $conn->connect_error);
}

// 선택한 메뉴의 정보를 가져오기 위한 쿼리
$selectedMenuInfo = [];

foreach ($_SESSION['selectedMenu'] as $menuID => $quantity) {
    $menuInfoQuery = "SELECT * FROM 메뉴 WHERE 메뉴ID = '$menuID'";
    $menuInfoResult = $conn->query($menuInfoQuery);

    if ($menuInfoResult->num_rows > 0) {
        $menuInfo = $menuInfoResult->fetch_assoc();
        $menuInfo['quantity'] = $quantity;
        $selectedMenuInfo[] = $menuInfo;
    }
}

// 총 금액 계산
$totalAmount = 0;

foreach ($selectedMenuInfo as $item) {
    $price = isset($item['가격']) ? floatval($item['가격']) : 0;
    $quantity = isset($item['quantity']) ? intval($item['quantity']) : 0;

    // Ensure $price and $quantity are numeric before performing multiplication
    if (is_numeric($price) && is_numeric($quantity)) {
        $totalAmount += $price * $quantity;
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>주문 확인</title>
    <style>
         body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h1, h2 {
            text-align: center;
        }

        ul {
            list-style: none;
            padding: 0;
            text-align: left;
        }

        li {
            margin-bottom: 10px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        select, textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        /* Adjust the size of the form */
        form {
            max-width: 400px;
            width: 100%;
        }

        input[type="text"]#address {
        width: 300px; /* 또는 필요에 따라 조절한 크기로 변경하세요. */
        padding: 8px;
        margin-bottom: 10px;
        box-sizing: border-box;
    }
    </style>
</head>
<body>

    <h1>주문 확인</h1>

    <?php if (!empty($selectedMenuInfo)): ?>
        <h2>주문 내역</h2>
        <ul>
            <?php foreach ($selectedMenuInfo as $item): ?>
                <li><?= $item['메뉴이름'] ?> - 수량: <?= $item['quantity'] ?></li>
            <?php endforeach; ?>
        </ul>

        <p><strong>총 금액: <?= $totalAmount ?>,000원</strong></p>

        <!-- 결제 수단, 요청사항, 주소 입력 폼 -->
        <form action="process_order.php" method="post">
            <label for="payment_method">결제 수단 선택:</label>
            <select name="payment_method" id="payment_method">
                <option value="card">카드 결제</option>
                <option value="cash">현금 결제</option>
            </select>

            <br>

            <label for="request_note">요청사항:</label>
            <textarea name="request_note" id="request_note" rows="4" cols="50"></textarea>

            <br>

            <label for="address">배송 주소:</label>
            <input type="text" name="address" id="address" placeholder="배송 주소를 입력하세요" required>

            <br>

           

            <input type="submit" value="주문 완료">
        </form>

    <?php else: ?>
        <p>주문 내역이 없습니다.</p>
    <?php endif; ?>

</body>
</html>