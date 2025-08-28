<?php
include('./BlogPDO.php');

try {
    $dbh = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    if (empty($_GET['ids'])) {
        http_response_code(400);
        echo json_encode(['error' => 'IDが指定されていません'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $idArray = array_filter(
        explode(',', $_GET['ids']),
        fn($id) => preg_match('/^\d+$/', $id)
    );

    if (empty($idArray)) {
        http_response_code(400);
        echo json_encode(['error' => 'IDが不正です'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // プレースホルダ作成
    $placeholders = implode(',', array_fill(0, count($idArray), '?'));

    // ===== 順番保持のため ORDER BY FIELD を追加 =====
    $fieldPlaceholders = implode(',', array_fill(0, count($idArray), '?'));
    $sql = "SELECT 
            illust.id as illust_id,
            illust.p_id as profile_id,
            illust.title as illust_title,
            illust.body as illust_body,
            illust.public as public,
            illust.R18 as R18,
            profile.name AS profile_name,
            profile.profile_photo AS profile_photo,
            images.url AS thumbnail_url
        FROM illust
        JOIN profile ON illust.p_id = profile.id
        JOIN images ON illust.id = images.post_id AND images.sort_order = 0
        WHERE illust.id IN ($placeholders) 
        ORDER BY FIELD(illust.id, $fieldPlaceholders)";


    $stmt = $dbh->prepare($sql);

    // 1回目の?（IN句用）と2回目の?（FIELD用）に同じ配列を渡す
    $stmt->execute([...$idArray, ...$idArray]);

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'データベース接続失敗: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
    exit;
}
