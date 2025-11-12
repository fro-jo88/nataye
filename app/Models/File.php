<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'uploader_id',
        'path',
        'filename',
        'mime_type',
        'size_bytes',
        'reference_type',
        'reference_id',
    ];

    protected $casts = [
        'size_bytes' => 'integer',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }

    public function getUrl(): string
    {
        return Storage::url($this->path);
    }

    public function getHumanReadableSizeAttribute(): string
    {
        $bytes = $this->size_bytes;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    public function isDocument(): bool
    {
        return in_array($this->mime_type, [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function delete(): bool
    {
        Storage::delete($this->path);
        return parent::delete();
    }

    public function scopeByReference($query, string $type, int $id)
    {
        return $query->where('reference_type', $type)
                    ->where('reference_id', $id);
    }

    public function scopeByUploader($query, int $uploaderId)
    {
        return $query->where('uploader_id', $uploaderId);
    }
}
