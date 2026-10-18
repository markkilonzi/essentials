<?php

namespace Nyumbani\Essentials\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;
use Nyumbani\Essentials\Enums\UploadDisk;

/**
 * File behaviour for the app's Upload model, which also uses HasUlids
 *
 * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
 */
trait HasUploads
{
    /**
     * Boot the file hooks of an upload
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected static function bootHasUploads(): void
    {
        //  Call the saving hook to set the default disk and compute the size
        static::saving(function (self $model) {
            //  Default the disk, which has to happen here since saving runs before creating
            $model->disk ??= UploadDisk::PUBLIC;

            //  Compute the file size from the resolved disk whenever the stored file exists
            if ($model->path && Storage::disk($model->disk->value)->exists($model->path)) {
                $model->size = Storage::disk($model->disk->value)->size($model->path);
            }
        });

        //  Call the deleted hook to remove the physical file
        static::deleted(function (self $model) {
            //  Remove the physical file from disk when the upload record is deleted
            if ($model->path) {
                Storage::disk($model->disk->value)->delete($model->path);
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
