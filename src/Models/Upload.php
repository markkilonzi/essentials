<?php

namespace Nyumbani\Essentials\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;
use Nyumbani\Essentials\Enums\UploadDisk;
use Nyumbani\Essentials\Traits\HasUlids;

/**
 * Upload model representing a file (image or document) attached to any resource in the system
 *
 * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
 */
class Upload extends Model
{
    use HasFactory, HasUlids;

    /**
     * The attributes that are mass assignable
     *
     * @var list<string>
     */
    protected $fillable = [
        'owner_type',
        'owner_id',
        'title',
        'path',
        'size',
        'disk',
        'type',
    ];

    /**
     * Get the attributes that should be cast, the type being cast to the app's enum named in essentials.enums.upload_type.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function casts(): array
    {
        return array_filter([
            'type' => config('essentials.enums.upload_type'),
            'disk' => UploadDisk::class,
            'size' => 'integer',
        ]);
    }

    /**
     * Boot the model with lifecycle hooks
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected static function booted(): void
    {
        //  Call the saving hook to set the default disk and compute the size
        static::saving(function (self $upload) {
            //  Default the disk, which has to happen here since saving runs before creating
            $upload->disk ??= UploadDisk::PUBLIC;

            //  Compute the file size from the resolved disk whenever the stored file exists
            if ($upload->path && Storage::disk($upload->disk->value)->exists($upload->path)) {
                $upload->size = Storage::disk($upload->disk->value)->size($upload->path);
            }
        });

        //  Call the deleted hook to remove the physical file
        static::deleted(function (self $upload) {
            //  Remove the physical file from disk when the upload record is deleted
            if ($upload->path) {
                Storage::disk($upload->disk->value)->delete($upload->path);
            }
        });
    }

    /**
     * Get the publicly accessible URL for this upload
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function url(): Attribute
    {
        return Attribute::get(
            fn () => $this->disk === UploadDisk::PUBLIC
                ? Storage::disk('public')->url($this->path)
                : null
        );
    }

    /**
     * Get the human-readable file size
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function formattedSize(): Attribute
    {
        return Attribute::get(fn () => $this->size ? Number::fileSize($this->size) : null);
    }

    /**
     * Get the file extension of this upload
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function extension(): Attribute
    {
        return Attribute::get(fn () => pathinfo($this->path, PATHINFO_EXTENSION));
    }

    /**
     * Get the model that owns this upload
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Determine whether this upload is a previewable image
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function isImage(): bool
    {
        return in_array(strtolower($this->extension), [
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg',
        ]);
    }
}
