<?php

declare(strict_types=1);

/*
 * @Author: bib
 * @Date:   2023-10-03 10:55:55
 * @Last Modified by:   Bogdan Bocioaca
 * @Last Modified time: 2023-10-06 19:14:19
 */

namespace App\Models;

use App\Models\RecycleMaterial as RecycleMaterialModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ActionLog extends Model
{
    protected $table = 'action_log';

    protected $fillable = ['name'];

    protected $casts = [
        // 'old_values' => 'json',
        // 'new_values' => 'json',
    ];

    public static function logAction(Collection $action): ?self
    {
        if ($action->get('action') == 'created' || (! empty($action->get('old_values')) && ! empty($action->get('new_values')))) {
            $item = new self();
            $item->model = $action->get('model');
            $item->model_id = $action->get('model_id');
            $item->user_id = $action->get('user_id');
            $item->action = $action->get('action');
            $item->old_values = ! empty($action->get('old_values')) ? json_encode($action->get('old_values')) : null;
            $item->new_values = ! empty($action->get('new_values')) ? json_encode($action->get('new_values')) : null;
            $item->save();

            return $item;
        }

        return null;
    }

    public static function formatValuesText($record, $values = 'old_values'): string
    {
        $text = '';
        if (! empty($record->$values)) {
            $values = json_decode($record->$values, true);
            foreach ($values as $key => $val) {
                if ($record->action == 'location_update') {
                    $text .= trans('actions.' . $key) . ': ' . $val . '<br />';
                } elseif ($record->action == 'details_update') {
                    if ($key == 'materials') {
                        $text .= trans('actions.' . $key) . '<br />';
                        foreach (RecycleMaterialModel::whereIn('id', $val)->get() as $material) {
                            $text .= $material->name . '<br />';
                        }
                    } elseif ($key == 'opening_hours') {
                        $text .= trans('actions.opening_hours') . '<br />';
                        foreach (json_decode($val, true) as $schedule) {
                            $text .= trans('actions.start_day') . trans('common.week_days.' . $schedule['start_day']) . ' ' . $schedule['start_hour'] . '<br/ >';
                            $text .= trans('actions.end_day') . trans('common.week_days.' . $schedule['end_day']) . ' ' . $schedule['end_hour'] . '<br/ >';
                        }
                    } elseif (\in_array($key, ['notes', 'website', 'email', 'phone_no'])) {
                        $text .= trans('actions.' . $key) . ' ' . $val . '<br />';
                    } else {
                        $text .= trans('actions.' . $key . '.' . strtolower($val)) . '<br />';
                    }
                } else {
                    if ($key == 'group') {
                        $text .= $val . '<br />';
                    } else {
                        $text .= trans('actions.' . $key . '.' . strtolower($val)) . '<br />';
                    }
                }
                $text .= '<hr />';
            }
        }

        return $text;
    }
}
