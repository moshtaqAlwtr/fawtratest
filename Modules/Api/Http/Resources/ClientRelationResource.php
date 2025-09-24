<?php

namespace Modules\Api\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientRelationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                     => $this->id,
            'process'               => $this->process ?? 'بدون تصنيف',
            'description'           => $this->description ?? 'لا يوجد وصف',
            'deposit_count'         => $this->deposit_count,
            'site_type'             => $this->site_type,
            'site_type_label'       => $this->getSiteTypeLabel($this->site_type),
            'competitor_documents'  => $this->competitor_documents,
            'created_at'            => $this->created_at->format('Y-m-d H:i:s'),

            'employee' => [
                'id'   => optional($this->employee)->id,
                'name' => optional($this->employee)->name ?? 'غير معروف',
            ],

            'status' => [
                'id'    => optional($this->client->status_client)->id,
                'name'  => optional($this->client->status_client)->name,
                'color' => optional($this->client->status_client)->color ?? '#007BFF',
            ],

            'attachments' => $this->getAttachmentUrls($this->attachments),
        ];
    }

    protected function getSiteTypeLabel($type)
    {
        return match ($type) {
            'independent_booth' => 'بسطة مستقلة',
            'grocery'           => 'بقالة',
            'supplies'          => 'تموينات',
            'markets'           => 'أسواق',
            'station'           => 'محطة',
            default             => $type,
        };
    }

    protected function getAttachmentUrls($attachments)
    {
        $files = is_array($attachments) ? $attachments : json_decode($attachments, true);
        if (!is_array($files)) return [];

        $base = asset('assets/uploads/notes');
        return collect($files)->map(fn($file) => [
            'name' => $file,
            'url'  => "{$base}/{$file}",
            'ext'  => pathinfo($file, PATHINFO_EXTENSION),
        ])->values();
    }
}








