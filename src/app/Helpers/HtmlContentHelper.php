<?php


namespace App\Helpers;


class HtmlContentHelper
{
    public static function getHtmlContent($htmlContent)
    {
        if(empty($htmlContent)){
            return "";
        }

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML(mb_convert_encoding($htmlContent, 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();
        $imagesElements = $dom->getElementsByTagName('img');
        $videosElements = $dom->getElementsByTagName('video');
        foreach ($imagesElements as $imageElement) {
            $pathInfo = PathHelper::getPathInfoForSecureUrl($imageElement->getAttribute('src'));
            if (!$pathInfo) {
                $imageElement->setAttribute('src', ''); //invalid url that's why set empty
                continue;
            }
            $src = OssStorageHelper::getSecureStoragePathForAssets(
                PathHelper::getStoragePath() . OssStorageHelper::encryptAssetURI($pathInfo['dirname'] . $pathInfo['basename']));
            $imageElement->setAttribute('src', $src);
        }
        foreach ($videosElements as $videoElement) {
            $pathInfo = PathHelper::getPathInfoForSecureUrl($videoElement->getAttribute('src'));
            if (!$pathInfo) {
                $videoElement->setAttribute('src', ''); //invalid url that's why set empty
                continue;
            }
            $src = OssStorageHelper::getSignUrl(PathHelper::getStoragePath() . $pathInfo['dirname'] . $pathInfo['basename'],
                250);
            $videoElement->setAttribute('src', $src);
        }
        return $dom->saveHTML();
    }

}
