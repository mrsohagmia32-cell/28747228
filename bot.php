<?php
// ==========================================
// সরাসরি সমস্ত কনফিগারেশন
// ==========================================
$pexelsApiKey     = 'DiqA3nNE1bXk1CaViv1inoLX9HW18I8g6GtECOhrp6y8kdloYRvRZRCd';
$telegramBotToken = '8660862030:AAE1ieT8MVW8MOjlZE0gMnM_ACNFDY2foNI';
$channelId        = '-1003951625461';

// বিভিন্ন ক্যাটাগরির ছবি র্যান্ডমলি বাছাইয়ের ব্যবস্থা
$categories = ['nature', 'wallpaper', 'technology', 'cars', 'abstract', 'dark'];
$searchQuery = $categories[array_rand($categories)];

// Pexels API Call
$pexelsUrl = "https://api.pexels.com/v1/search?query=" . urlencode($searchQuery) . "&per_page=30&page=" . rand(1, 15);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $pexelsUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: {$pexelsApiKey}"
]);
$pexelsResponse = curl_exec($ch);
curl_close($ch);

$data = json_decode($pexelsResponse, true);

if (!empty($data['photos'])) {
    // যেকোনো একটি ছবি সিলেক্ট করা
    $randomPhoto  = $data['photos'][array_rand($data['photos'])];
    $imageUrl     = $randomPhoto['src']['large2x']; // HD Quality
    $photographer = $randomPhoto['photographer'];
    $photoPage    = $randomPhoto['url'];

    // টেলিগ্রাম ক্যাপশন
    $caption  = "📸 <b>New HD Photo</b>\n\n";
    $caption .= "👤 Photo by: <a href='{$photoPage}'>{$photographer}</a>\n";
    $caption .= "🏷️ Category: #" . ucfirst($searchQuery) . "\n\n";
    $caption .= "<i>Auto Uploaded via GitHub Actions 🚀</i>";

    // Telegram Bot API - sendPhoto
    $telegramUrl = "https://api.telegram.org/bot{$telegramBotToken}/sendPhoto";
    $postData = [
        'chat_id'    => $channelId,
        'photo'      => $imageUrl,
        'caption'    => $caption,
        'parse_mode' => 'HTML'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $telegramUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);

    echo "Photo posted successfully to Telegram channel!\n";
} else {
    echo "Failed to fetch image from Pexels API.\n";
}
?>
