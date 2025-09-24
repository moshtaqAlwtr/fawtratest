<?php

namespace Modules\Api\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Api\Http\Resources\ClientResource;
use Modules\Api\Http\Resources\ClientFullResource;
use Modules\Api\Http\Resources\ClientContactsResource;
use Modules\Api\Services\ClientFilterService;  
use Modules\Api\Services\ClientDistanceService;
use Modules\Api\Services\ClientAnalyticsService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Traits\ApiResponseTrait;
use Carbon\Carbon;
use App\Models\Client;
use App\Models\Account;
use App\Models\Booking;
use App\Models\Neighborhood;
use App\Models\Employee;
use App\Models\Branch;
use App\Models\ChartOfAccount;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use App\Models\Region_groub;
use App\Models\ClientEmployee;
use App\Models\EmployeeClientVisit;
use App\Models\CategoriesClient;
use App\Models\ClientRelation;
use App\Models\Expense;
use App\Models\GeneralClientSetting;
use App\Models\Installment;
use App\Models\SerialSetting;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\Log as ModelsLog;
use App\Models\Memberships;
use App\Models\Package;
use App\Models\Revenue;
use App\Models\Statuses;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Modules\Client\Http\Requests\ClientRequestApi;
use Modules\Client\Http\Requests\ClientRequest;
use Modules\Client\Http\Requests\ClientLocationResource;
use Illuminate\Support\Facades\Schema;

use Modules\Client\Http\Requests\ApiUpdateOpeningBalanceRequest;

