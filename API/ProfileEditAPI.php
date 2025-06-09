<?php
header('Content-Type: application/json; charset=utf-8');
include('./BlogPDO.php');

// リクエストURIからIDを取得
$requestUri = $_SERVER['REQUEST_URI'];
$id = null;

if (preg_match('/\/(\d+)$/', $requestUri, $matches)) {
    $id = intval($matches[1]);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $imageUrl = null;

    if (isset($_FILES['profilePhoto']) && $_FILES['profilePhoto']['error'] === UPLOAD_ERR_OK) {
        $uploadedFile = $_FILES['profilePhoto'];

        // 保存先の設定
        $domainName = 'yellowokapi2.sakura.ne.jp';
        $folderName = 'Blog/index/profile_photo';
        $uniqueFileName = uniqid('image_', true) . '.' . pathinfo($uploadedFile['name'], PATHINFO_EXTENSION);
        $savePath = $_SERVER['DOCUMENT_ROOT'] . '/' . $folderName . '/' . $uniqueFileName;

        // フォルダが存在しない場合は作成（必要なら）
        if (!is_dir(dirname($savePath))) {
            mkdir(dirname($savePath), 0755, true);
        }

        // ファイルを一時保存場所から目的の場所へ移動
        if (move_uploaded_file($uploadedFile['tmp_name'], $savePath)) {
            $imageUrl = "http://$domainName/$folderName/$uniqueFileName";
        } else {
            echo json_encode(["error" => "画像ファイルの保存に失敗しました。"], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    // 入力値の取得（nullチェックのみ行う）
    $inputData = [
        'image'      => isset($imageUrl) ? $imageUrl : null,
        'name'          => array_key_exists('userName', $_POST) ? $_POST['userName'] : null,
        'password'      => array_key_exists('password', $_POST) ? $_POST['password'] : null,
        'certificate18' => array_key_exists('certificate18', $_POST) ? $_POST['certificate18'] : null,
        'birthDate'     => array_key_exists('birthDate', $_POST) ? $_POST['birthDate'] : null,
        'body'          => array_key_exists('body', $_POST) ? $_POST['body'] : null,
    ];

    // ★ ここでJSON形式で入力内容を出力するよ
    echo json_encode([
        "debug" => "受信した入力内容のデバッグ表示",
        "input" => $inputData
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;

    /*
    // 以下の本処理はコメントアウト中
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
        $stmt = $dbh->prepare($sql);
        $stmt->execute($params);

        echo json_encode(["message" => "プロフィールを更新しました。"], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(["error" => "更新対象がありません。"], JSON_UNESCAPED_UNICODE);
    }
    */
} else {
    echo json_encode(["error" => "POSTリクエストを送信してください。"], JSON_UNESCAPED_UNICODE);
}
?>
