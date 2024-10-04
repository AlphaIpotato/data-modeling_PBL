<?php
session_start();

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

// 로그인 폼이 제출되었을 때의 처리
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 사용자 입력 가져오기
    $loginID = $_POST["loginID"];

    // 입력 유효성 검사 및 필요한 경우 이스케이프
    $loginID = mysqli_real_escape_string($conn, $loginID);

    // 사용자가 존재하는지 확인하는 쿼리
    $loginQuery = "SELECT * FROM 회원 WHERE 회원ID = '$loginID'";
    $loginResult = $conn->query($loginQuery);

    if ($loginResult->num_rows > 0) {
        
        // 로그인 성공 후 세션에 사용자 정보 저장
        $user = $loginResult->fetch_assoc();
        $_SESSION['user_id'] = $user['회원ID'];
        $_SESSION['user_name'] = $user['이름'];

        // 로그인 성공 후 메인 페이지로 리디렉션
        header("Location: main_page.php");
        exit();
    } else {
        $loginError = "유효하지 않은 로그인 자격 증명";
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>음식 배달 앱 - 로그인</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        #login-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }

        h2 {
            color: #333;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
        }

        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 16px;
            box-sizing: border-box;
        }

        button {
            background-color: #4caf50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        p {
            color: red;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div id="login-container">
    <h1>E&S EATS</h1>
        <h2>로그인</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="loginID">회원ID:</label>
            <input type="text" id="loginID" name="loginID" required>
            <button type="submit">로그인</button>
        </form>
        <?php
        // 로그인 오류가 있을 경우 오류 메시지를 표시합니다
        if (isset($loginError)) {
            echo '<p>' . $loginError . '</p>';
        }
        ?>

    <p>아직 계정이 없으신가요? <a href="register.php">회원가입</a></p>
    </div>
</body>
</html>