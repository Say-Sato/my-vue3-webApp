<?php
// 入力を正規化
$subFolder = isset($subFolder) ? trim($subFolder, " \t\n\r\0\x0B/") : null;

// ホワイトリスト（定数でもOK）
// $subFolderにて許容する値
$allowedPaths = [
    'Blog/mypage/portfolio/photo',
    'Blog/mypage/portfolio/s_photo',
    'Blog/index/profile_photo'
];

// バリデーション
if (!$subFolder) {
    echo json_encode(["error" => "フォルダが指定されていません。"], JSON_UNESCAPED_UNICODE);
    exit;
} elseif (!in_array($subFolder, $allowedPaths, true)) {
    echo json_encode(["error" => "不正な保存パスが指定されました。"], JSON_UNESCAPED_UNICODE);
    exit;
}

// 画像ファイルがアップロードされているかチェック
if (!isset($_FILES['image'])) {
    echo json_encode(["error" => "画像ファイルがアップロードされていません。"], JSON_UNESCAPED_UNICODE);
    exit;
}

// 複数ファイル対応のため、配列形式をチェック
// 単数の場合は配列化して処理を統一する
$files = $_FILES['image'];
$fileCount = is_array($files['name']) ? count($files['name']) : 1;

if ($fileCount === 1) {
    // 単一ファイルの配列化
    $fileArray = [
        [
            'name' => $files['name'],
            'type' => $files['type'],
            'tmp_name' => $files['tmp_name'],
            'error' => $files['error'],
            'size' => $files['size'],
        ]
    ];
} else {
    // 複数ファイルの情報を1ファイルごとの連想配列にまとめる
    $fileArray = [];
    for ($i = 0; $i < $fileCount; $i++) {
        $fileArray[] = [
            'name' => $files['name'][$i],
            'type' => $files['type'][$i],
            'tmp_name' => $files['tmp_name'][$i],
            'error' => $files['error'][$i],
            'size' => $files['size'][$i],
        ];
    }
}

$allowedMimeTypes = [
    'image/jpeg' => '.jpg',
    'image/png' => '.png',
    'image/gif' => '.gif',
    'image/webp' => '.webp',
];

$savedFiles = [];

foreach ($fileArray as $file) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        // エラーあったらスキップ or すぐ終了してもOK
        continue;
    }
    
    $imageTmpPath = $file['tmp_name'];
    $imageMimeType = mime_content_type($imageTmpPath);
    
    if (!array_key_exists($imageMimeType, $allowedMimeTypes)) {
        // 対応してない形式はスキップ
        continue;
    }
    
    $extension = $allowedMimeTypes[$imageMimeType];
    $uniqueFileName = uniqid('image_', true) . $extension;
    $savePath = $_SERVER['DOCUMENT_ROOT'] . '/' . $subFolder . '/' . $uniqueFileName;
    
    if (!move_uploaded_file($imageTmpPath, $savePath)) {
        continue;
    }
    
    // 保存成功したファイル情報を配列に追加
    $domainName = 'yellowokapi2.sakura.ne.jp';
    $imageUrl = "http://$domainName/$subFolder/$uniqueFileName";
    $savedFiles[] = [
        'url' => $imageUrl,
        'filename' => $uniqueFileName,
    ];
}

if (count($savedFiles) === 0) {
    echo json_encode(["error" => "ファイルの保存に失敗しました。"], JSON_UNESCAPED_UNICODE);
    exit;
}

// 成功レスポンスを配列で返す
echo json_encode([
    "success" => true,
    "files" => $savedFiles,
], JSON_UNESCAPED_UNICODE);
?>
