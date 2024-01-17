<?php


namespace App\Http\Controllers\FileManager;


use Illuminate\Support\Facades\Auth;

class ACLRepository implements \Alexusmai\LaravelFileManager\Services\ACLService\ACLRepository
{
    public function getUserID()
    {
        return Auth::id();
    }

    public function getRules(): array
    {
        return [
            ['disk' => 'oss', 'path' => '/', 'access' => 1],
            ['disk' => 'oss', 'path' => 'storage', 'access' => 2],
            ['disk' => 'oss', 'path' => 'storage/*', 'access' => 2],
        ];
    }

}
