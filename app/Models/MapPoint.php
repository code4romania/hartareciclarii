<?php

/*
 * @Author: bib
 * @Date:   2023-10-03 10:55:55
 * @Last Modified by:   Bogdan Bocioaca
 * @Last Modified time: 2023-10-03 22:15:43
 */

namespace App\Models;

use App\Models\MapPointGroup as MapPointGroupModel;
use App\Models\MapPointIssues as MapPointIssuesModel;
use App\Models\MapPointToField as MapPointToFieldModel;
use App\Models\MapPointType as MapPointTypeModel;
use App\Models\MaterialToMapPoint as MaterialToMapPointModel;
use App\Models\RecycleMaterial as RecycleMaterialModel;
use App\Models\User as UserModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MapPoint extends Model
{
    protected $table = 'recycling_points';

    protected $fillable = ['name'];

    public function getType()
    {
        return $this->hasOne(MapPointTypeModel::class, 'id', 'point_type_id');
    }

    public function getFields()
    {
        return $this->hasMany(
            MapPointToFieldModel::class,
            'recycling_point_id',
            'id',
        );
    }

    public function getCounty()
    {
        return $this->hasOne(
            MapPointToFieldModel::class,
            'recycling_point_id',
            'id',
        )->whereFieldTypeId(2);
    }

    public function getCity()
    {
        return $this->hasOne(
            MapPointToFieldModel::class,
            'recycling_point_id',
            'id',
        )->whereFieldTypeId(1);
    }

    public function getAddress()
    {
        return $this->hasOne(
            MapPointToFieldModel::class,
            'recycling_point_id',
            'id',
        )->whereFieldTypeId(4);
    }

    public function getManagedBy()
    {
        return $this->hasOne(
            MapPointToFieldModel::class,
            'recycling_point_id',
            'id',
        )->whereFieldTypeId(3);
    }

    public function getMaterials()
    {
        return $this->hasManyThrough(
            RecycleMaterialModel::class,
            MaterialToMapPointModel::class,
            'recycling_point_id',
            'id',
            'id',
            'material_id'
        );
    }

    public function getIssues()
    {
        return $this->hasMany(
            MapPointIssuesModel::class,
            'point_id',
            'id',
        );
    }

    public function getGroup()
    {
        return $this->belongsTo(
            MapPointGroupModel::class,
            'group_id',
            'id',
        );
    }

    public function getGod()
    {
        return $this->belongsTo(
            UserModel::class,
            'created_by',
            'id',
        );
    }

    public function scopeManageble(Builder $query): void
    {
        if (!auth()->user()->hasRole('SuperAdmin'))
        {
            $query->where('created_by', auth()->user()->id);
        }
    }

    public function changeStatus(): bool
    {
        $this->status = !$this->status;
        $this->save();

        return true;
    }
}
