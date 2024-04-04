<?php

declare(strict_types=1);

/*
 * @Author: bib
 * @Date:   2023-10-03 10:55:55
 * @Last Modified by:   Bogdan Bocioaca
 * @Last Modified time: 2023-10-05 21:59:06
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class MapPointToField extends Model
{
    protected $table = 'field_type_recycling_point';

    protected $fillable = ['field_type_id', 'recycling_point_id'];

    public static function addValueToPoint(Collection $field): self
    {
        $item = self::firstOrCreate(['field_type_id' => $field->get('field_type_id'), 'recycling_point_id' => $field->get('recycling_point_id')]);
        $item->value = $field->get('value');
        $item->save();

        return $item;
    }

    public function field()
    {
        return $this->hasOne(
            MapPointField::class,
            'id',
            'field_type_id',
        );
    }

    public static function addValuesToPoint($fields): bool
    {
        foreach ($fields as $field) {
            self::addValueToPoint($field);
        }

        return true;
    }
}
