<?php
session_start();

// 사용자 인증을 확인하고 로그인 페이지로 리디렉션
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
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

$user_id = (int)$_SESSION['user_id'];
$query = "SELECT * FROM 주문이력 WHERE 회원ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// 주문 이력을 저장할 배열 초기화
$orderHistory = [];

if ($result->num_rows > 0) {
    // 결과에서 각 행을 가져와 배열에 추가
    while ($row = $result->fetch_assoc()) {
        $orderHistory[] = $row;
    }
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>주문 이력</title>
    <style>
         body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f2f2f2;
        }

        header {
            background-color: #333;
            color: white;
            padding: 1em;
            position: relative;
            text-align: center;
        }

        header a {
            color: white;
            text-decoration: none;
        }

        #order-history-container {
            max-width: 800px;
            margin: 20px auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #333;
            color: white;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        p {
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- 헤더 섹션 -->
    <header>
        <h1>주문 이력</h1>
        <!-- 홈 링크 추가 -->
        <a href="main_page.php">홈</a>
    </header>

    <!-- 주문 이력 섹션 -->
    <div id="order-history-container">
        <?php if (empty($orderHistory)): ?>
            <p>주문 이력이 없습니다.</p>
        <?php else: ?>
            <table border="1">
                <thead>
                    <tr>
                        <th>주문ID</th>
                        <th>가게ID</th>
                        <th>주소</th>
                        <th>결제수단</th>
                        <th>주문메뉴</th>
                        <th>쿠폰사용여부</th>
                        <th>요청사항</th>
                        <th>총액</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orderHistory as $order): ?>
                        <tr>
                            <td><?= $order['주문ID'] ?></td>
                            <td><?= $order['가게ID'] ?></td>
                            <td><?= $order['주소'] ?></td>
                            <td><?= $order['결제수단'] ?></td>
                            <td><?= $order['주문메뉴'] ?></td>
                            <td><?= $order['쿠폰사용여부'] ?></td>
                            <td><?= $order['요청사항'] ?></td>
                            <td><?= $order['총액'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</body>
</html>