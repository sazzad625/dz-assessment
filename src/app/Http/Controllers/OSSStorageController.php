<?php

namespace App\Http\Controllers;

use App\Helpers\AuthHelper;
use App\Helpers\OssStorageHelper;
use App\Helpers\OssVideoStream;
use App\Helpers\PathHelper;
use App\Models\Permission;
use App\Models\ReportExport;
use App\Traits\FileManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use OSS\OssClient;

class OSSStorageController extends Controller
{
    private $fileManager;

    public function __construct(FileManager $fileManager)
    {
        $this->fileManager = $fileManager;
    }

    public function getPublicAssets($uri)
    {
        if (!Storage::disk('oss')->exists($uri)) {
            abort(404);
        }

        return Storage::disk('oss')->response($uri);
    }

    public function getSecureAssets($uri, Request $request)
    {
        $uri = PathHelper::excludeStoragePathFromUri($uri);

        //if requested path is not encrypted then only allowed user can access it
        if (!OssStorageHelper::isEncryptedURI($uri)) {
            AuthHelper::hasPermissionElseAbort(Permission::ACCESS_SECURE_ASSETS_PERMISSION);
            if (Str::of($uri)->endsWith(".mp4")) {
                return $this->streamVideo(PathHelper::getStoragePath() . $uri, $request);
            }
            return Storage::disk('oss')->response(PathHelper::getStoragePath() . $uri);
        }

        $uri = OssStorageHelper::decryptAssetURI($uri);

        if (empty($uri) || Str::of($uri)->endsWith(".mp4")) {
            abort(403);
        }

        return Storage::disk('oss')->response(PathHelper::getStoragePath() . $uri);
    }

    private function streamVideo($uri, Request $request)
    {
        $ossVideoStream = new OssVideoStream($uri, $request);
        return response()->stream(function () use ($uri, $ossVideoStream) {
            if(in_array($ossVideoStream->getStatusCode(), [200, 206])) {
                $ossVideoStream->stream();
            }
        }, $ossVideoStream->getStatusCode(), $ossVideoStream->getHeaders());
    }

    public function reportDownload($id)
    {
        AuthHelper::hasPermissionElseAbort(Permission::REPORT_DOWNLOAD_PERMISSION);
        $export = ReportExport::where(['id' => $id, 'fk_user_id' => Auth::id()])->first();
        if(!empty($export->path))
        {
            return Storage::response($export->path, substr($export->path, strrpos($export->path, "-", -1)+1));
        }
        return redirect()->back()->with('status.error', "File not found");
    }
}
