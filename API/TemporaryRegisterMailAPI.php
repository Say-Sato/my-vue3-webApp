<?php
const REGISTRATION_LIMIT_SECONDS = 60; // 1分制限
const TOKEN_EXPIRATION_SECONDS = 86400; // 24時間

// プリフライト対応
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    exit(0);
}

// ヘッダー設定
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// 共通レスポンス関数
function jsonResponse(array $data)
{
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// データベース接続
include('./BlogPDO.php');

try {
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    jsonResponse(['success' => false, 'error' => 'DB接続に失敗しました。']);
}

//==============================================================================
// 1. 入力値取得・検証
//==============================================================================

$input = json_decode(file_get_contents('php://input'), true);
$email = isset($input['email']) ? trim($input['email']) : '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(['success' => false, 'error' => '不正な形式のメールアドレスです。']);
}

//==============================================================================
// 2. 登録可否チェック（1分以内の再登録防止）
//==============================================================================

try {
    $stmt = $pdo->prepare(
        'SELECT created_at FROM temporary_users WHERE email = :email ORDER BY created_at DESC LIMIT 1'
    );
    $stmt->execute([':email' => $email]);
    $last = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($last && (time() - strtotime($last['created_at']) < REGISTRATION_LIMIT_SECONDS)) {
        jsonResponse(['success' => false, 'error' => '続けて登録することはできません。1分以上経ってから再度お試しください。']);
    }
} catch (PDOException $e) {
    jsonResponse(['success' => false, 'error' => '登録状態の確認中にエラーが発生しました。']);
}

//==============================================================================
// 3. トークン生成・DB保存
//==============================================================================

$token = bin2hex(random_bytes(16));
$expireAt = date('Y-m-d H:i:s', time() + TOKEN_EXPIRATION_SECONDS);

try {
    $stmt = $pdo->prepare(
        'INSERT INTO temporary_users (email, token, expire, is_used) VALUES (:email, :token, :expire, 0)'
    );
    $stmt->execute([
        ':email' => $email,
        ':token' => $token,
        ':expire' => $expireAt
    ]);
} catch (PDOException $e) {
    jsonResponse(['success' => false, 'error' => '仮登録データの保存に失敗しました。']);
}

//==============================================================================
// 4. 本登録メール送信
//==============================================================================

$registerUrl = "https://yellowokapi2.sakura.ne.jp/register?token={$token}";

$subject = "【SPACE】本登録のご案内";
$body = <<<EOT
SPACEへの仮登録ありがとうございます。

まだ本登録は完了しておりません。
以下のリンクから24時間以内に本登録を完了してください。

{$registerUrl}

※お心当たりがない場合は破棄してください。
EOT;

$fromName = mb_encode_mimeheader('SPACE運営', 'UTF-8');
$fromEmail = 'noreply@yellowokapi2.sakura.ne.jp';
$headers = "From: {$fromName} <{$fromEmail}>";

if (mb_send_mail($email, $subject, $body, $headers, "-f {$fromEmail}")) {
    jsonResponse(['success' => true, 'message' => '仮登録が完了しました。メールをご確認ください。']);
} else {
    jsonResponse(['success' => false, 'error' => 'メールの送信に失敗しました。時間をおいて再度お試しください。']);
}
