<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$url = "https://tivi.viulk.xyz/metadata/epg.xml";

$xmlContent = file_get_contents($url);
if ($xmlContent === false) {
    echo "⚠️ Failed to download XML\n";
    exit(1);
}

$xml = simplexml_load_string($xmlContent);
if ($xml === false) {
    echo "⚠️ Failed to parse XML\n";
    exit(1);
}

foreach ($xml->programme as $programme) {
    $title = trim((string)$programme->title);
    if (isset($programme->desc)) {
        $programme->desc = $title;
    } else {
        $programme->addChild("desc", $title);
    }
}

if ($xml->asXML("epg_fixed.xml")) {
    echo "✅ EPG updated successfully and epg_fixed.xml created.\n";
} else {
    echo "⚠️ Failed to write epg_fixed.xml\n";
    exit(1);
}
