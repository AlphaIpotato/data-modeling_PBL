<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 세션 변수 가져오기
    $paymentMethod = isset($_POST['payment_method']) ? htmlspecialchars($_POST['payment_method']) : '';
    $requestNote = isset($_POST['request_note']) ? htmlspecialchars($_POST['request_note']) : '';
    $address = isset($_POST['address']) ? htmlspecialchars($_POST['address']) : '';
    $selectedMenu = isset($_SESSION['selectedMenu']) ? $_SESSION['selectedMenu'] : [];

    // MySQL 연결 정보
    $host = "localhost";
    $user = "user"; // 사용자 이름
    $password = "12345"; // 비밀번호
    $database = "sample"; // 데이터베이스 이름

    $conn = new mysqli($host, $user, $password, $database);

    // 연결 확인
    if ($conn->connect_error) {
        die("MySQL 연결 실패: " . $conn->connect_error);
    }
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>주문 완료</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
            text-align: center;
        }

        h1, p {
            margin-bottom: 10px;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            margin-bottom: 5px;
        }

        a {
            display: block;
            margin-top: 20px;
            text-decoration: none;
            color: #4CAF50;
            font-weight: bold;
        }

        a:hover {
            color: #45a049;
        }
    </style>
</head>
<body>

    <h1>주문이 완료되었습니다!</h1>
    <p>결제 수단: <?= $paymentMethod ?></p>
    <p>요청 사항: <?= $requestNote ?></p>

    <!-- 주소 정보를 표시합니다. -->
    <p>배송 주소: <?= $address ?></p>

    <ul>
    <?php
    foreach ($selectedMenu as $menuID => $quantity) {
        // 메뉴 정보 데이터베이스에서 가져오기
        $menuInfoQuery = "SELECT * FROM 메뉴 WHERE 메뉴ID = '$menuID'";
        $menuInfoResult = $conn->query($menuInfoQuery);

        if ($menuInfoResult->num_rows > 0) {
            $menuInfo = $menuInfoResult->fetch_assoc();
            echo "<li>{$menuInfo['메뉴이름']} - 수량: $quantity</li>";
        }
    }

    // 총 금액 계산 및 표시
    $totalAmount = 0;
    foreach ($selectedMenu as $menuID => $quantity) {
        $menuInfoQuery = "SELECT * FROM 메뉴 WHERE 메뉴ID = '$menuID'";
        $menuInfoResult = $conn->query($menuInfoQuery);

        if ($menuInfoResult->num_rows > 0) {
            $menuInfo = $menuInfoResult->fetch_assoc();
            $price = floatval($menuInfo['가격']);
            $totalAmount += $price * $quantity;
        }
    }

    echo "</ul>";

    // 총 금액 표시
    echo "<p><strong>총 금액: $totalAmount,000원</strong></p>";

    // 메인 페이지 링크
    echo "<a href='main_page.php'>메인페이지로 이동</a>";

    // 세션 변수 삭제
    unset($_SESSION['selectedMenu']);
    unset($_SESSION['paymentMethod']);
    unset($_SESSION['requestNote']);
    unset($_SESSION['address']);
} else {
    // 양식 제출하지 않고 직접 액세스한 경우 오류 페이지로 리디렉션
    header("Location: error.php");
    exit();
}
?>

</body>
</html>