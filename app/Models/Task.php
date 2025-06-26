<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{

    use SoftDeletes;   
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'date',
        'start_time',
        'end_time',
        'status',
        'notified',
        'description',
        'category',
    ];

    protected $with = ['participants'];

    public function participants()
    {
        return $this->belongsToMany(User::class)
            ->withTimestamps()
            ->select(['users.id', 'users.name', 'users.email']);
    }
}
