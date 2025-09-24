<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=utf-8");

include('./BlogPDO.php');

$requestUri = $_SERVER['REQUEST_URI'];
$id = null;

if (preg_match('/\/(\d+)$/', $requestUri, $matches)) {
    $id = intval($matches[1]);
}

$data = [];

try {
    $dbh = new PDO($dsn, $user, $password);

    if ($id !== null) {
        $query = "SELECT p.*,
             n.notify_comment AS n_comment,
             n.notify_follow AS n_follow,
             n.notify_favorite AS n_favorite,
             n.notify_illust AS n_illust
            FROM profile p
            JOIN notification_settings n 
            ON n.user_id = p.id
            WHERE p.id = :id;
            ";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // 🔑 AESの鍵（16文字 → AES-128）
            $key = 'are0421'; // 本番では.envに！

            // 🔐 パスワードを復号
            $encryptedHex = $row['password'];
            $encryptedBin = hex2bin($encryptedHex);

            $decryptedPassword = openssl_decrypt(
                $encryptedBin,
                'AES-128-ECB',
                $key,
                OPENSSL_RAW_DATA
            );

            $data = [
                "id" => $row['id'],
                "login_id" => $row['login_id'],
                "password" => $row['password'],
                "password_decrypted" => $decryptedPassword !== false ? $decryptedPassword : null,
                "name" => $row['name'],
                "body" => $row['body'],
                "profile_photo" => $row['profile_photo'],
                "admin" => $row['admin'],
                "birthDate" => $row['birthDate'],
                "certificate18" => $row['certificate18'],
                "n_comment" => $row['n_comment'],
                "n_follow" => $row['n_follow'],
                "n_favorite" => $row['n_favorite'],
                "n_illust" => $row['n_illust']
            ];

            echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } else {
            http_response_code(404);
            echo json_encode(
                ["error" => "ID:{$id}のデータが見つかりません。"],
                JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
            );
        }
    } else {
        // 全件取得
        $query = "SELECT * FROM profile;";
        $stmt = $dbh->prepare($query);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = [
                "id" => $row['id'],
                "login_id" => $row['login_id'],
                "password" => $row['password'],
                "name" => $row['name'],
                "body" => $row['body'],
                "profile_photo" => $row['profile_photo'],
                "admin" => $row['admin'],
                "birthDate" => $row['birthDate'],
                "certificate18" => $row['certificate18']
            ];
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(
        ["error" => "データベースの接続に失敗しました: " . $e->getMessage()],
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );
} finally {
    $dbh = null;
}
