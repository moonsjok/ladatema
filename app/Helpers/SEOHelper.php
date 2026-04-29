<?php

namespace App\Helpers;

use SEOMeta;
use OpenGraph;
use TwitterCard;

class SEOHelper
{
    public static function setSEO($title, $description, $url = null, $image = null, $siteName = null)
    {
        $url = $url ?? url()->current();
        $siteName = $siteName ?? config('app.name');

        SEOMeta::setTitle($title);
        SEOMeta::setDescription($description);
        SEOMeta::setCanonical($url);

        OpenGraph::setTitle($title)
            ->setDescription($description)
            ->setUrl($url)
            ->setSiteName($siteName);

        if ($image) {
            OpenGraph::addImage($image);
        }

        TwitterCard::setTitle($title)
            ->setDescription($description);

        if ($image) {
            TwitterCard::addImage($image);
        }
    }
}
