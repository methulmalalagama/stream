<?php
// Your M3U and EPG URLs
$m3uUrl = "https://tivimate.viulk.xyz/channels.m3u";
$epgUrl = "https://tivi.viulk.xyz/metadata/epg.xml";

// Load EPG
libxml_use_internal_errors(true);
$epgXml = simplexml_load_file($epgUrl);
if (!$epgXml) {
    echo "❌ Failed to load EPG XML\n";
    foreach (libxml_get_errors() as $error) echo $error->message;
    exit;
}

// Load M3U
$m3uContent = file_get_contents($m3uUrl);
if (!$m3uContent) {
    echo "❌ Failed to load M3U\n";
    exit;
}

// Parse M3U channels
preg_match_all('/#EXTINF:[^\n]*tvg-id="([^"]+)".*?\n(https?:\/\/[^\n]+)/', $m3uContent, $matches, PREG_SET_ORDER);
$channels = [];
foreach ($matches as $m) {
    $channels[$m[1]] = [
        'name' => trim($m[0]),
        'url' => trim($m[2])
    ];
}

// Create new XMLTV
$newXml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><tv></tv>');

// Add <channel> elements
foreach ($channels as $tvgId => $data) {
    $ch = $newXml->addChild("channel");
    $ch->addAttribute("id", $tvgId);
    $dn = $ch->addChild("display-name");
    $dn->addAttribute("lang", "en");
    $dn->addCData($data['name']);
}

// Helper to add CDATA
function addCData(SimpleXMLElement $node, $cdata_text) {
    $dom = dom_import_simplexml($node);
    $dom->appendChild($dom->ownerDocument->createCDATASection($cdata_text));
}

// Copy & fix programmes
foreach ($epgXml->programme as $prog) {
    $chId = (string)$prog['channel'];
    if (!isset($channels[$chId])) continue; // skip unmatched channels

    $p = $newXml->addChild("programme");
    $p->addAttribute("start", $prog['start']);
    $p->addAttribute("stop", $prog['stop']);
    $p->addAttribute("channel", $chId);

    // Title
    $titleText = trim((string)$prog->title);
    $titleNode = $p->addChild("title");
    $titleNode->addAttribute("lang", "en");
    addCData($titleNode, $titleText);

    // Description
    $descNode = $p->addChild("desc");
    $descNode->addAttribute("lang", "en");
    addCData($descNode, $titleText); // you can append extra text here

    // Icon
    if (isset($prog->icon)) {
        $iconNode = $p->addChild("icon");
        $iconNode->addAttribute("src", (string)$prog->icon['src']);
    }

    // Copy catchup attributes if present
    foreach (['catchup', 'catchup-days', 'catchup-source'] as $attr) {
        if (isset($prog[$attr])) $p->addAttribute($attr, (string)$prog[$attr]);
    }
}

// Save fixed XML
$newXml->asXML("epg_fixed.xml");
echo "✅ Fully OTT-ready EPG saved as epg_fixed.xml\n";
