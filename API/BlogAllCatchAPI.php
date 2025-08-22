<?php
include('./BlogPDO.php');

// リクエストURIからIDを取得
$requestUri = $_SERVER['REQUEST_URI'];
$id = null;

if (preg_match('/\/(\d+)$/', $requestUri, $matches)) {
    $id = intval($matches[1]);
}

$data = []; // 初期化

if ($id !== null) {
    try {
        $dbh = new PDO($dsn, $user, $password);

        // 該当IDとその前後1件を取得
        $query = "
            (SELECT 
                id, title, 'prev' AS role,
                NULL AS p_id, NULL AS body, NULL AS url, NULL AS created,
                NULL AS p_name, NULL AS p_photo, NULL AS tag_ids,
                NULL AS R18, NULL AS public, NULL AS s_url
            FROM illust
            WHERE id < :id
            ORDER BY id DESC
            LIMIT 1)
            UNION ALL
            (SELECT 
                illust.id, illust.title, 'current' AS role,
                illust.p_id, illust.body, illust.url, illust.created,
                profile.name AS p_name, profile.profile_photo AS p_photo,
                GROUP_CONCAT(illust_tags.t_id) AS tag_ids,
                illust.R18, illust.public, illust.s_url
            FROM illust
            JOIN profile ON illust.p_id = profile.id
            LEFT JOIN illust_tags ON illust.id = illust_tags.i_id
            WHERE illust.id = :id
            GROUP BY illust.id)
            UNION ALL
            (SELECT 
                id, title, 'next' AS role,
                NULL AS p_id, NULL AS body, NULL AS url, NULL AS created,
                NULL AS p_name, NULL AS p_photo, NULL AS tag_ids,
                NULL AS R18, NULL AS public, NULL AS s_url
            FROM illust
            WHERE id > :id
            ORDER BY id ASC
            LIMIT 1)
        ";

        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $prev = null;
        $current = null;
        $next = null;

        foreach ($rows as $row) {
            switch ($row['role']) {
                case 'prev':    $prev = $row; break;
                case 'current': $current = $row; break;
                case 'next':    $next = $row; break;
            }
        }

        if ($current) {
            $data = [
                "illust_id"         => $current['id'],
                "profile_id"       => $current['p_id'],
                "illust_title"      => $current['title'],
                "tags"       => isset($current['tag_ids']) ? array_map('intval', explode(',', $current['tag_ids'])) : [],
                "thumbnail_url"        => $current['url'],
                "illust_body"       => $current['body'],
                "R18"        => $current['R18'],
                "public"     => $current['public'],
                "profile_name"     => $current['p_name'],
                "profile_photo"    => $current['p_photo'],
                "prev_id"    => $prev['id'] ?? null,
                "next_id"    => $next['id'] ?? null,
                "images"     => []
            ];

            // 対応画像の取得
            $imgQuery = "
                SELECT url AS image_url, id AS image_id, sort_order
                FROM images
                WHERE post_id = :id
                ORDER BY sort_order ASC";
            $imgStmt = $dbh->prepare($imgQuery);
            $imgStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $imgStmt->execute();
            $images = $imgStmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($images as $img) {
                $data['images'][] = [
                    'image_id'   => (int)$img['image_id'],
                    'image_url'  => $img['image_url'],
                    'sort_order' => (int)$img['sort_order']
                ];
            }

            // JSON返却
            header('Content-Type: application/json');
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "ID:{$id}のデータが見つかりません。"], JSON_UNESCAPED_UNICODE);
        }

    } catch (PDOException $e) {
        header('Content-Type: application/json', true, 500);
        echo json_encode(["error" => "データベース接続失敗: " . $e->getMessage()]);
    } finally {
        $dbh = null;
    }
} else {
    // IDが指定されていない場合公開に設定されているすべてのデータを返す
    try {
        $dbh = new PDO($dsn, $user, $password);
        
        // illust + profile + tags を一括取得
        $query = "SELECT illust.*, profile.name AS p_name,
                        profile.profile_photo AS p_photo,
                        GROUP_CONCAT(illust_tags.t_id) AS tag_ids
                FROM illust
                JOIN profile ON illust.p_id = profile.id
                LEFT JOIN illust_tags ON illust.id = illust_tags.i_id
                WHERE public = 0
                GROUP BY illust.id
                ORDER BY created DESC";
        $stmt = $dbh->prepare($query);
        $stmt->execute();

        $data = [];
        
        // メインループで各イラストごとの配列を作成
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $item = [
                "illust_id" => (int)$row['id'],
                "profile_id" => (int)$row['p_id'],
                "illust_title" => $row['title'],
                "tags" => $row['tag_ids'] ? array_map('intval', explode(',', $row['tag_ids'])) : [],
                "thumbnail_url" => $row['url'],
                "illust_body" => $row['body'],
                "R18" => $row['R18'],
                "public" => $row['public'],
                "profile_name" => $row['p_name'],
                "profile_photo" => $row['p_photo'],
                "images" => [], // ← ここに画像リスト入れる
            ];

            // 各イラストに紐づく images を取得
            $imgQuery = "SELECT post_id,
                                url AS image_url,
                                id AS image_id,
                                sort_order
                        FROM images
                        WHERE post_id = :post_id
                        ORDER BY sort_order ASC";
            $imgStmt = $dbh->prepare($imgQuery);
            $imgStmt->bindValue(':post_id', $item['id'], PDO::PARAM_INT);
            $imgStmt->execute();

            $images = $imgStmt->fetchAll(PDO::FETCH_ASSOC);
            // :contentReference[oaicite:1]{index=1}

            foreach ($images as $img) {
                $item['images'][] = [
                    'image_id' => (int)$img['image_id'],
                    'image_url' => $img['image_url'],
                    'sort_order' => (int)$img['sort_order'],
                ];
            }

            $data[] = $item;
        }

        // JSON形式で返す
        header('Content-Type: application/json');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    } catch(PDOException $e) {
        // エラーメッセージもJSON形式で返す
        header('Content-Type: application/json', true, 500);
        echo json_encode(["error" => "データベースの接続に失敗しました: " . $e->getMessage()]);
        die();
    } finally {
        // DB接続を閉じる
        $dbh = null; // PDOオブジェクトをnullにすることで接続を閉じる
    }
}
?>
