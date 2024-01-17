<?php


namespace App\Helpers;


use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PathHelper
{
    const DOCUMENT_EXTENSIONS = ['pdf', 'doc', 'docx', 'ppt', 'xlsx', 'odp', 'ods', 'odt', 'txt'];

    public static function getPublicPath()
    {
        return "public/";
    }

    public static function getTempPath()
    {
        return "temp/";
    }

    public static function getStoragePath()
    {
        return "storage/";
    }

    public static function getCourseContentPath()
    {
        return "course-content/";
    }

    public static function getNewCourseContentPath()
    {
        $now = Carbon::now();

        return sprintf(
            "%s%s/%s/",
            self::getCourseContentPath(),
            $now->year,
            $now->monthName
        );

    }

    public static function excludeStoragePathFromUri($uri)
    {
        return Str::of($uri)->substr(Str::of(self::getStoragePath())->length());
    }

    public static function getNewStoragePath()
    {
        $now = $now = Carbon::now();

        $path = sprintf(
            "%s%s/%s/",
            self::getStoragePath(),
            $now->year,
            $now->monthName);

        if (!Storage::exists($path)) {
            Storage::makeDirectory($path);
        }

        return $path;

    }

    public static function getNewPublicPath()
    {
        $now = Carbon::now();

        return sprintf(
            "%s%s/%s/",
            self::getPublicPath(),
            $now->year,
            $now->monthName
        );
    }

    public static function getNewTempPath()
    {
        $now = Carbon::now();

        return sprintf(
            "%s%s/%s/",
            self::getTempPath(),
            $now->year,
            $now->monthName
        );
    }

    public static function getFileName($fileName)
    {
        return Carbon::now()->toFileTimeFormat() . '-' . uniqid() . '-' . $fileName;
    }

    public static function getFileNameFromURI($uri)
    {
        return pathinfo($uri)['basename'];
    }

    public static function getPathInfoForSecureUrl($src)
    {
        $pathInfo = pathinfo($src);
        $storagePos = strpos($pathInfo['dirname'], self::getStoragePath());
        if (empty($pathInfo['basename']) || empty($pathInfo['dirname']) || $storagePos === false) {
            return false;  //invalid url
        }
        $dirname = substr($pathInfo['dirname'], $storagePos + strlen(self::getStoragePath())) . '/';
        return ['basename' => $pathInfo['basename'], 'dirname' => $dirname];
    }

    public static function isDocument($uri)
    {
        return Str::endsWith(Str::lower($uri), self::DOCUMENT_EXTENSIONS);
    }

    public static function getExtension($uri)
    {
        return str::lower(pathinfo($uri)['extension']);
    }
}
