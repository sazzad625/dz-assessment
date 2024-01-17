<?php

namespace App\Models;

use App\Mail\SetPasswordMail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    const USER_TYPE_ADMIN = 'ADMIN';
    const USER_TYPE_TEACHER = 'TEACHER';
    const USER_TYPE_STUDENT = 'STUDENT';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role', 'fk_user_id', 'fk_role_id');
    }

    /**
     *
     * @return array()
     */
    public function getRolesAndPermissions()
    {
        $allRoles = [];
        $allPermissions = [];
        $this->load('roles', 'roles.permissions');
        foreach ($this->roles as $role) {
            $allRoles[] = $role->name;
            foreach ($role->permissions as $permission) {
                $allPermissions[] = $permission->name;
            }
        }
        return ['roles' => $allRoles, 'permissions' => array_unique($allPermissions)];
    }

    /**
     * @param string // permission name
     *
     * @return boolean
     */
    function hasPermission($permission)
    {
        return in_array($permission, Session::get('permissions'));
    }

    function hasPermissionElseAbort($permission)
    {
        if (!$this->hasPermission($permission)) {
            abort('403');
        }
    }

    function department()
    {
        return $this->belongsTo(Department::class, 'fk_department_id', 'id')->withTrashed();
    }

    function country()
    {
        return $this->belongsTo(Country::class, 'fk_country_id', 'id');
    }

    /**
     * @param string // role name
     *
     * @return boolean
     */
    function hasRole($role)
    {
        return in_array($role, Session::get('roles'));
    }

    /**
     * @param string // permission names array
     *
     * @return boolean
     */
    function hasPermissionAny($permissions)
    {
        $intersectArray = array_intersect(Session::get('permissions'), $permissions);
        return (count($intersectArray) > 0) ? true : false;
    }

    /**
     * @param string // role names array
     *
     * @return boolean
     */
    function hasRoleAny($roles)
    {
        $intersectArray = array_intersect(Session::get('roles'), $roles);
        return (count($intersectArray) > 0) ? true : false;
    }

    /**
     *
     * @return boolean
     */
    function isAdmin()
    {
        return Auth::user()->type === self::USER_TYPE_ADMIN;
    }

    /**
     *
     * @return boolean
     */
    function isStudent()
    {
        return Auth::user()->type === self::USER_TYPE_STUDENT;
    }

    /**
     *
     * @return boolean
     */
    function isTeacher()
    {
        return Auth::user()->type === self::USER_TYPE_TEACHER;
    }

    public function sendSetPasswordMail()
    {
        $token = Password::getRepository()->create($this);
        $mail = new SetPasswordMail();
        $mail
            ->setToEmail($this->email)
            ->setToken($token)
            ->setUserName($this->name)
            ->sendMail();
    }

    public function courses()
    {
        return $this->belongsToMany(
            Course::class,
            'course_user',
            'fk_user_id',
            'fk_course_id',
        );
    }
}
