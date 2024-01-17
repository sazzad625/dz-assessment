<?php


namespace App\Helpers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use OSS\OssClient;

class OssVideoStream
{
    private $uri;
    private $ossClient;
    private $bucket;
    private $request;
    private $headers = [];

    private $buffer = 1024 * 500; //500 KB
    private $start = -1;
    private $end = -1;
    private $size = 0;

    private $statusCode = 200;

    public function __construct($uri, Request $request)
    {
        $this->uri = $uri;
        $this->request = $request;
        $accessKeyId = config('app.oss_key_id');
        $accessKeySecret = config('app.oss_key_secret');
        $endpoint = config('app.oss_endpoint');
        $this->bucket = config('app.oss_bucket_name');
        $this->ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint, false);

        $this->setHeaders();
    }

    private function setHeaders()
    {
        $this->start = 0;
        $this->size = Storage::size($this->uri);
        $this->end = $this->size - 1;

        $this->headers["Content-Type"] = Storage::mimeType($this->uri);
        $this->headers["Accept-Ranges"] = "0-" . $this->end;
        $this->headers["Cache-Control"] = "max-age=2592000, public";
        $this->headers["Expires"] = gmdate('D, d M Y H:i:s', time() + 2592000) . ' GMT';

        if ($this->request->hasHeader('Range')) {

            $c_start = $this->start;
            $c_end = $this->end;

            list(, $range) = explode('=', $this->request->header('Range'), 2);
            if (strpos($range, ',') !== false) {
                $this->statusCode = 416;
                $this->headers["Content-Range"] = "bytes $this->start-$this->end/$this->size";
                return;
            }

            $range = explode('-', $range);
            $c_start = $range[0];

            $c_end = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $c_end;


            $c_end = ($c_end > $this->end) ? $this->end : $c_end;
            if ($c_start > $c_end || $c_start > $this->size - 1 || $c_end >= $this->size) {
                $this->headers["Content-Range"] = "bytes $this->start-$this->end/$this->size";
                $this->statusCode = 416;
                return;
            }

            $this->start = $c_start;
            $this->end = $c_end;
            $length = $this->end - $this->start + 1;
            $this->statusCode = 206;
            $this->headers["Content-Length"] = $length;
            $this->headers["Content-Range"] = "bytes $this->start-$this->end/$this->size";

        } else {
            $this->headers["Content-Length"] = $this->size;
        }
    }

    public function stream()
    {
        ob_get_clean();
        $i = $this->start;
        for (; $i <= $this->end; $i += $this->buffer) {
            $bytesToRead = $i + $this->buffer;
            if ($bytesToRead > $this->end) {
                $bytesToRead = $this->end + 1;
            }
            $range = $i . "-" . ($bytesToRead - 1);
            $data = $this->ossClient->getObject($this->bucket, config('app.oss_root_path') . $this->uri, [
                OssClient::OSS_RANGE => $range
            ]);
            echo $data;
            flush();
        }
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

}
