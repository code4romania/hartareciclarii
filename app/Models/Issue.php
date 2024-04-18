<?php

declare(strict_types=1);

    /*
     * @Author: bib
     * @Date:   2023-10-03 10:55:55
     * @Last Modified by:   Bogdan Bocioaca
     * @Last Modified time: 2023-11-09 19:31:12
     */

namespace App\Models;

    use App\Enums\IssueStatus;
    use App\Models\MapPoint as MapPointModel;
    use App\Models\User as UserModel;
    use Carbon\Carbon;
    use Illuminate\Database\Eloquent\Model;
    use Spatie\MediaLibrary\HasMedia;
    use Spatie\MediaLibrary\InteractsWithMedia;
    use Spatie\MediaLibrary\MediaCollections\Models\Media;

    class Issue extends Model implements HasMedia
    {
        use InteractsWithMedia;

        protected $table = 'reported_point_issues';

        protected $casts = [
            'material_issue' => 'array',
            'material_issue_missing' => 'array',
            'material_issue_extra' => 'array',
            'collection_decline_reason' => 'array',
        ];

        public function type()
        {
            return $this->hasOne(IssueType::class, 'id', 'reported_point_issue_type_id');
        }

        public function map_point()
        {
            return $this->hasOne(MapPointModel::class, 'id', 'point_id');
        }

        public function reporter()
        {
            return $this->belongsTo(UserModel::class, 'reporter_id');
        }

        public static function createFromArray($data)
        {
            $issue = new self();
            $issue->point_id = $data['point_id'];
            $issue->reporter_id = $data['id_user'];
            $issue->status = IssueStatus::New;
            $issue->reported_point_issue_type_id = $data['reported_point_issue_type_id'];
            $issue->description = self::_fillDescription($data);
            $issue->created_at = Carbon::now()->toDateTimeString();

            switch ($data['reported_point_issue_type_id']) {
                case 1:
                case 2:
                    $issue->latitude = $data['lat'];
                    $issue->longitude = $data['lng'];
                    break;

                case 3:
                    if (isset($data['material_issue'])) {
                        $issue->material_issue = $data['material_issue'];
                    }

                    if (isset($data['material_issue_missing'])) {
                        $issue->material_issue_missing = $data['material_issue_missing'];
                    }

                    if (isset($data['material_issue_extra'])) {
                        $issue->material_issue_extra = $data['material_issue_extra'];
                    }

                    break;

                case 6:
                    if (isset($data['collection_decline_reason'])) {
                        $issue->collection_decline_reason = $data['collection_decline_reason'];
                    }
                    break;
            }

            $issue->save();

            return $issue;
        }

        private static function _fillDescription($data)
        {
            $description = '';
            if (isset($data['description'])) {
                $description = $data['description'];
            }

            if (isset($data['address'])) {
                $description = $data['address'];
            }

            return $description;
        }

        public function registerMediaCollections(): void
        {
            $this->addMediaCollection('issue_report_images')
                ->singleFile()
                ->registerMediaConversions(function (Media $media) {
                    $this
                        ->addMediaConversion('preview');
                });
        }
    }
