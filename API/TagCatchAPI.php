<?php
//TagCatchiAPI.php
header("Access-Control-Allow-Origin: *"); // 全てのオリジンを許可（開発用）
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
include('./BlogPDO.php');

header('Content-Type: application/json');

// クエリパラメータから tag を取得
$tag = isset($_GET['tag']) ? urldecode($_GET['tag']) : null;

// カンマ区切りで複数タグを配列化
$tagList = array_map('urldecode', array_map('trim', explode(',', $tag)));

try {
    // データベース接続
    $dbh = new PDO($dsn, $user, $password);

    // タグの検証とプレースホルダの生成の修正
    if (count($tagList) > 0) {
        $tagConditions = array_fill(0, count($tagList), "(id = ?)");
        $tagQuery = "SELECT id FROM tags WHERE " . implode(" OR ", $tagConditions);
        $tagStmt = $dbh->prepare($tagQuery);
        $tagStmt->execute($tagList);

        $tagIds = [];
        while ($row = $tagStmt->fetch(PDO::FETCH_ASSOC)) {
            $tagIds[] = $row['id'];
        }

        if (empty($tagIds)) {
            echo json_encode(["error" => "該当するタグが見つかりません。"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            die();
        }

        // $sortTagIdsを正しく初期化
        $sortTagIds = [];

        // 検索クエリの処理
        $sortPlaceholders = implode(',', array_fill(0, count($tagList), '?'));
        $sortQuery = "SELECT i_id, GROUP_CONCAT(t_id ORDER BY t_id) AS tag_ids
        FROM illust_tags
        WHERE illust_tags.t_id IN ($sortPlaceholders)
        GROUP BY i_id";
        $sortStmt = $dbh->prepare($sortQuery);
        $sortStmt->execute($tagList);

        $searchIdList = [];
        while ($sortResult = $sortStmt->fetch(PDO::FETCH_ASSOC)) {
            // 正しくタグを分割
            $resultTags = $sortResult['tag_ids'];
            $sortTagIds = $resultTags ? explode(',', $resultTags) : [];

            // 全タグが一致するか確認
            $missingTags = array_diff($tagList, $sortTagIds);
            if (empty($missingTags)) {
                // $searchIdListにi_idを追加
                $searchIdList[] = $sortResult['i_id'];
            }
        }

        // 検索IDが存在する場合の処理
        if (count($searchIdList) > 0) {
            $placeholders = implode(',', array_fill(0, count($searchIdList), '?'));
            $query = "SELECT illust.*, profile.name AS p_name, profile.profile_photo AS p_photo
            FROM illust
            JOIN profile ON illust.p_id = profile.id
            WHERE illust.id IN ($placeholders)
            GROUP BY illust.id
            ORDER BY illust.updated DESC";
            $stmt = $dbh->prepare($query);
            $stmt->execute($searchIdList);

            $illusts = [];

            // イラストのデータを取得
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $illusts[] = [
                    "illust_id" => $row['id'],
                    "profile_id" => $row['p_id'],
                    "illust_title" => $row['title'],
                    "thumbnail_url" => $row['url'],
                    "illust_body" => $row['body'],
                    "R18" => $row['R18'],
                    "public" => $row['public'],
                    "profile_name" => $row['p_name'],
                    "profile_photo" => $row['p_photo'],
                ];
            }

            // タグ名を取得
            $tagSearchQuery = "SELECT tags.name AS name FROM tags WHERE tags.id IN ($sortPlaceholders)";
            $tagSearchStmt = $dbh->prepare($tagSearchQuery);
            $tagSearchStmt->execute($tagList);

            $tagWords = [];
            while ($tagResult = $tagSearchStmt->fetch(PDO::FETCH_ASSOC)) {
                $tagWords[] = $tagResult['name']; // タグ名を追加
            }

            // 重複を除外
            $tagWords = array_unique($tagWords);

            // タグ名をカンマ区切りで結合
            $tagWord = implode(',', $tagWords);

            // イラストデータにタグ情報を追加
            foreach ($illusts as &$illust) {
                $illust['tags'] = $tagWord;
            }

            // 結果を返す
            if (empty($illusts)) {
                echo json_encode(["error" => "イラストが見つかりません。"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode($illusts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }
        } else {
            echo json_encode(["error" => "該当する記事がありません。"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
    } else {
        echo json_encode(["error" => "タグが入力されていません。"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
} catch (PDOException $e) {
    header('Content-Type: application/json', true, 500);
    echo json_encode(["error" => "データベースの接続に失敗しました: " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
    die();
}
