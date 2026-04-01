<?php
// auto_m3u.php

// 1️⃣ Set paths
$remote_m3u = "https://tivimate.viulk.xyz/channels.m3u";
$local_m3u = __DIR__ . "/channels.m3u"; // saved M3U file
$my_epg_url = "https://epg-production.up.railway.app/epg_fixed.xml";

// 2️⃣ Fetch remote M3U via cURL
$ch = curl_init($remote_m3u);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$m3u_content = curl_exec($ch);
curl_close($ch);

if (!$m3u_content) {
    echo "Failed to fetch remote M3U";
    exit;
}

// 3️⃣ Replace tvg-url with your EPG
$m3u_content = preg_replace('/url-tvg=".*?"/', 'url-tvg="'.$my_epg_url.'"', $m3u_content);

// 4️⃣ Save locally
file_put_contents($local_m3u, $m3u_content);

// 5️⃣ Output saved M3U
header("Content-Type: application/x-mpegURL");
echo $m3u_content;
?>
