<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Filament\Resources\IssuesResource;
use App\Http\Resources\IssueResource;
use App\Http\Resources\MapPointResource;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasName
{
    use HasApiTokens;

    use HasFactory;

    use Notifiable;

    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
    ];

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return auth()->user()->can('admin_login');
    }

    public function getFilamentName(): string
    {
        return "{$this->firstname} {$this->lastname}";
    }

    public function getFullnameAttribute($value)
    {
        return "{$this->firstname} {$this->lastname}";
    }
	
	public function getContributions()
	{
		$mapPoints = MapPointResource::collection(MapPoint::with('type', 'service', 'fields.field')->where('created_by', $this->id)->get());
		$issues = IssueResource::collection(Issue::with('type', 'map_point', 'map_point.fields.field')->where('reporter_id', $this->id)->get());
		
		$contributions = [];
		if (!empty($mapPoints))
		{
			foreach ($mapPoints->collection->toArray() as $mapPoint)
			{
				$contributions[] =
				[
					'date' => $mapPoint['created_at'],
					'type' => __('common.contribution_types.point'),
					'item_type' => $mapPoint['service']['display_name'].' ('.$mapPoint['type']['display_name'].')',
					'location' => self::getAddress($mapPoint['fields']),
					'status' => $mapPoint['status'],
					'point_id' => $mapPoint['id']
				];
			}
		}
		
		if (!empty($issues))
		{
			foreach ($issues->collection->toArray() as $issue)
			{
				$contributions[] =
					[
						'date' => $issue['created_at'],
						'type' => __('common.contribution_types.issue'),
						'item_type' => $issue['type']['title'],
						'location' => self::getAddress($issue['map_point']['fields']),
						'status' => $issue['status'],
						'point_id' => $issue['point_id']
					];
			}
		}
		
		return $contributions;
	}
	
	private static function getAddress($fields)
	{
		if (!empty($fields))
		{
			foreach ($fields as $field)
			{
				if ($field['field']['field_name'] == 'address')
				{
					return $field['value'];
				}
			}
		}
		
		return null;
		
	}
}
