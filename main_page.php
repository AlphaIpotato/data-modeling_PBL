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

// 가게 목록을 가져오는 쿼리
$query = "SELECT * FROM 가게";
$result = $conn->query($query);

// 가게 목록을 저장할 배열 초기화
$restaurants = [];

if ($result->num_rows > 0) {
    // 결과에서 각 행을 가져와 배열에 추가
    while ($row = $result->fetch_assoc()) {
        $restaurants[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>음식 배달 앱</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        header {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 1em;
            width: 100%;
            box-sizing: border-box;
        }

        #user-info {
            margin: 20px;
            text-align: left;
            position: absolute;
            top: 1em;
            left: 1em;
            color: white;
        }

        #search-container,
        #category-filter {
            text-align: center;
            margin: 2em;
        }

        #restaurants-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            margin: 2em;
        }

        .restaurant-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 1em;
            margin: 1em;
            max-width: 300px;
            background-color: #fff;
            flex: 1 1 calc(25% - 2em); /* 4개의 열로 균등하게 배치 */
        }

        button {
            padding: 0.5em 1em;
            font-size: 1em;
            cursor: pointer;
        }

        #sort-filter {
            text-align: center;
            margin: 2em;
        }
    </style>
</head>
<body>

    <!-- 헤더 섹션 -->
    <header>
        <h1>음식 배달 앱</h1>
        <!-- 로그아웃 링크 추가 -->
        <a href="login.php?logout=true" style="position: absolute; top: 1em; right: 1em; color: white; text-decoration: none;">로그아웃</a>
        <a href="order_history.php" style="position: absolute; top: 1em; right: 8em; color: white; text-decoration: none;">주문내역</a>
    </header>

    <!-- 사용자 정보 표시 -->
    <div id="user-info">
        <?php
        if (isset($_SESSION['user_id'])) {
            echo '<p>환영합니다, ' . $_SESSION['user_name'] . '님!</p>';
        }
        ?>
    </div>

    <!-- 검색 섹션 -->
    <div id="search-container">
        <label for="search">음식 검색:</label>
        <input type="text" id="search" placeholder="음식을 검색하세요">
        <button onclick="searchRestaurants()">검색</button>
    </div>

    <!-- 카테고리 필터 섹션 -->
    <div id="category-filter">
        <label for="category">카테고리 선택:</label>
        <select id="category" onchange="filterByCategory()">
            <option value="all">전체</option>
            <option value="한식">한식</option>
            <option value="치킨">치킨</option>
            <option value="고기">고기</option>
            <option value="디저트">디저트</option>
            <option value="분식">분식</option>
            <option value="일식">일식</option>
            <option value="중식">중식</option>
            <option value="주점">주점</option>
            <option value="양식">양식</option>
        </select>
    </div>

    <!-- 정렬 필터 섹션 -->
<div id="sort-filter">
    <label for="sort">정렬 방법:</label>
    <select id="sort" onchange="sortRestaurants()">
        <option value="default">기본 정렬</option>
        <option value="rating">평점 높은 순</option>
        <option value="review">리뷰 수 높은 순</option>
    </select>
</div>

    <!-- 가게 목록 섹션 -->
    <div id="restaurants-container">
    <?php foreach ($restaurants as $restaurant): ?>
        <div class="restaurant-card" data-category="<?= $restaurant['카테고리'] ?>" data-name="<?= $restaurant['가게명'] ?>">
            <h3><?= $restaurant['가게명'] ?></h3>
            <p>연락처: <?= $restaurant['가게연락처'] ?></p>
            <p>최소 주문 금액: $<?= $restaurant['최소주문금액'] ?></p>
            <p>배달 팁: $<?= $restaurant['배달팁'] ?></p>
            <p>운영시간: <?= $restaurant['운영시간'] ?></p>
            <p>배달 가능 지역: <?= $restaurant['배달가능지역'] ?></p>
            <p>평점: <?= $restaurant['평점'] ?> (리뷰수: <?= $restaurant['리뷰수'] ?>)</p>
            <!-- 메뉴 보기 링크 추가 -->
            <a href="menu.php?restaurantID=<?= $restaurant['가게ID'] ?>">메뉴 보기</a>
        </div>
    <?php endforeach; ?>
    </div>

    <script>
    let originalRestaurants = <?= json_encode($restaurants) ?>;
    let currentRestaurants = originalRestaurants.slice(); // 초기화된 배열 유지

    function filterByCategory() {
        const selectedCategory = document.getElementById("category").value;
        const restaurantCards = document.querySelectorAll('.restaurant-card');

        restaurantCards.forEach(card => {
            const category = card.getAttribute('data-category');

            if (selectedCategory === 'all' || category === selectedCategory) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function sortRestaurants() {
        const selectedSort = document.getElementById("sort").value;
        const restaurantCards = document.querySelectorAll('.restaurant-card');

        const sortedRestaurants = currentRestaurants.slice(); // 현재 배열을 복사하여 사용

        if (selectedSort === 'rating') {
            // 평점순 정렬 (내림차순)
            sortedRestaurants.sort((a, b) => b.평점 - a.평점);
        } else if (selectedSort === 'review') {
            // 리뷰 수순 정렬 (내림차순)
            sortedRestaurants.sort((a, b) => b.리뷰수 - a.리뷰수);
        }

        // 정렬된 배열을 기반으로 화면에 표시 순서 업데이트
        restaurantCards.forEach((card, index) => {
            const restaurant = sortedRestaurants[index];
            const category = card.getAttribute('data-category');

            if (category === restaurant['카테고리']) {
                card.style.display = 'block';
                // 필요한 경우 다른 카드 세부 정보 업데이트
                card.querySelector('h3').innerText = restaurant['가게명'];
                // 기타 세부 정보를 필요에 따라 업데이트
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>

</body>
</html>