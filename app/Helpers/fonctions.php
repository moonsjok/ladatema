 <?php
function formateTime($time){
        // S'assurer que le temps est positif pour le formatage
        $timeForFormatting = max(0, $time);
        
        $minutes = floor($timeForFormatting / 60);
        $seconds = $timeForFormatting % 60;
        return sprintf("%02d:%02d", $minutes, $seconds);
}

function myStripTags($text){
    return html_entity_decode(strip_tags($text));
}