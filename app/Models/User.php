<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Http\Resources\IssueResource;
use App\Http\Resources\MapPointResource;
use App\Notifications\User\ResetPasswordNotification as UserResetPasswordNotification;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasAvatar, HasName, HasMedia, CanResetPassword
{
    use InteractsWithMedia;
    use MustVerifyEmail;
    use HasFactory;
    use Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'accept_terms',
        'send_newsletter',
        'phone',
        'email',
        'password',
        'user_group_id',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'accept_terms' => 'boolean',
        'send_newsletter' => 'boolean',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->useFallbackUrl(
                \sprintf(
                    'https://ui-avatars.com/api/?%s',
                    Arr::query([
                        'name' => Str::initials($this->full_name),
                        'color' => 'FFFFFF',
                        'background' => '075985',
                    ])
                )
            )
            ->singleFile()
            ->registerMediaConversions(function () {
                $this->addMediaConversion('thumb')
                    ->fit(Fit::Crop, 96, 96)
                    ->keepOriginalImageFormat()
                    ->optimize();
            });
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->getFirstMediaUrl('avatar', 'thumb');
    }

    public function getFilamentName(): string
    {
        return $this->full_name;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return auth()->user()->can('admin_login');
    }

    public function getContributions()
    {
        $mapPoints = MapPointResource::collection(MapPoint::with('type', 'service', 'fields.field')->where('created_by', $this->id)->get());
        $issues = IssueResource::collection(IssueOld::with('type', 'map_point', 'map_point.fields.field')->where('reporter_id', $this->id)->get());

        $contributions = [];
        if (! empty($mapPoints)) {
            foreach ($mapPoints->collection->toArray() as $mapPoint) {
                $contributions[] =
                [
                    'date' => $mapPoint['created_at'],
                    'type' => __('common.contribution_types.point'),
                    'item_type' => $mapPoint['service']['display_name'] . ' (' . $mapPoint['type']['display_name'] . ')',
                    'location' => self::getAddress($mapPoint['fields']),
                    'status' => $mapPoint['status'],
                    'point_id' => $mapPoint['id'],
                ];
            }
        }

        if (! empty($issues)) {
            foreach ($issues->collection->toArray() as $issue) {
                $contributions[] =
                    [
                        'date' => $issue['created_at'],
                        'type' => __('common.contribution_types.issue'),
                        'item_type' => $issue['type']['title'],
                        'location' => self::getAddress($issue['map_point']['fields']),
                        'status' => $issue['status'],
                        'point_id' => $issue['point_id'],
                    ];
            }
        }

        return $contributions;
    }

    private static function getAddress($fields)
    {
        if (! empty($fields)) {
            foreach ($fields as $field) {
                if ($field['field']['field_name'] == 'address') {
                    return $field['value'];
                }
            }
        }

        return null;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new UserResetPasswordNotification($token));
    }

    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class);
    }

    public function points(): HasMany
    {
        return $this->hasMany(Point::class, 'created_by');
    }

    public function userGroup(): BelongsTo
    {
        return $this->belongsTo(UserGroup::class);
    }
}
