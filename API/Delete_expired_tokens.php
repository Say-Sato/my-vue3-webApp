<?php
//DB接続
include('./BlogPDO.php');

try {
    $pdo = new PDO($dsn, $user, $password);
    $sql = "DELETE FROM temporary_users WHERE expire < NOW() OR is_used = 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    echo date('Y-m-d H:i:s') . " - 削除完了: " . $stmt->rowCount() . "件\n";

} catch (PDOException $e) {
    echo "エラー: " . $e->getMessage();
}
?>
