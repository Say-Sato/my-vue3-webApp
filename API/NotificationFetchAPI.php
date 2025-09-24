<?php
header('Content-Type: application/json; charset=utf-8');
include('./BlogPDO.php'); // PDO接続

try {
    $dbh = new PDO($dsn, $user, $password);

    //$lastChecked = '1970-01-01 00:00:00';

    // リクエストURIからIDを取得
    $requestUri = $_SERVER['REQUEST_URI'];
    $userId = null;

    // URIが'/api/NotificationFetchAPI.php/id'形式の場合
    if (preg_match('/php\/(\d+)$/', $requestUri, $matches)) {
        $userId = urldecode($matches[1]);
    }

    /*if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
        echo json_encode(["error" => "必要なパラメータがセットされていません。"], JSON_UNESCAPED_UNICODE);
        die();
    }

    $userId = $_GET['user_id'];*/
    $lastChecked = date('Y-m-d H:i:s', strtotime($_GET['last_checked'] ?? '1970-01-01 00:00:00'));

    // ユーザー設定取得
    $stmtSettings = $dbh->prepare("SELECT * FROM notification_settings WHERE user_id = :user_id");
    $stmtSettings->execute([':user_id' => $userId]);
    $flags = $stmtSettings->fetch(PDO::FETCH_ASSOC);

    if (!$flags) {
        echo json_encode(["error" => "ユーザー設定が見つかりません。"], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // SQL文を先に定義
    $sql_comments = "
        SELECT 'comments' AS type, c.id, c.created_at, p.name AS actor, i.title AS target
        FROM comments c
        JOIN illust i ON c.post_id = i.id
        JOIN profile p ON c.user_id = p.id
        WHERE i.p_id = :user_id AND c.created_at > :last_checked
    ";

    $sql_follows = "
        SELECT 'follows' AS type, 
            f.id, 
            f.created_at, 
            p.name AS actor, 
            NULL AS target
        FROM follows f
        JOIN profile p 
        ON f.follower_id = p.id
        LEFT JOIN user_follow_notification_blocks ufb
        ON ufb.target_user_id = f.followed_id
        AND ufb.user_id = f.follower_id
        WHERE f.followed_id = :user_id
        AND f.created_at > :last_checked
        AND ufb.user_id IS NULL;
    ";

    $sql_favorites = "
        SELECT 'favorite' AS type, CONCAT('fav_', fav.i_id, '_', fav.u_id) AS id,
               fav.liked_times AS created_at, p.name AS actor, i.title AS target
        FROM favorite fav
        JOIN illust i ON fav.i_id = i.id
        JOIN profile p ON fav.u_id = p.id
        WHERE i.p_id = :user_id AND fav.liked_times > :last_checked
    ";

    $sql_illusts = "
        SELECT 'illust' AS type, i.id, i.created_at, p.name AS actor, i.title AS target
        FROM illust i
        JOIN follows f ON i.p_id = f.followed_id
        JOIN profile p ON i.p_id = p.id
        WHERE f.follower_id = :user_id AND i.created_at > :last_checked
    ";

    // フラグに応じてSQLを追加
    $sqlParts = [];
    if (!empty($flags['notify_comment']) && $flags['notify_comment'] == 1)
        $sqlParts[] = $sql_comments;
    if (!empty($flags['notify_follow']) && $flags['notify_follow'] == 1)    $sqlParts[] = $sql_follows;
    if (!empty($flags['notify_favorite']) && $flags['notify_favorite'] == 1)  $sqlParts[] = $sql_favorites;
    if (!empty($flags['notify_illust']) && $flags['notify_illust'] == 1)    $sqlParts[] = $sql_illusts;

    // SQLを実行
    if (!empty($sqlParts)) {
        $sql = implode(" UNION ALL ", $sqlParts) . " ORDER BY created_at DESC";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':last_checked' => $lastChecked
        ]);
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $notifications = [];
    }

    echo json_encode([
        'success' => true,
        'last_checked' => $lastChecked,
        'notifications' => $notifications
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    // エラーメッセージもJSON形式で返す
    header('Content-Type: application/json', true, 500);
    echo json_encode(["error" => "データベースの接続に失敗しました: " . $e->getMessage(), JSON_UNESCAPED_UNICODE], JSON_UNESCAPED_UNICODE);
    die();
} finally {
    // DB接続を閉じる
    $dbh = null; // PDOオブジェクトをnullにすることで接続を閉じる
}