class ClientController extends Controller
{
    use ApiResponseTrait;

// public function index(Request $request)
// {
//     try {
//         $user = auth()->user();
//         if (! $user) {
//             return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
//         }

//         $clients = ClientFilterService::apply($request, $user);
//         $clientsWithDistance = ClientDistanceService::append($clients, $user);
       

//         $page = (int) $request->input('page', 1);
//         $perPage = (int) $request->input('per_page', 20);
//         $perPage = $perPage > 0 && $perPage <= 100 ? $perPage : 20;

//         $pageItems = $clientsWithDistance->forPage($page, $perPage)->values();

//         $pagedClients = new LengthAwarePaginator(
//             $pageItems,
//             $clientsWithDistance->count(),
//             $perPage,
//             $page,
//             ['path' => $request->url(), 'query' => $request->query()]
//         );

//         $data = ClientResource::collection($pagedClients)->resolve();

//         return response()->json([
//             'success'        => true,
//             'message'        => 'تم جلب العملاء',
//             'data'           => $data,
//             'current_page'   => $pagedClients->currentPage(),
//             'per_page_count' => $pagedClients->count(),
//             'total_count'    => $pagedClients->total(),
//             'next_page_url'  => $pagedClients->nextPageUrl(),
//         ]);

//     } catch (\Throwable $e) {
//         return response()->json([
//             'success' => false,
//             'message' => 'فشل في جلب البيانات',
//         ], 500);
//     }
// }


public function index(Request $request)
{
    try {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        /** @var \Illuminate\Database\Eloquent\Builder $q */
        $q = ClientFilterService::apply($request, $user);

        // أعمدة أساسية
        $select = [
            'clients.id','clients.code','clients.trade_name',
            'clients.phone','clients.email',
            'clients.status_id','clients.branch_id',
            'clients.created_at',
        ];

        // أعمدة اختيارية (تضاف فقط إذا موجودة فعلاً)
        foreach (['tax_number','visit_type','type'] as $col) {
            if (Schema::hasColumn('clients', $col)) {
                $select[] = "clients.$col";
            }
        }

        $q->select($select)
          ->with([
              // طابق أسماء علاقاتك كما تستخدمها في Resource
              'account_client:id,client_id,balance',
              'status_client:id,name,color',
              'branch:id,name',
              'neighborhood:id,name,region_id',
              'neighborhood.region:id,name',
              'locations:id,client_id,latitude,longitude',
          ]);

        // حساب المسافة داخل SQL (اختياري)
        $lat = $request->float('lat');
        $lng = $request->float('lng');
        if ($lat && $lng) {
            // لو لكل عميل أكثر من موقع، خذ أقرب مسافة (MIN)
            $distanceExpr = "
                6371 * acos(least(1,
                    cos(radians($lat)) * cos(radians(L.latitude)) *
                    cos(radians(L.longitude) - radians($lng)) +
                    sin(radians($lat)) * sin(radians(L.latitude))
                ))
            ";

            $q->leftJoin('locations as L', 'L.client_id', '=', 'clients.id')
              ->addSelect(DB::raw("MIN($distanceExpr) as distance_km"))
              ->groupBy($select); // جميع أعمدة clients المختارة
        } else {
            $q->addSelect(DB::raw('NULL as distance_km'));
        }

        // Pagination من الـDB
        $perPage = (int) $request->input('per_page', 20);
        $perPage = ($perPage > 0 && $perPage <= 100) ? $perPage : 20;

        $paged = $q->paginate($perPage)->appends($request->query());

        // Resource
        $data = ClientResource::collection($paged)->response()->getData(true);

        return response()->json([
            'success'        => true,
            'message'        => 'تم جلب العملاء',
            'data'           => $data['data'],
            'current_page'   => $data['meta']['current_page'],
            'per_page_count' => count($data['data']),
            'total_count'    => $data['meta']['total'],
            'next_page_url'  => $data['links']['next'] ?? null,
        ]);

    } catch (\Throwable $e) {
        \Log::error('clients.index failed', [
            'msg'  => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);

        return response()->json([
            'success' => false,
            'message' => (config('app.debug') ? $e->getMessage() : 'فشل في جلب البيانات'),
        ], 500);
    }
}

// public function map(Request $request)
// {
//     try {
//         $user = auth()->user();
//         if (! $user) {
//             return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
//         }

//         // 1) فلترة + اختيار أعمدة + eager loading انتقائي
//         $clientsQuery = ClientFilterService::apply($request, $user);

//         $clientsQuery->select(['id','code','trade_name','phone','status_id','branch_id','neighborhood_id']);
//         $clientsQuery->with([
//             'accountClient:id,client_id,balance',
//             'statusClient:id,name,color',
//             'branch:id,name',
//             'neighborhood:id,name,region_id',
//             'neighborhood.region:id,name',
//             'locations:id,client_id,latitude,longitude',
//         ]);

//         $clients = $clientsQuery->get();

//         // 2) أضف المسافة (بدون استعلامات إضافية لكل عنصر)
//         $clientsWithDistance = ClientDistanceService::append($clients, $user);

//         // 3) حوّل لبيانات خفيفة (Resource)
//         // فلتر العناصر التي لا تملك إحداثيات لتقليل حجم الرد للخريطة
//         $clientsWithDistance = $clientsWithDistance->filter(function ($c) {
//             $loc = $c->locations;
//             $loc = $loc instanceof \Illuminate\Support\Collection ? $loc->first() : $loc;
//             return $loc && $loc->latitude && $loc->longitude;
//         })->values();

//         $data = ClientLocationResource::collection($clientsWithDistance)->resolve();

//         return response()->json([
//             'success'     => true,
//             'message'     => 'تم جلب العملاء',
//             'data'        => $data,
//             'total_count' => $clientsWithDistance->count(),
//         ]);

//     } catch (\Throwable $e) {
//         \Log::error('map() failed', [
//             'msg'  => $e->getMessage(),
//             'file' => $e->getFile(),
//             'line' => $e->getLine(),
//         ]);

//         return response()->json([
//             'success' => false,
//             'message' => 'فشل في جلب البيانات',
//         ], 500);
//     }
// }


// public function map(Request $request)
// {
//     try {
//         $user = auth()->user();
//         if (! $user) {
//             return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
//         }

//         $base = ClientFilterService::apply($request, $user);

//         // مصفوفة الناتج النهائية
//         $out = [];
//         $total = 0;

//         // دالة تحويل عنصر واحد إلى مصفوفة خفيفة (بدون N+1)
//         $transform = function ($c) {
//             // locations قد تكون hasOne أو hasMany
//             $loc = $c->locations;
//             if ($loc instanceof \Illuminate\Support\Collection) {
//                 $loc = $loc->first(); // عدّلها للأحدث إذا تبغى
//             }

//             if (! $loc || !$loc->latitude || !$loc->longitude) {
//                 return null; // أسقط العملاء بلا إحداثيات
//             }

//             return [
//                 'id'           => $c->id,
//                 'code'         => $c->code,
//                 'name'         => $c->trade_name,
//                 'phone'        => $c->phone,

//                 'balance'      => optional($c->accountClient)->balance ?? 0,
//                 'status'       => optional($c->statusClient)->name,
//                 'status_color' => optional($c->statusClient)->color,
//                 'branch'       => optional($c->branch)->name,
//                 'region'       => optional(optional($c->neighborhood)->region)->name,
//                 'neighborhood' => optional($c->neighborhood)->name,

//                 'latitude'     => (float) $loc->latitude,
//                 'longitude'    => (float) $loc->longitude,

//                 // إن كانت خدمة المسافة تضيف خاصية distance
//                 'distance_km'  => isset($c->distance) ? (float) $c->distance : null,
//             ];
//         };

//         // حالة: الـ Service رجع Query Builder
//         if ($base instanceof Builder) {
//             $query = $base->select([
//                     'id','code','trade_name','phone',
//                     'status_id','branch_id','neighborhood_id'
//                 ])
//                 ->with([
//                     'accountClient:id,client_id,balance',
//                     'statusClient:id,name,color',
//                     'branch:id,name',
//                     'neighborhood:id,name,region_id',
//                     'neighborhood.region:id,name',
//                     'locations:id,client_id,latitude,longitude',
//                 ])
//                 ->orderBy('id'); // مطلوب لـ chunkById

//             // نعالج على دفعات لتقليل الذاكرة
//             $query->chunkById(1000, function (Collection $chunk) use (&$out, &$total, $user, $transform) {
//                 // أضف المسافة لهذه الدفعة فقط
//                 $withDistance = ClientDistanceService::append($chunk, $user);

//                 foreach ($withDistance as $c) {
//                     $row = $transform($c);
//                     if ($row !== null) {
//                         $out[] = $row;
//                         $total++;
//                     }
//                 }
//             });

//         } else {
//             // حالة: الـ Service رجّع Collection
//             /** @var \Illuminate\Support\Collection $collection */
//             $collection = $base instanceof Collection ? $base : collect($base);

//             // حمّل العلاقات انتقائيًا دفعة واحدة
//             $collection->load([
//                 'accountClient:id,client_id,balance',
//                 'statusClient:id,name,color',
//                 'branch:id,name',
//                 'neighborhood:id,name,region_id',
//                 'neighborhood.region:id,name',
//                 'locations:id,client_id,latitude,longitude',
//             ]);

//             // أضف المسافة لكل العناصر (تأكد أن الخدمة لا تفتح استعلامات لكل عنصر)
//             $withDistance = ClientDistanceService::append($collection, $user);

//             // عالج على تشunks داخل الذاكرة لتقليل الذروة
//             foreach ($withDistance->chunk(1000) as $chunk) {
//                 foreach ($chunk as $c) {
//                     $row = $transform($c);
//                     if ($row !== null) {
//                         $out[] = $row;
//                         $total++;
//                     }
//                 }
//             }
//         }

//         return response()->json([
//             'success'     => true,
//             'message'     => 'تم جلب العملاء',
//             'data'        => $out,       // كل النقاط للخريطة
//             'total_count' => $total,
//         ]);

//     } catch (\Throwable $e) {
//         Log::error('map() failed', [
//             'msg'  => $e->getMessage(),
//             'file' => $e->getFile(),
//             'line' => $e->getLine(),
//         ]);

//         return response()->json([
//             'success' => false,
//             'message' => 'فشل في جلب البيانات',
//         ], 500);
//     }
// }

// public function map(Request $request)
// {
//     try {
//         $user = auth()->user();
//         if (! $user) {
//             return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
//         }

//         // 1) فلترة الأساس (قد ترجع Builder أو Collection)
//         $base = ClientFilterService::apply($request, $user);

//         // مصفوفة الناتج النهائية + عدّاد
//         $out   = [];
//         $total = 0;

//         // محوّل عنصر واحد إلى صف خفيف للخريطة (بدون N+1)
//         $transform = function ($c) {
//             // locations قد تكون hasOne أو Collection
//             $loc = $c->locations;
//             if ($loc instanceof \Illuminate\Support\Collection) {
//                 $loc = $loc->first(); // خذ أول موقع (عدّلها للأحدث عند الحاجة)
//             }

//             if (! $loc || !$loc->latitude || !$loc->longitude) {
//                 return null; // تجاهل العملاء بلا إحداثيات
//             }

//             return [
//                 'id'           => $c->id,
//                 'code'         => $c->code,
//                 'name'         => $c->trade_name,
//                 'phone'        => $c->phone,

//                 'balance'      => optional($c->accountClient)->balance ?? 0,
//                 'status'       => optional($c->statusClient)->name,
//                 'status_color' => optional($c->statusClient)->color,
//                 'branch'       => optional($c->branch)->name,
//                 'region'       => optional(optional($c->neighborhood)->region)->name,
//                 'neighborhood' => optional($c->neighborhood)->name,

//                 'latitude'     => (float) $loc->latitude,
//                 'longitude'    => (float) $loc->longitude,

//                 'distance_km'  => isset($c->distance) ? (float) $c->distance : null,
//             ];
//         };

//         // 2) معالجة حسب نوع المخرجات من خدمة الفلترة
//         if ($base instanceof Builder) {
//             // ‼️ مهم: eager loading بأعمدة محددة + ترتيب للـ chunkById
//             $query = $base
//                 ->select(['id','code','trade_name','phone','status_id','branch_id','neighborhood_id'])
//                 ->with([
//                     'accountClient:id,client_id,balance',
//                     'statusClient:id,name,color',
//                     'branch:id,name',
//                     'neighborhood:id,name,region_id',
//                     'neighborhood.region:id,name',
//                     'locations:id,client_id,latitude,longitude',
//                 ])
//                 ->orderBy('id'); // مطلوب للـ chunkById

//             // التعامل على دفعات لتقليل الذاكرة
//             $query->chunkById(1000, function (Collection $chunk) use (&$out, &$total, $user, $transform) {
//                 // أضف المسافة لهذه الدفعة فقط (تأكد أن الخدمة لا تفتح استعلام لكل عنصر)
//                 $withDistance = ClientDistanceService::append($chunk, $user);

//                 foreach ($withDistance as $c) {
//                     $row = $transform($c);
//                     if ($row !== null) {
//                         $out[] = $row;
//                         $total++;
//                     }
//                 }
//             });

//         } else {
//             // Collection
//             $collection = $base instanceof Collection ? $base : collect($base);

//             // eager loading لكل العناصر دفعة واحدة (أعمدة محددة)
//             $collection->load([
//                 'accountClient:id,client_id,balance',
//                 'statusClient:id,name,color',
//                 'branch:id,name',
//                 'neighborhood:id,name,region_id',
//                 'neighborhood.region:id,name',
//                 'locations:id,client_id,latitude,longitude',
//             ]);

//             // أضف المسافة
//             $withDistance = ClientDistanceService::append($collection, $user);

//             // عالج على تشُنكات داخل الذاكرة لتقليل الذروة
//             foreach ($withDistance->chunk(1000) as $chunk) {
//                 foreach ($chunk as $c) {
//                     $row = $transform($c);
//                     if ($row !== null) {
//                         $out[] = $row;
//                         $total++;
//                     }
//                 }
//             }
//         }

//         return response()->json([
//             'success'     => true,
//             'message'     => 'تم جلب العملاء',
//             'data'        => $out,   // كل النقاط للخريطة
//             'total_count' => $total,
//         ]);

//     } catch (\Throwable $e) {
//         // اطبع التفاصيل في اللوج
//         Log::error('map() failed', [
//             'msg'  => $e->getMessage(),
//             'file' => $e->getFile(),
//             'line' => $e->getLine(),
//             // سهل تتبّع مشاكل الـ query الكبيرة
//             'trace_top' => collect($e->getTrace())->take(3)->all(),
//         ]);

//         // أثناء التطوير، رجّع الخطأ الحقيقي (لو APP_DEBUG=true)
//         $message = config('app.debug') ? $e->getMessage() : 'فشل في جلب البيانات';

//         return response()->json([
//             'success' => false,
//             'message' => $message,
//         ], 500);
//     }
// }

// public function map(Request $request)
// {
//     try {
//         $user = auth()->user();
//         if (!$user) {
//             return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
//         }

//         // يمكن يرجّع Builder أو Collection حسب خدمتك
//         $base = ClientFilterService::apply($request, $user);

//         // محوّل عنصر واحد إلى صف خفيف للخريطة
//         $transform = function ($c) {
//             // locations قد تكون hasOne أو hasMany (Collection)
//             $loc = $c->locations ?? null;
//             if ($loc instanceof \Illuminate\Support\Collection) {
//                 $loc = $loc->first(); // خذ أول موقع (عدّلها للأحدث لو تحتاج)
//             }

//             if (!$loc || !$loc->latitude || !$loc->longitude) {
//                 return null; // تجاهل العملاء بلا إحداثيات
//             }

//             return [
//                 'id'           => $c->id,
//                 'code'         => $c->code,
//                 'name'         => $c->trade_name,
//                 'phone'        => $c->phone,

//                 // علاقاتك بالـ snake_case
//                 'balance'      => optional($c->account_client)->balance ?? 0,
//                 'status'       => optional($c->status_client)->name,
//                 'status_color' => optional($c->status_client)->color,
//                 'branch'       => optional($c->branch)->name,
//                 'region'       => optional(optional($c->neighborhood)->region)->name,
//                 'neighborhood' => optional($c->neighborhood)->name,

//                 'latitude'     => (float) $loc->latitude,
//                 'longitude'    => (float) $loc->longitude,

//                 'distance_km'  => isset($c->distance) ? (float) $c->distance : null,
//             ];
//         };

//         $out   = [];
//         $total = 0;

//         if ($base instanceof Builder) {
//             // Builder: eager loading بأسماء العلاقات الصحيحة (snake_case) + أعمدة محددة
//             $query = $base->select([
//                     'id','code','trade_name','phone',
//                     'status_id','branch_id','neighborhood_id'
//                 ])
//                 ->with([
//                     // أسماء العلاقات كما في Resource تبعك
//                     'account_client:id,client_id,balance',
//                     'status_client:id,name,color',
//                     'branch:id,name',
//                     'neighborhood:id,name,region_id',
//                     'neighborhood.region:id,name',
//                     'locations:id,client_id,latitude,longitude',
//                 ])
//                 ->orderBy('id'); // لضمان chunkById

//             // عالج على دفعات لتقليل استهلاك الذاكرة
//             $query->chunkById(1000, function (Collection $chunk) use (&$out, &$total, $user, $transform) {
//                 // أضف المسافة لهذه الدفعة فقط (لا تفتح استعلام لكل عنصر)
//                 $withDistance = ClientDistanceService::append($chunk, $user);

//                 foreach ($withDistance as $c) {
//                     $row = $transform($c);
//                     if ($row !== null) {
//                         $out[] = $row;
//                         $total++;
//                     }
//                 }
//             });

//         } else {
//             // Collection: حمّل العلاقات دفعة واحدة بأسماء snake_case
//             $collection = $base instanceof Collection ? $base : collect($base);

//             $collection->load([
//                 'account_client:id,client_id,balance',
//                 'status_client:id,name,color',
//                 'branch:id,name',
//                 'neighborhood:id,name,region_id',
//                 'neighborhood.region:id,name',
//                 'locations:id,client_id,latitude,longitude',
//             ]);

//             $withDistance = ClientDistanceService::append($collection, $user);

//             foreach ($withDistance->chunk(1000) as $chunk) {
//                 foreach ($chunk as $c) {
//                     $row = $transform($c);
//                     if ($row !== null) {
//                         $out[] = $row;
//                         $total++;
//                     }
//                 }
//             }
//         }

//         return response()->json([
//             'success'     => true,
//             'message'     => 'تم جلب العملاء',
//             'data'        => $out,
//             'total_count' => $total,
//         ]);

//     } catch (\Throwable $e) {
//         Log::error('map() failed', [
//             'msg'  => $e->getMessage(),
//             'file' => $e->getFile(),
//             'line' => $e->getLine(),
//         ]);

//         // لو APP_DEBUG=true راح يظهر الخطأ الحقيقي في الرسالة
//         return response()->json([
//             'success' => false,
//             'message' => config('app.debug') ? $e->getMessage() : 'فشل في جلب البيانات',
//         ], 500);
//     }
// }


public function map(Request $request)
{
    try {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $base = ClientFilterService::apply($request, $user);

        $transform = function ($c) {
            $loc = $c->locations ?? null;
            if ($loc instanceof \Illuminate\Support\Collection) {
                $loc = $loc->first();
            }
            if (!$loc || !$loc->latitude || !$loc->longitude) {
                return null; // تجاهل بدون إحداثيات
            }

            return [
                'id'           => $c->id,
                'code'         => $c->code,
                'name'         => $c->trade_name,
                'phone'        => $c->phone,

                'balance'      => optional($c->account_client)->balance ?? 0,
                'status'       => optional($c->status_client)->name,
                'status_color' => optional($c->status_client)->color,
                'branch'       => optional($c->branch)->name,
                'region'       => optional(optional($c->Neighborhood)->Region)->name,
                'neighborhood' => optional($c->Neighborhood)->name,

                'latitude'     => (float) $loc->latitude,
                'longitude'    => (float) $loc->longitude,

                'distance_km'  => isset($c->distance) ? (float) $c->distance : null,
            ];
        };

        $out = [];
        $total = 0;

        if ($base instanceof Builder) {
            // ابني قائمة الأعمدة ديناميكيًا حسب وجودها
            $select = [
                'clients.id',
                'clients.code',
                'clients.trade_name',
                'clients.phone',
            ];
            foreach (['status_id', 'branch_id', 'neighborhood_id'] as $col) {
                if (Schema::hasColumn('clients', $col)) {
                    $select[] = "clients.$col";
                }
            }

            $query = $base->select($select)
                ->with([
                    'account_client:id,client_id,balance',
                    'status_client:id,name,color',
                    'branch:id,name',
                    'neighborhood:id,name,region_id',
                    'neighborhood.region:id,name',
                    'locations:id,client_id,latitude,longitude',
                ])
                ->orderBy('clients.id'); // مهم لـ chunkById

            $query->chunkById(1000, function (Collection $chunk) use (&$out, &$total, $user, $transform) {
                $withDistance = ClientDistanceService::append($chunk, $user);

                foreach ($withDistance as $c) {
                    $row = $transform($c);
                    if ($row !== null) {
                        $out[] = $row;
                        $total++;
                    }
                }
            });

        } else {
            $collection = $base instanceof Collection ? $base : collect($base);

            $collection->load([
                'account_client:id,client_id,balance',
                'status_client:id,name,color',
                'branch:id,name',
                'neighborhood:id,name,region_id',
                'neighborhood.region:id,name',
                'locations:id,client_id,latitude,longitude',
            ]);

            $withDistance = ClientDistanceService::append($collection, $user);

            foreach ($withDistance->chunk(1000) as $chunk) {
                foreach ($chunk as $c) {
                    $row = $transform($c);
                    if ($row !== null) {
                        $out[] = $row;
                        $total++;
                    }
                }
            }
        }

        return response()->json([
            'success'     => true,
            'message'     => 'تم جلب العملاء',
            'data'        => $out,
            'total_count' => $total,
        ]);

    } catch (\Throwable $e) {
        Log::error('map() failed', [
            'msg'  => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);

        return response()->json([
            'success' => false,
            'message' => (config('app.debug') ? $e->getMessage() : 'فشل في جلب البيانات'),
        ], 500);
    }
}

public function test()
{
    $client = Client::all();
    
        return response()->json([
            'success'        => true,
            'message'        => 'تم جلب العملاء',
            'data'           => $client,
            
        ]);
}

    // جلب بيانات الاضافة
    public function createData()
    {

        try {
            $data = [
                'employees' => Employee::select('id', 'first_name')->get(),
                // 'categories' => CategoriesClient::select('id', 'name')->get(),
                'regions' => Region_groub::select('id', 'name')->get(),
                'branches' => Branch::select('id', 'name')->get()
            ];

            return $this->successResponse($data, 'تم جلب بيانات شاشة الإضافة');
        } catch (\Exception $e) {
            return $this->errorResponse('حدث خطأ أثناء جلب البيانات', 500, $e->getMessage());
        }
    }



    /**
     * Show the form for creating a new resource.
     */
    public function store(ClientRequestApi $request)
    {
        try {
            $data_request = $request->except('_token');

            Validator::make($data_request, [
                'region_id' => ['required']
            ], [
                'region_id.required' => 'حقل المجموعة مطلوب.'
            ])->validate();

            if (!$request->has('latitude') || !$request->has('longitude')) {
                return response()->json(['success' => false, 'message' => 'الإحداثيات غير موجودة'], 400);
            }

            DB::beginTransaction();

            $latitude = $request->latitude;
            $longitude = $request->longitude;

            $client = new Client();
            $client->status_id = 3;

            $serialSetting = SerialSetting::where('section', 'customer')->first();
            $currentNumber = $serialSetting ? $serialSetting->current_number : 1;
            
            $lastClient = Client::orderBy('code', 'desc')->first();

            $newCode = $lastClient ? $lastClient->code + 1 : 3000;
            $client->code = $newCode;
            $client->fill($data_request);

            if ($request->hasFile('attachments')) {
                $file = $request->file('attachments');
                if ($file->isValid()) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('assets/uploads/'), $filename);
                    $client->attachments = $filename;
                }
            }

            $client->save();

            $client->contacts()->create([
                'first_name' => $client->trade_name,
                'phone' => $client->phone,
                'mobile' => $client->mobile,
                'email' => $client->email,
                'is_primary' => true
            ]);

            $employeeIds = [];
           $user = auth()->user(); // يرجع كائن المستخدم الحالي



            if ($user && $user->role === 'manager' && $request->has('employee_client_id')) {
                foreach ($request->employee_client_id as $employee_id) {
                    ClientEmployee::create([
                        'client_id' => $client->id,
                        'employee_id' => $employee_id
                    ]);
                    $employeeIds[] = $employee_id;
                }
            } elseif ($user && $user->role === 'employee') {
                $employeeId = $user->employee_id;
                ClientEmployee::create([
                    'client_id' => $client->id,
                    'employee_id' => $employeeId,
                    ($employeeId = auth()->id()),
                ]);
                $employeeIds[] = $employeeId;
            }

            $client->locations()->create([
                'latitude' => $latitude,
                'longitude' => $longitude
            ]);

            $neighborhoodName = $this->getNeighborhoodFromGoogle($latitude, $longitude);
            Neighborhood::create([
                'name' => $neighborhoodName ?? 'غير محدد',
                'region_id' => $request->region_id,
                'client_id' => $client->id
            ]);

            if ($request->email) {
                User::create([
                    'name' => $client->trade_name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'role' => 'client',
                    'client_id' => $client->id,
                    'password' => Hash::make(Str::random(10))
                ]);
            }

            ModelsLog::create([
                'type' => 'client',
                'type_id' => $client->id,
                'type_log' => 'log',
                'description' => 'تم إضافة عميل **' . $client->trade_name . '**',
                'created_by' => auth()->id()
            ]);

            if ($serialSetting) {
                $serialSetting->update(['current_number' => $currentNumber + 1]);
            }

            $customers = Account::where('name', 'العملاء')->first();
            if ($customers) {
                $customerAccount = new Account();
                $customerAccount->name = $client->trade_name;
                $customerAccount->client_id = $client->id;
                $customerAccount->balance += $client->opening_balance ?? 0;

                $lastChild = Account::where('parent_id', $customers->id)->orderBy('code', 'desc')->first();
                $newCode = $lastChild ? $this->generateNextCode($lastChild->code) : $customers->code . '1';

                while (Account::where('code', $newCode)->exists()) {
                    $newCode = $this->generateNextCode($newCode);
                }

                $customerAccount->code = $newCode;
                $customerAccount->balance_type = 'debit';
                $customerAccount->parent_id = $customers->id;
                $customerAccount->is_active = false;
                $customerAccount->save();

                if ($client->opening_balance > 0) {
                    $journalEntry = JournalEntry::create([
                        'reference_number' => $client->code,
                        'date' => now(),
                        'description' => 'رصيد افتتاحي للعميل : ' . $client->trade_name,
                        'status' => 1,
                        'currency' => 'SAR',
                        'client_id' => $client->id
                    ]);

                    JournalEntryDetail::create([
                        'journal_entry_id' => $journalEntry->id,
                        'account_id' => $customerAccount->id,
                        'description' => 'رصيد افتتاحي للعميل : ' . $client->trade_name,
                        'debit' => $client->opening_balance ?? 0,
                        'credit' => 0,
                        'is_debit' => true
                    ]);
                }
            }

            if ($request->has('contacts') && is_array($request->contacts)) {
                foreach ($request->contacts as $contact) {
                    $client->contacts()->create($contact);
                }
            }

            $this->assignClientVisits($client->id, $employeeIds);

            DB::commit();

            return $this->successResponse(
                new ClientResource($client),
                'تم إضافة العميل بنجاح'
            );
        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorResponse(
                'حدث خطأ أثناء حفظ العميل',
                500,
                $e->getMessage() // تقدر تشيلها إذا ما تبغى ترجع الخطأ
            );
        }
    }

