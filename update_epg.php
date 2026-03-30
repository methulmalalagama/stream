<?php
$url = "https://tivi.viulk.xyz/metadata/epg.xml";

$xmlContent = file_get_contents($url);
if (!$xmlContent) {
    die("Failed to download XML.");
}

$xml = simplexml_load_string($xmlContent);
if (!$xml) {
    die("Failed to parse XML.");
}

foreach ($xml->programme as $programme) {
    $title = trim((string)$programme->title);

    if (isset($programme->desc)) {
        $programme->desc = $title;
    } else {
        $programme->addChild("desc", $title);
    }
}

$xml->asXML("epg_fixed.xml");
echo "EPG updated successfully!";
?>
