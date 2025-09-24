<?php

namespace Modules\Client\Http\Controllers;
use App\Models\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Route;
use Modules\Client\Http\Controllers\AppointmentController;
use Modules\Client\Http\Controllers\AppointmentNoteController;

use Modules\Client\Http\Controllers\CatagroiyClientController;
use Modules\Client\Http\Controllers\ClientController;
use Modules\Client\Http\Controllers\ClientSettingController;
use Modules\Client\Http\Controllers\GroupsController;
use Modules\Client\Http\Controllers\ItineraryController;
use Modules\Client\Http\Controllers\LoyaltyPoints\LoyaltyPointsController;
use Modules\Client\Http\Controllers\LoyaltyPoints\LoyaltyPointsSittingController;

Route::middleware(['auth'])->group(function () {
    // =============================================
    // Routes الرئيسية للعملاء
    // =============================================
    // =============================================
    // Routes إعدادات العميل
    // =============================================
    Route::prefix('client-settings')->group(function () {
        // Personal Settings
        Route::get('/personal', [ClientSettingController::class, 'personal'])->name('clients.personal');
        Route::get('/edit/profile', [ClientSettingController::class, 'profile'])->name('clients.profile');

        // Client Sections
        Route::get('/invoice/client', [ClientSettingController::class, 'invoice_client'])->name('clients.invoice_client');
        Route::get('/appointments/client', [ClientSettingController::class, 'appointments_client'])->name('clients.appointments_client');
        Route::get('/SupplyOrders/client', [ClientSettingController::class, 'SupplyOrders_client'])->name('clients.SupplyOrders_client');
        Route::get('/questions/client', [ClientSettingController::class, 'questions_client'])->name('clients.questions_client');

        // Targets Settings
        Route::get('/employee-targets', [ClientSettingController::class, 'employee_targets'])->name('employee_targets.index');
        Route::get('/sittingsIndex', [ClientSettingController::class, 'index'])->name('clients.setting');
        Route::post('/employee-targets', [ClientSettingController::class, 'storeOrUpdate'])->name('employee_targets.store');
        Route::get('/general-target', [ClientSettingController::class, 'showGeneralTarget'])->name('target.show');
        Route::post('/general-target', [ClientSettingController::class, 'updateGeneralTarget'])->name('target.update');
        Route::get('/client-target', [ClientSettingController::class, 'client_target'])->name('target.client');
        Route::get('/client-target-create', [ClientSettingController::class, 'client_target_create'])->name('target.client.create');
        Route::post('/client-target-create', [ClientSettingController::class, 'client_target_store'])->name('target.client.update');

        // Client Store
        Route::put('/Client/store', [ClientSettingController::class, 'Client_store'])->name('clients.Client_store');
    });

// API Routes for AJAX calls
Route::prefix('api/clients')->middleware('auth')->group(function() {
    Route::get('/data', [ClientApiController::class, 'getClients'])->name('api.clients.data');
    Route::get('/map-data', [ClientApiController::class, 'getMapData'])->name('api.clients.map-data');
    Route::get('/financial-data', [ClientApiController::class, 'getClientFinancialData'])->name('api.clients.financial-data');
    Route::get('/filter-options', [ClientApiController::class, 'getFilterOptions'])->name('api.clients.filter-options');
});

    // =============================================
    // Routes المجموعات والفئات
    // =============================================
    Route::prefix('group')->group(function () {
        Route::get('/group', [GroupsController::class, 'group_client'])->name('groups.group_client');
        Route::get('/group/create', [GroupsController::class, 'group_client_create'])->name('groups.group_client_create');
        Route::post('/group/store', [GroupsController::class, 'group_client_store'])->name('groups.group_client_store');
        Route::get('/group/edit/{id}', [GroupsController::class, 'group_client_edit'])->name('groups.group_client_edit');
        Route::put('/group/update/{id}', [GroupsController::class, 'group_client_update'])->name('groups.group_client_update');
        Route::delete('/group/{id}', [GroupsController::class, 'destroy'])->name('groups.group_client_destroy');
    });

    Route::prefix('categoriesClient')->group(function () {
        Route::get('/categories', [CatagroiyClientController::class, 'index'])->name('categoriesClient.index');
        Route::get('/categories/create', [CatagroiyClientController::class, 'create'])->name('categoriesClient.create');
        Route::post('/categories/store', [CatagroiyClientController::class, 'store'])->name('categoriesClient.store');
        Route::get('/categories/edit/{id}', [CatagroiyClientController::class, 'edit'])->name('categoriesClient.edit');
        Route::put('/categories/update/{id}', [CatagroiyClientController::class, 'update'])->name('categoriesClient.update');
        Route::delete('/categories/delete/{id}', [CatagroiyClientController::class, 'destroy'])->name('categoriesClient.destroy');
    });

    // =============================================
    // Routes المواعيد والملاحظات
    // =============================================
    Route::prefix('appointments')->group(function () {
        Route::resource('appointments', AppointmentController::class)->except(['show']);
        Route::get('/show/{id}', [AppointmentController::class, 'show'])->name('appointments.show');

        // Appointment Status
        Route::post('/update-status', [AppointmentController::class, 'updateStatus'])->name('appointments.update.status');
        Route::patch('/appointments/{id}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.update-status');

        // Appointment Filters
        Route::get('/filter', [AppointmentController::class, 'filterAppointments'])->name('appointments.filter');
        Route::get('/appointments/calendar', [AppointmentController::class, 'calendar'])->name('appointments.calendar');

        // Appointment Details
        Route::get('/appointments/{id}/full-details', [AppointmentController::class, 'getFullAppointmentDetails'])->name('appointments.full-details');
    });

    Route::prefix('appointment-notes')->group(function () {
        Route::get('/', [AppointmentNoteController::class, 'index'])->name('appointment.notes.index');
        Route::get('/create/{id}', [AppointmentNoteController::class, 'create'])->name('appointment.notes.create');
        Route::post('/', [AppointmentNoteController::class, 'store'])->name('appointment.notes.store');
        Route::get('/{note}', [AppointmentNoteController::class, 'show'])->name('appointment.notes.show');
        Route::get('/{note}', [AppointmentNoteController::class, 'edit'])->name('appointment.notes.edit');
        Route::put('/{note}', [AppointmentNoteController::class, 'update'])->name('appointment.notes.update');
        Route::delete('/{note}', [AppointmentNoteController::class, 'destroy'])->name('appointment.notes.destroy');
        Route::get('/{note}/download/{index}', [AppointmentNoteController::class, 'downloadAttachment'])->name('appointment.notes.download');
    });

    // =============================================
    // Routes الجدول الزمني
    // =============================================
    Route::prefix('itinerary')->group(function () {
        Route::get('/', [ItineraryController::class, 'create'])->name('itinerary.create');
        Route::post('/', [ItineraryController::class, 'store'])->name('itinerary.store');
                Route::get('/edit/{id}', [ItineraryController::class, 'edit'])->name('itinerary.edit');
        Route::put('/update/{id}', [ItineraryController::class, 'update'])->name('itinerary.update');
        Route::get('/list', [ItineraryController::class, 'listAll'])->name('itinerary.list');
        Route::delete('/visits/{visit}', [ItineraryController::class, 'destroyVisit'])->name('itinerary.visits.destroy');
    });
    Route::prefix('clients')
        ->middleware(['auth'])
        ->group(function () {
            Route::delete('/itinerary/visits/{visit}', [ItineraryController::class, 'destroyVisit'])->name('itinerary.visits.destroy');
            # Client routes
            Route::prefix('clients_management')->group(function () {
                Route::get('/index', [ClientController::class, 'index'])->name('clients.index');
                Route::get('/mang-client', [ClientController::class, 'mang_client'])->name('clients.management');
                Route::get('/map', [ClientController::class, 'getMapDataWithBranch'])->name(name: 'clients.getMapDataWithBranch');
                Route::get('/search', [ClientController::class, 'search'])->name('clients.search');
                Route::get('/clients/data', [ClientController::class, 'getClientsData'])->name('clients.data');
              Route::get('/clients/ajax/map-data', [ClientController::class, 'getMapData'])->name('clients.getMapData');
     Route::get('/clients/{client}/update-opening-balance', [ClientController::class, 'updateOpeningBalance'])->name('clients.updateOpeningBalance');


     Route::post('/{client}/hide-from-map', [ClientController::class, 'hideFromMap'])->name('clients.hideFromMap');

     Route::post('/{client}/show-in-map', [ClientController::class, 'showInMap'])->name('clients.showInMap');
     Route::get('/hidden-clients', [ClientController::class, 'getHiddenClients'])->name('clients.getHiddenClients');

                Route::get('/{client}/details', [ClientController::class, 'getClientDetails'])->name('clients.details');
                Route::get('/{client}/invoices', [ClientController::class, 'getClientInvoices'])->name('clients.invoices');
                Route::get('/{client}/notes', [ClientController::class, 'getClientNotes'])->name('clients.notes');
                Route::get('/search', [ClientController::class, 'searchClients'])->name('clients.search_clients');
                Route::get('/clients/select', [ClientController::class, 'getClientsForSelect'])->name('clients.getForSelect');

                Route::get('/clients/ajax', [ClientController::class, 'ajaxIndex'])->name('clients.ajax');
                Route::post('clients/update-credit-limit', [ClientController::class, 'updateCreditLimit'])->name('clients.update_credit_limit');

                // Search route
                Route::get('/search', [ClientController::class, 'search'])->name('clients.search');

                // Invoices routes
                Route::get('/{id}/invoices', [ClientController::class, 'clientInvoices'])->name('clients.invoices');

                // Notes routes
                Route::get('/{id}/notes', [ClientController::class, 'clientNotes'])->name('clients.notes');
                Route::post('/{id}/notes', [ClientController::class, 'storeNote'])->name('clients.notes.store');
                Route::delete('/{id}/notes/{noteId}', [ClientController::class, 'deleteNote'])->name('clients.notes.delete');

                Route::get('/testcient', [ClientController::class, 'testcient'])->name('clients.testcient');
                Route::get('/notes/clients', [ClientController::class, 'notes'])->name('clients.notes');
                Route::post('/clients/{id}/update-status', [ClientController::class, 'updateStatus']);

                Route::get('/send_info/{id}', [ClientController::class, 'send_email'])->name('clients.send_info');
                // اعدادات العميل
                Route::get('/setting', [ClientSettingController::class, 'setting'])->name('clients.setting');
                Route::get('/general/settings', [ClientSettingController::class, 'general'])->name('clients.general');
                Route::post('/general/settings', [ClientSettingController::class, 'store'])->name('clients.store_general');
                Route::get('/status/clients', [ClientSettingController::class, 'status'])->name('clients.status');
                Route::post('/status/store', [ClientSettingController::class, 'storeStatus'])->name('clients.status.store');

                Route::post('/update-client-status', [ClientController::class, 'updateStatusClient'])->name('clients.updateStatusClient');

                Route::delete('/status/delete/{id}', [ClientSettingController::class, 'deleteStatus'])->name('clients.status.delete');
                // صلاحيات العميل
                Route::get('/permission/settings', [ClientSettingController::class, 'permission'])->name('clients.permission');
                Route::post('/permission/settings', [ClientSettingController::class, 'permission_store'])->name('clients.store_permission');

                Route::get('/create', [ClientController::class, 'create'])->name('clients.create');
                Route::post('/clients/import', [ClientController::class, 'import'])->name('clients.import');
                Route::get('/mang_client', [ClientController::class, 'mang_client'])->name('clients.mang_client');
                Route::post('/mang_client', [ClientController::class, 'mang_client_store'])->name('clients.mang_client_store');

                Route::post('/addnotes', [ClientController::class, 'addnotes'])->name('clients.addnotes');
                Route::post('/store', [ClientController::class, 'store'])->name('clients.store');
                Route::get('/clients/{client_id}/notes', [ClientController::class, 'getClientNotes']);
                Route::get('/edit/{id}', [ClientController::class, 'edit_question'])->name('clients.edit');
                Route::get('/show/client/{id}', [ClientController::class, 'show'])->name('clients.show');
                Route::get('/statement/{id}', [ClientController::class, 'statement'])->name('clients.statement');
                Route::put('/update/{id}', [ClientController::class, 'update'])->name('clients.update');
                Route::delete('/{id}', [ClientController::class, 'destroy'])->name('clients.destroy');
                Route::post('/delete-multiple', [ClientController::class, 'deleteMultiple'])->name('clients.deleteMultiple');
                Route::get('/contacts', [ClientController::class, 'contacts'])->name('clients.contacts');
                Route::get('/first', [ClientController::class, 'getFirstClient'])->name('clients.first');
                Route::get('/next', [ClientController::class, 'getNextClient'])->name('clients.next');
                Route::get('/previous', [ClientController::class, 'getPreviousClient'])->name('clients.previous');
                Route::post('/{id}/update-opening-balance', [ClientController::class, 'updateOpeningBalance']);

                Route::post('/clients/{client}/force-show', [ClientController::class, 'forceShow'])->name('clients.force-show');
                Route::post('/clients/{client}/assign-employees', [ClientController::class, 'assignEmployees'])->name('clients.assign-employees');
                Route::post('/clients/{client}/remove-employee', [ClientController::class, 'removeEmployee'])->name('clients.remove-employee');
                Route::get('/clients/{client}/assigned-employees', [ClientController::class, 'getAssignedEmployees'])->name('clients.get-assigned-employees');
                Route::get('/clients_management/clients/all', [ClientController::class, 'getAllClients'])->name('clients.all');
                Route::get('/show-contant/{id}', [ClientController::class, 'show_contant'])->name('clients.show_contant');
                // مسار تصدير العملاء إلى Excel
                Route::get('/export', [ClientController::class, 'export'])->name('clients.export');

                Route::get('/clients/search', function (Request $request) {
                    $query = $request->query('query');

                    $clients = Client::with('latestStatus')
                        ->where('trade_name', 'LIKE', "%{$query}%")
                        ->orWhere('first_name', 'LIKE', "%{$query}%")
                        ->orWhere('last_name', 'LIKE', "%{$query}%")
                        ->orWhere('phone', 'LIKE', "%{$query}%")
                        ->orWhere('mobile', 'LIKE', "%{$query}%")
                        ->orWhere('city', 'LIKE', "%{$query}%")
                        ->orWhere('region', 'LIKE', "%{$query}%")
                        ->orWhere('email', 'LIKE', "%{$query}%")
                        ->limit(10)
                        ->get();

                    return response()->json($clients);
                });
                Route::prefix('Loyalty_Points')->group(function () {
                    Route::get('/index', [LoyaltyPointsController::class, 'index'])->name('loyalty_points.index');
                    Route::get('/create', [LoyaltyPointsController::class, 'create'])->name('loyalty_points.create');
                    Route::get('/show/{id}', [LoyaltyPointsController::class, 'show'])->name('loyalty_points.show');
                    Route::post('/store', [LoyaltyPointsController::class, 'store'])->name('loyalty_points.store');
                    Route::get('/edit/{id}', [LoyaltyPointsController::class, 'edit'])->name('loyalty_points.edit');
                    Route::put('/update/{id}', [LoyaltyPointsController::class, 'update'])->name('loyalty_points.update');
                    Route::delete('/destroy/{id}', [LoyaltyPointsController::class, 'destroy'])->name('loyalty_points.destroy');

                    Route::get('/updateStatus/{id}', [LoyaltyPointsController::class, 'updateStatus'])->name('loyalty_points.updateStatus');
                });

                Route::prefix('sittingLoyalty')->group(function () {
                    Route::get('/create', [LoyaltyPointsSittingController::class, 'create'])->name('sittingLoyalty.sitting');
                    Route::post('/store', [LoyaltyPointsSittingController::class, 'store'])->name('sittingLoyalty.store');
                });

                Route::prefix('CourseOfWork')->group(function () {
                    Route::get('/create', [LoyaltyPointsSittingController::class, 'create'])->name('CourseOfWork.sitting');
                    Route::post('/store', [LoyaltyPointsSittingController::class, 'store'])->name('CourseOfWork.store');
                });
            });
        });
});
