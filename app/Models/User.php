<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'department',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relationships
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'submitted_by');
    }

    public function cashTransactions()
    {
        return $this->hasMany(CashTransaction::class, 'submitted_by');
    }

    public function approvals()
    {
        return $this->hasMany(TransactionApproval::class, 'approver_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class, 'created_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    // Methods
    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }

    public function hasApprovalLevel($level)
    {
        return $this->roles()->where('approval_level', '>=', $level)->exists();
    }
}
