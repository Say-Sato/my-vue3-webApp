<?php
// プリフライト対応
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    exit(0);
}

// ヘッダ設定（CORSとJSONレスポンス）
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// DB接続
include('./BlogPDO.php');

try {
    $pdo = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    echo json_encode(['error' => 'DB接続失敗: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
    exit;
}

// 前回登録から1分経っているか確認
try {
    $stmt = $pdo->prepare('SELECT created_at 
                           FROM temporary_users 
                           WHERE email = :email 
                           ORDER BY created_at DESC 
                           LIMIT 1');
    $stmt->execute([':email' => $email]);
    $last = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($last) {
        $lastTime = strtotime($last['created_at']);
        if (time() - $lastTime < 60) {
            echo json_encode([
                'success' => false,
                'error' => '直前に登録しています。1分以上経ってから再度お試しください。'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => '確認エラー: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// POSTデータ受け取り＆デコード
$input = json_decode(file_get_contents('php://input'), true);
$email = isset($input['email']) ? trim($input['email']) : '';

$response = ['success' => false];

if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // トークン生成（簡易的な例）
    $token = bin2hex(random_bytes(16));
    $expireTimestamp = time() + 86400; // 24時間（秒）
    $expireDatetime = date('Y-m-d H:i:s', $expireTimestamp);

    // DBに仮登録データを保存
    try {
        $stmt = $pdo->prepare('INSERT INTO temporary_users (email, token, expire, is_used) 
        VALUES (:email, :token, :expire, 0)');
        $stmt->execute([
            ':email' => $email,
            ':token' => $token,
            ':expire' => $expireDatetime,
        ]);
    } catch (PDOException $e) {
        // 重複トークンの可能性もあるがまずは失敗として返す
        echo json_encode(['success' => false, 'error' => 'DB保存エラー: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ユニークトークン付きの本登録URLを作成
    $registerUrl = "https://yellowokapi2.sakura.ne.jp/register?token={$token}";

    // メール設定
    mb_language("Japanese");
    mb_internal_encoding("UTF-8");

    $subject = "本登録のご案内";
    $body = <<<EOT
SPACEへの仮登録ありがとうございます。

※まだ本登録は完了しておりません。ご利用いただくには本登録が必要です。
以下のリンクから24時間以内に本登録を完了してください。

$registerUrl

※本メールにお心当たりがない場合は破棄してください。
EOT;

    $fromName = mb_encode_mimeheader('SPACE運営', 'UTF-8');
    $fromEmail = 'noreply@yellowokapi2.sakura.ne.jp';
    $fromHeader = "From: {$fromName} <{$fromEmail}>";

    // メール送信
    if (mb_send_mail($email, $subject, $body, $fromHeader, "-f $fromEmail")) {
        $response['success'] = true;
        $response['message'] = '仮登録が完了しました。メールをご確認ください。';
    } else {
        $response['error'] = 'メール送信に失敗しました。';
    }
} else {
    $response['error'] = '不正なメールアドレスよ。ちゃんと入力しなさい！';
}

// レスポンス返却
echo json_encode($response, JSON_UNESCAPED_UNICODE);
