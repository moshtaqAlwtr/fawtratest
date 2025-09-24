<?php

namespace Modules\Api\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TrafficAnalysisCollectionResource extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($branch) {
                return [
                    'id' => $branch['id'],
                    'name' => $branch['name'],
                    'groups' => collect($branch['groups'])->map(function ($group) {
                        return [
                            'id' => $group['id'],
                            'name' => $group['name'],
                            'clients' => collect($group['clients'])->map(function ($client) {
                                return [
                                    'id' => $client['id'],
                                    'name' => $client['name'],
                                    'code' => $client['code'],
                                    'neighborhood' => $client['neighborhood'],
                                    'status' => $client['status'],
                                    'weekly_data' => $client['weekly_data'],
                                ];
                            }),
                        ];
                    }),
                ];
            }),
        ];
    }
}
