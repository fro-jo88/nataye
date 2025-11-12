<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ParentModel extends Model
{
    use HasFactory;

    protected $table = 'parents';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'relation',
        'address',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_parent')
            ->withPivot('is_primary', 'linked_at')
            ->withTimestamps();
    }

    public function primaryStudents(): BelongsToMany
    {
        return $this->students()->wherePivot('is_primary', true);
    }

    public function linkToStudent(Student $student, bool $isPrimary = false): void
    {
        $this->students()->syncWithoutDetaching([
            $student->id => [
                'is_primary' => $isPrimary,
                'linked_at' => now(),
            ]
        ]);
    }

    public function unlinkFromStudent(Student $student): void
    {
        $this->students()->detach($student->id);
    }

    public function scopeByPhone($query, string $phone)
    {
        return $query->where('phone', $phone);
    }

    public function scopeByEmail($query, string $email)
    {
        return $query->where('email', $email);
    }

    public function getPhoneNormalizedAttribute(): ?string
    {
        if (!$this->phone) {
            return null;
        }
        
        try {
            $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
            $numberProto = $phoneUtil->parse($this->phone, config('app.phone_default_country', 'US'));
            return $phoneUtil->format($numberProto, \libphonenumber\PhoneNumberFormat::E164);
        } catch (\Exception $e) {
            return $this->phone;
        }
    }
}
