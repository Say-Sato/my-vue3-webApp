<?php
// CORSヘッダーを追加
header('Access-Control-Allow-Origin: *'); // すべてのオリジンを許可（必要に応じてオリジンを制限）
header('Access-Control-Allow-Methods: POST, GET, OPTIONS'); // 許可するHTTPメソッド
header('Access-Control-Allow-Headers: Content-Type, Authorization'); // 許可するヘッダー

// データベース接続情報
$dsn      = 'mysql:dbname=yellowokapi2_mamemochimi;host=mysql3101.db.sakura.ne.jp;charset=utf8mb4';
$user     = 'yellowokapi2';
$password = 'Qawsedrftftgy5249';
