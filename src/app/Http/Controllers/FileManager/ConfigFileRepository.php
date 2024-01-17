<?php


namespace App\Http\Controllers\FileManager;


use Alexusmai\LaravelFileManager\Services\ConfigService\DefaultConfigRepository;
use App\Helpers\PathHelper;

class ConfigFileRepository extends DefaultConfigRepository
{
    public function getLeftPath(): ?string
    {
        return PathHelper::getNewStoragePath();
    }

}
