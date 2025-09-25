<?php
// CORSヘッダーを追加
header('Access-Control-Allow-Origin: *'); // すべてのオリジンを許可（必要に応じてオリジンを制限）
header('Access-Control-Allow-Methods: POST, GET, OPTIONS'); // 許可するHTTPメソッド
header('Access-Control-Allow-Headers: Content-Type, Authorization'); // 許可するヘッダー

// データベース接続情報サンプル
$dsn      = 'mysql:dbname=hogehoge;host=mysql****.db.example.com;charset=utf8mb4';
$user     = 'hogehoge';
$password = '*********';
$secretKey = '********';
