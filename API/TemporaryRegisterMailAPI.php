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

// 以降は元のコード...


// POSTデータ受け取り＆デコード
$input = json_decode(file_get_contents('php://input'), true);
$email = isset($input['email']) ? trim($input['email']) : '';

$response = ['success' => false];

if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // トークン生成（簡易的な例）
    $token = bin2hex(random_bytes(16));
    $expire = time() + 86400; // 24時間（秒）

    // 仮登録データをDBやファイルに保存する処理（必要に応じて追加）

    // 本登録用URL
    $registerUrl = "http://localhost:5173/register";

    // メール設定
    mb_language("Japanese");
    mb_internal_encoding("UTF-8");

    $subject = "本登録のご案内";
    $body = <<<EOT

SPACEへのご登録ありがとうございます。

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
    }
} else {
    $response['error'] = '不正なメールアドレスよ。ちゃんと入力しなさい！';
}

// レスポンス返却
echo json_encode($response, JSON_UNESCAPED_UNICODE);

?>