    // private function generateNextCode($parentId)
    // {
    //     $parentAccount = ChartOfAccount::findOrFail($parentId);
    //     $lastChild = ChartOfAccount::where('parent_id', $parentId)->orderBy(DB::raw('CAST(SUBSTRING_INDEX(code, ".", -1) AS UNSIGNED)'), 'desc')->first();

    //     if (!$lastChild) {
    //         return $parentAccount->code . '.1';
    //     }

    //     $lastNumber = intval(substr(strrchr($lastChild->code, '.'), 1));
    //     return $parentAccount->code . '.' . ($lastNumber + 1);
    // }
    //عرض بيانات التعديل
    public function showBasic($id)
    {
        $client = Client::with([
            'contacts:id,client_id,first_name,last_name,email,phone,mobile',
            'employees:id,first_name',
            'branch:id,name',
            'locations:id,client_id,latitude,longitude',
            'group:id,name',

        ])->findOrFail($id);

        $employees = Employee::select('id', 'first_name')->get();
        $branches = Branch::select('id', 'name')->get();
        $regions = Region_groub::select('id', 'name')->get();

        return $this->successResponse([
            'client' => new ClientResource($client),
        
        ], 'تم جلب بيانات العميل');
    }

 public function showFull($id)
{
    try {
        $user = auth()->user();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // جلب العميل مع العلاقات
        $client = Client::with([
            'invoices' => fn($q) => $q->where('type', '!=', 'returned')
                ->whereNotIn('id', function ($subQuery) {
                    $subQuery->select('reference_number')
                        ->from('invoices')
                        ->whereNotNull('reference_number');
                })
                ->orderBy('created_at', 'desc'),
            'invoices.payments',
            'payments' => fn($q) => $q->orderBy('created_at', 'desc'),
            'appointments' => fn($q) => $q->latest(),
            'appointmentNotes' => fn($q) => $q->latest(),
            'visits.employee' => fn($q) => $q->latest(),
            'employee',
            'account',
        ])->findOrFail($id);

        // إضافة بيانات المسافة والتحليلات مثل index
        $clientWithDistance = ClientDistanceService::append(collect([$client]), $user);
        $analytics = ClientAnalyticsService::summarize($clientWithDistance);
        $client->analytics = $analytics[$client->id] ?? [];

        // بيانات إضافية
        $employees = Employee::all();
        $statuses  = Statuses::all();

        return response()->json([
            'success'  => true,
            'message'  => 'تم جلب بيانات العميل بالكامل',
            'data'     => [
                'client'    => new ClientFullResource($client),
                // 'analytics' => $client->analytics,
            ],
            'employees' => $employees,
            'statuses'  => $statuses,
        ]);

    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'فشل في جلب البيانات',
            'error'   => $e->getMessage()
        ], 500);
    }
}

    private function getTreasury($id)
    {
        return Account::findOrFail($id);
    }

    private function getBranches()
    {
        return Branch::all();
    }




    private function getNeighborhoodFromGoogle($latitude, $longitude)
    {
        $apiKey = env('GOOGLE_MAPS_API_KEY');
        $url = "https://maps.googleapis.com/maps/api/geocode/json";

        $response = Http::withoutVerifying()->get($url, [
            'latlng' => "$latitude,$longitude",
            'key' => $apiKey,
            'language' => 'ar',
        ]);


        if ($response->successful()) {
            $data = $response->json();
            if (!empty($data['results'][0]['address_components'])) {
                foreach ($data['results'][0]['address_components'] as $component) {
                    if (in_array('sublocality', $component['types']) || in_array('neighborhood', $component['types'])) {
                        return $component['long_name'];
                    }
                }
            }
        }

        return 'غير محدد';
    }


    private function generateNextCode($code)
    {
        return (string)((int)$code + 1);
    }

    private function assignClientVisits($clientId, $employeeIds)
    {
        $now = now();
        $currentDate = $now->copy();
        $currentYear = $now->year;

        $firstSaturday = Carbon::createFromDate($currentYear, 1, 1)->startOfWeek(Carbon::SATURDAY);
        if (Carbon::createFromDate($currentYear, 1, 1)->dayOfWeek === Carbon::SATURDAY) {
            $firstSaturday = Carbon::createFromDate($currentYear, 1, 1);
        }

        $daysDiff = $firstSaturday->diffInDays($currentDate);
        $currentWeek = (int) floor($daysDiff / 7) + 1;

        $adjustedDayOfWeek = ($now->dayOfWeek + 1) % 7;
        $englishDays = ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $dayOfWeek = strtolower($englishDays[$adjustedDayOfWeek]);

        foreach ($employeeIds as $employeeId) {
            EmployeeClientVisit::updateOrCreate([
                'employee_id' => $employeeId,
                'client_id' => $clientId,
                'day_of_week' => $dayOfWeek,
                'year' => $currentYear,
                'week_number' => $currentWeek,
                'status' => 'active'
            ], [
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }
    }

    public function update(ClientRequest $request, $id)
    {
        $rules = [
            'region_id' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ];

        $messages = [
            'region_id.required' => 'حقل المجموعة مطلوب.',
            'latitude.required' => 'العميل ليس لديه موقع مسجل الرجاء تحديد الموقع على الخريطة',
            'longitude.required' => 'العميل ليس لديه موقع مسجل الرجاء تحديد الموقع على الخريطة',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'يرجى التحقق من البيانات المدخلة'
            ], 422);
        }

        // بدء المعاملة لضمان سلامة البيانات
        DB::beginTransaction();

        try {
            $data_request = $request->except('_token', 'contacts');
            $client = Client::findOrFail($id);
            $oldData = $client->getOriginal();

            $latitude = $request->latitude ?? $client->latitude;
            $longitude = $request->longitude ?? $client->longitude;

            $data_request = $request->except('_token', 'contacts', 'latitude', 'longitude');

            // حذف الموظفين السابقين فقط إذا كان المستخدم مدير
            if (auth()->user()->role === 'manager') {
                ClientEmployee::where('client_id', $client->id)->delete();

                if ($request->has('employee_client_id')) {
                    foreach ($request->employee_client_id as $employee_id) {
                        ClientEmployee::create([
                            'client_id' => $client->id,
                            'employee_id' => $employee_id,
                        ]);
                    }
                }
            } elseif (auth()->user()->role === 'employee') {
                $user = auth()->user();
                $employee_id = $user?->employee_id ?? 7; // مؤقتًا للتجربة

                // التحقق إذا هو أصلاً مسؤول
                $alreadyExists = ClientEmployee::where('client_id', $client->id)->where('employee_id', $employee_id)->exists();

                if (!$alreadyExists) {
                    ClientEmployee::create([
                        'client_id' => $client->id,
                        'employee_id' => $employee_id,
                    ]);
                }
            }

            // 1. معالجة المرفقات
            if ($request->hasFile('attachments')) {
                $file = $request->file('attachments');
                if ($file->isValid()) {
                    // حذف الملف القديم إن وجد
                    if ($client->attachments) {
                        $oldFilePath = public_path('assets/uploads/') . $client->attachments;
                        if (File::exists($oldFilePath)) {
                            File::delete($oldFilePath);
                        }
                    }

                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('assets/uploads/'), $filename);
                    $data_request['attachments'] = $filename;
                }
            }

            // 2. تحديث بيانات العميل الأساسية
            $client->update($data_request);

            // تحديث اسم الحساب إذا تغير الاسم التجاري
            if ($client->wasChanged('trade_name')) {
                Account::where('client_id', $client->id)->update(['name' => $client->trade_name]);
            }

            // 3. معالجة الإحداثيات - الطريقة المؤكدة
            $client->locations()->delete(); // حذف جميع المواقع القديمة

            $client->locations()->create([
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'client_id' => $client->id,
            ]);

            $neighborhoodName = $this->getNeighborhoodFromGoogle($request->latitude, $request->longitude);

            // البحث عن الحي الحالي للعميل
            $Neighborhood = Neighborhood::where('client_id', $client->id)->first();

            if ($Neighborhood) {
                // إذا كان لديه حي، قم بتحديثه
                $Neighborhood->name = $neighborhoodName ?? 'غير محدد';
                $Neighborhood->region_id = $request->region_id;
                $Neighborhood->save();
            } else {
                // إذا لم يكن لديه حي، أضف حيًا جديدًا
                $Neighborhood = new Neighborhood();
                $Neighborhood->name = $neighborhoodName ?? 'غير محدد';
                $Neighborhood->region_id = $request->region_id;
                $Neighborhood->client_id = $client->id;
                $Neighborhood->save();
            }

            // 4. تحديث بيانات المستخدم
            if ($request->email) {
                $full_name = implode(' ', array_filter([$client->trade_name, $client->first_name, $client->last_name]));

                $userData = [
                    'name' => $full_name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                ];

                $user = User::where('client_id', $client->id)->first();

                if ($user) {
                    $user->update($userData);
                } else {
                    $userData['password'] = Hash::make(Str::random(10));
                    $userData['role'] = 'client';
                    $userData['client_id'] = $client->id;
                    User::create($userData);
                }
            }

            // 6. معالجة جهات الاتصال
            if ($request->has('contacts')) {
                $existingContacts = $client->contacts->keyBy('id');
                $newContacts = collect($request->contacts);

                // الحذف
                $contactsToDelete = $existingContacts->diffKeys($newContacts->whereNotNull('id')->keyBy('id'));
                $client->contacts()->whereIn('id', $contactsToDelete->keys())->delete();

                // التحديث والإضافة
                foreach ($request->contacts as $contact) {
                    if (isset($contact['id']) && $existingContacts->has($contact['id'])) {
                        $existingContacts[$contact['id']]->update($contact);
                    } else {
                        $client->contacts()->create($contact);
                    }
                }
            }

            // 7. تسجيل العملية في السجل
            ModelsLog::create([
                'type' => 'client',
                'type_id' => $client->id,
                'type_log' => 'update',
                'description' => 'تم تحديث بيانات العميل: ' . $client->trade_name,
                'created_by' => auth()->id(),
                'old_data' => json_encode($oldData),
                'new_data' => json_encode($client->getAttributes()),
            ]);

            DB::commit();
            return $this->successResponse(new ClientResource($client), 'تم تحديث بيانات العميل بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse('حدث خطأ أثناء التحديث', 500, $e->getMessage());
        }
    }

    private function getTransactions($id)
    {
        return JournalEntryDetail::where('account_id', $id)
            ->with([
                'journalEntry' => function ($query) {
                    $query->with('invoice', 'client');
                },
            ])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    private function getTransfers($id)
    {
        return JournalEntry::whereHas('details', function ($query) use ($id) {
            $query->where('account_id', $id);
        })
            ->with(['details.account'])
            ->where('description', 'تحويل المالية')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    private function getExpenses($id)
    {
        return Expense::where('treasury_id', $id)
            ->with(['expenses_category', 'vendor', 'employee', 'branch', 'client'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    private function getRevenues($id)
    {
        return Revenue::where('treasury_id', $id)
            ->with(['account', 'paymentVoucher', 'treasury', 'bankAccount', 'journalEntry'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    private function processOperations($transactions, $transfers, $expenses, $revenues, $treasury)
    {
        $currentBalance = 0;
        $allOperations = [];

        // معالجة المدفوعات
        foreach ($transactions as $transaction) {
            $amount = $transaction->debit > 0 ? $transaction->debit : $transaction->credit;
            $type = $transaction->debit > 0 ? 'إيداع' : 'سحب';

            $currentBalance = $this->updateBalance($currentBalance, $amount, $type);

            $allOperations[] = [
                'operation' => $transaction->description,
                'deposit' => $type === 'إيداع' ? $amount : 0,
                'withdraw' => $type === 'سحب' ? $amount : 0,
                'balance_after' => $currentBalance,

                'journalEntry' => $transaction->journalEntry->id,
                'date' => $transaction->journalEntry->date,
                'invoice' => $transaction->journalEntry->invoice,
                'client' => $transaction->journalEntry->client,
                'type' => 'transaction',
            ];
        }

        // معالجة التحويلات
        // foreach ($transfers as $transfer) {
        //     $amount = $transfer->details->sum('debit');
        //     $fromAccount = $transfer->details->firstWhere('is_debit', true)->account;
        //     $toAccount = $transfer->details->firstWhere('is_debit', false)->account;

        //     if ($fromAccount->id == $treasury->id) {
        //         $currentBalance -= $amount;
        //         $operationText = 'تحويل مالي إلى ' . $toAccount->name;
        //     } else {
        //         $currentBalance += $amount;
        //         $operationText = 'تحويل مالي من ' . $fromAccount->name;
        //     }

        //     $allOperations[] = [
        //         'operation' => $operationText,
        //         'deposit' => $fromAccount->id != $treasury->id ? $amount : 0,
        //         'withdraw' => $fromAccount->id == $treasury->id ? $amount : 0,
        //         'balance_after' => $currentBalance,
        //         'date' => $transfer->date,
        //         'invoice' => null,
        //         'client' => null,
        //         'type' => 'transfer',
        //     ];
        // }

        // معالجة سندات الصرف
        foreach ($expenses as $expense) {
            $currentBalance -= $expense->amount;

            $allOperations[] = [
                'operation' => 'سند صرف: ' . $expense->description,
                'deposit' => 0,
                'withdraw' => $expense->amount,
                'balance_after' => $currentBalance,
                'date' => $expense->date,
                'invoice' => null,
                'client' => $expense->client,
                'type' => 'expense',
            ];
        }

        // معالجة سندات القبض
        foreach ($revenues as $revenue) {
            $currentBalance += $revenue->amount;

            $allOperations[] = [
                'operation' => 'سند قبض: ' . $revenue->description,
                'deposit' => $revenue->amount,
                'withdraw' => 0,
                'balance_after' => $currentBalance,
                'date' => $revenue->date,
                'invoice' => null,
                'client' => null,
                'type' => 'revenue',
            ];
        }

        return $allOperations;
    }

    private function updateBalance($currentBalance, $amount, $type)
    {
        return $type === 'إيداع' ? $currentBalance + $amount : $currentBalance - $amount;
    }

    private function paginateOperations($allOperations)
    {
        $perPage = 15;
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedOperations = array_slice($allOperations, $offset, $perPage);

        return new \Illuminate\Pagination\LengthAwarePaginator($paginatedOperations, count($allOperations), $perPage, $currentPage, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);
    }
    
public function contacts(Request $request)
{
    $query = Client::query()->with(['employee', 'status']);

    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->where(function ($q) use ($search) {
            $q->where('trade_name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%")
                ->orWhere('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('mobile', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhereHas('employee', fn($q) => $q->where('trade_name', 'like', "%{$search}%"))
                ->orWhereHas('status', fn($q) => $q->where('name', 'like', "%{$search}%"));
        });
    }

    // فلترة متقدمة
    if ($request->filled('phone')) $query->where('phone', 'like', "%{$request->phone}%");
    if ($request->filled('mobile')) $query->where('mobile', 'like', "%{$request->mobile}%");
    if ($request->filled('email')) $query->where('email', 'like', "%{$request->email}%");
    if ($request->filled('employee_id')) $query->where('employee_id', $request->employee_id);
    if ($request->filled('status_id')) $query->where('status_id', $request->status_id);
    if ($request->filled('city')) $query->where('city', 'like', "%{$request->city}%");
    if ($request->filled('region')) $query->where('region', 'like', "%{$request->region}%");

    $clients = $query->paginate(25)->withQueryString();

    return $this->paginatedResponse(ClientContactsResource::collection($clients), 'تم جلب جهات الاتصال');
}



public function updateOpeningBalance(Request $request, $id)
{
    
    try {
        $request->validate([
    'opening_balance' => 'required|numeric|min:0',
]);

        $client = Client::findOrFail($id);
        $openingBalance = $request->opening_balance;

        $client->opening_balance = $openingBalance;
        $client->save();

        $account = Account::where('client_id', $id)->first();
        if ($account) {
            $account->balance += $openingBalance;
            $account->save();
        }

        if ($openingBalance > 0) {
            $journalEntry = JournalEntry::create([
                'reference_number' => $client->code,
                'date' => now(),
                'description' => 'رصيد افتتاحي للعميل : ' . $client->trade_name,
                'status' => 1,
                'currency' => 'SAR',
                'client_id' => $client->id,
            ]);

            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $account->id,
                'description' => 'رصيد افتتاحي للعميل : ' . $client->trade_name,
                'debit' => $openingBalance,
                'credit' => 0,
                'is_debit' => true,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الرصيد الافتتاحي بنجاح',
            'opening_balance' => $client->opening_balance,
            'account_balance' => $account->balance ?? null,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'فشل في تحديث الرصيد الافتتاحي',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('api::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('api::edit');
    }

    /**
     * Update the specified resource in storage.
     */


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}
