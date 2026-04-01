<?php

function updateEPG() {
    $url = "https://tivi.viulk.xyz/metadata/epg.xml";

    $xmlContent = file_get_contents($url);
    if (!$xmlContent) {
        echo "Failed to download\n";
        return;
    }

    $xml = simplexml_load_string($xmlContent);

    foreach ($xml->programme as $programme) {
        $title = (string)$programme->title;
        $programme->desc = $title;
    }

    file_put_contents("epg_fixed.xml", $xml->asXML());

    echo "EPG Updated\n";
}

while (true) {
    updateEPG();
    sleep(3600); // wait 1 hour
}
