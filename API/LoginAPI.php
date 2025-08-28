<?php
//ログイン画面
// CORSヘッダーの設定
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// OPTIONSリクエストの処理（プリフライトリクエスト対応）
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    // プリフライトリクエストに対して200のステータスを返す
    http_response_code(200);
    exit;
}

//DB接続
include('./BlogPDO.php');

$data = []; // 配列の初期化

// DBへ接続
try {
    $dbh = new PDO($dsn, $user, $password);

    // JSON形式のリクエストデータを取得
    $inputData = file_get_contents('php://input');
    error_log('Received raw input data: ' . $inputData); // デバッグ用のログ出力
    $json = json_decode($inputData, true);

    if ($json === null) {
        // JSONデコードに失敗した場合の処理
        echo json_encode(["error" => "Invalid JSON data."]);
        die();
    }

    // POSTリクエストかどうか確認
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // JSONデータから値を取得
        $login_id = isset($json['loginid']) ? $json['loginid'] : '';
        $password = isset($json['password']) ? $json['password'] : '';

        // クエリ
        $query = "SELECT COUNT(*) FROM profile WHERE login_id = :login_id AND convert(AES_DECRYPT(UNHEX(password), 'are0421') USING utf8) = :password;";
        $stmt = $dbh->prepare($query);

        // パラメータをバインド
        $stmt->bindParam(':login_id', $login_id);
        $stmt->bindParam(':password', $password);

        // クエリを実行
        $stmt->execute();

        // カウントを取得
        $count = $stmt->fetchColumn();

        if ($count == 1) {
            // IDとパスワードが一致した場合の処理
            $query = "SELECT * FROM profile WHERE login_id = :login_id AND convert(AES_DECRYPT(UNHEX(password), 'are0421') USING utf8) = :password;";
            $stmt = $dbh->prepare($query);
            $stmt->bindParam(':login_id', $login_id);
            $stmt->bindParam(':password', $password);

            // クエリを実行
            $stmt->execute();

            // ユーザー情報を取得
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // ユーザー情報をオブジェクトとして配列に格納
                $data = [
                    "id" => $user['id'],
                    "login_id" => $user['login_id'],
                    "name" => $user['name'],
                    "body" => $user['body'],
                    "profile_photo" => $user['profile_photo'],
                    "admin" => $user['admin']
                ];

                // JSON形式で返す
                header('Content-Type: application/json');
                echo json_encode($data);
            } else {
                // IDが見つからない場合の処理
                echo json_encode(["error" => "該当するIDが存在しません。"], JSON_UNESCAPED_UNICODE);
            }
        } else {
            // IDまたはパスワードが一致しない場合
            echo json_encode(["error" => "ID:{$login_id}またはパスワード:{$password}が正しくありません。"], JSON_UNESCAPED_UNICODE);
        }
    } else {
        // POSTリクエストでない場合の処理
        echo json_encode(["error" => "POSTリクエストを送信してください。"], JSON_UNESCAPED_UNICODE);
    }
} catch (PDOException $e) {
    // データベース接続エラーの処理
    header('Content-Type: application/json', true, 500);
    echo json_encode(["error" => "データベースの接続に失敗しました: " . $e->getMessage()]);
    die();
} finally {
    // DB接続を閉じる
    $dbh = null;
}
