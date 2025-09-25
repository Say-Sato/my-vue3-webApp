<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=utf-8");
include('./BlogPDO.php');

// リクエストURIからIDを取得
$requestUri = $_SERVER['REQUEST_URI'];
$id = null;

if (preg_match('/\/(\d+)$/', $requestUri, $matches)) {
    $id = intval($matches[1]);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $imageUrl = null;
    $insertImage = null; // 最初に初期化しておく！

    if (isset($_FILES['profilePhoto']) && $_FILES['profilePhoto']['error'] === UPLOAD_ERR_OK) {
        $uploadedFile = $_FILES['profilePhoto'];

        // 保存先の設定
        $domainName = 'yellowokapi2.sakura.ne.jp';
        $folderName = 'Blog/index';
        $insertName = 'profile_photo';
        $uniqueFileName = uniqid('image_', true) . '.' . pathinfo($uploadedFile['name'], PATHINFO_EXTENSION);
        $savePath = $_SERVER['DOCUMENT_ROOT'] . '/' . $folderName . '/' . $insertName . '/' . $uniqueFileName;

        // フォルダが存在しない場合は作成（必要なら）
        if (!is_dir(dirname($savePath))) {
            mkdir(dirname($savePath), 0755, true);
        }

        // ファイルを一時保存場所から目的の場所へ移動
        if (move_uploaded_file($uploadedFile['tmp_name'], $savePath)) {
            $imageUrl = "http://$domainName/$folderName/$insertName/$uniqueFileName";
            $insertImage = "/$insertName/$uniqueFileName";
        } else {
            echo json_encode(["error" => "画像ファイルの保存に失敗しました。"], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    // 入力値の取得（nullチェックのみ行う）
    $inputData = [
        'profile_photo'      => isset($insertImage) ? $insertImage : null,
        'name'          => array_key_exists('userName', $_POST) ? $_POST['userName'] : null,
        'password'      => array_key_exists('password', $_POST) ? $_POST['password'] : null,
        'certificate18' => array_key_exists('certificate18', $_POST) ? $_POST['certificate18'] : null,
        'birthDate'     => array_key_exists('birthDate', $_POST) ? $_POST['birthDate'] : null,
        'body'          => array_key_exists('body', $_POST) ? $_POST['body'] : null,
    ];

    // 👇 passwordがあるなら暗号化！
    if (!empty($inputData['password'])) {
        $encrypted = openssl_encrypt(
            $inputData['password'],
            'AES-128-ECB', // 暗号化方式（暗号・復号で一致してる必要あり）
            $secretKey,
            OPENSSL_RAW_DATA
        );
        // バイナリ→16進文字列へ
        $inputData['password'] = strtoupper(bin2hex($encrypted));
    }

    $dbh = new PDO($dsn, $user, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $updates = [];
    $params = [];

    foreach ($inputData as $column => $value) {
        if (!is_null($value)) {
            $updates[] = "$column = :$column";
            $params[$column] = $value;
        }
    }

    if (!empty($updates) && $id !== null) {
        $params['id'] = $id;
        $sql = "UPDATE profile SET " . implode(', ', $updates) . " WHERE id = :id";

        // 💡SQLデバッグ用（バインドされた値をSQLに埋め込んで表示）
        $interpolatedSql = $sql;
        foreach ($params as $key => $val) {
            $escapedVal = is_null($val) ? 'NULL' : $dbh->quote($val);
            $interpolatedSql = str_replace(":$key", $escapedVal, $interpolatedSql);
        }

        // 🔍★ここで SQL とパラメータを出力（デバッグ用）
        /*echo json_encode([
            "debug" => "SQLクエリとバインド変数のデバッグ出力",
            "sql" => $sql,
            "bound_sql" => $interpolatedSql,
            "params" => $params,
            "input" => $inputData,
            "image" => $imageUrl,
            "insertImage" => $insertImage
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;*/

        // 実行処理
        $stmt = $dbh->prepare($sql);
        $stmt->execute($params);

        echo json_encode(["message" => "プロフィールを更新しました。"], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    } else {
        echo json_encode(["error" => "更新対象がありません。"], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
