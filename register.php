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

?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>회원가입</title>
    <style>
        body {
            font-family: '맑은 고딕', 'Malgun Gothic', 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        #register-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }

        h2 {
            color: #333;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 15px;
            box-sizing: border-box;
        }

        input::placeholder {
            color: #999;
            font-style: italic;
        }

        button {
            background-color: #4caf50;
            color: #fff;
            padding: 10px;
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
    </style>
</head>
<body>
    <div id="register-container">
        <h2>회원가입</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="registerID">회원ID:</label>
            <input type="text" id="registerID" name="registerID" placeholder="영어와 숫자를 조합하세요" required>
            
            <label for="registerName">이름:</label>
            <input type="text" id="registerName" name="registerName" required>
            
            <label for="registerPhone">휴대폰번호:</label>
            <input type="text" id="registerPhone" name="registerPhone" required>

            <!-- 다른 필요한 회원가입 필드를 추가하세요. -->

            <button type="submit">회원가입</button>
        </form>
        <?php
        // 회원가입 폼 제출 처리
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $registerID = $_POST["registerID"];
            $registerName = $_POST["registerName"];
            $registerPhone = $_POST["registerPhone"];

            // 필요한 경우 입력 유효성 검사 및 이스케이프
            $registerID = mysqli_real_escape_string($conn, $registerID);
            $registerName = mysqli_real_escape_string($conn, $registerName);
            $registerPhone = mysqli_real_escape_string($conn, $registerPhone);

            // 중복 회원ID 확인
            $checkUserQuery = "SELECT * FROM 회원 WHERE 회원ID = '$registerID'";
            $checkUserResult = $conn->query($checkUserQuery);

            if ($checkUserResult->num_rows > 0) {
                echo '<p>이미 존재하는 회원ID입니다.</p>';
            } else {
                // 회원 추가
                $insertUserQuery = "INSERT INTO 회원 (회원ID, 이름, 휴대폰번호) VALUES ('$registerID', '$registerName', '$registerPhone')";
                if ($conn->query($insertUserQuery) === TRUE) {
                    echo '<p>회원가입 성공!</p>';
                    // 여기에서 로그인 페이지로 리디렉션하거나 다른 작업을 수행할 수 있습니다.
                } else {
                    echo '<p>회원가입 오류: ' . $conn->error . '</p>';
                }
            }
        }
        ?>
    </div>
</body>
</html>