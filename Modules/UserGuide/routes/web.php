<?php

use Illuminate\Support\Facades\Route;
use Modules\UserGuide\Http\Controllers\UserGuideController;
use Modules\UserGuide\Http\Controllers\BusinessAreasController;
use Modules\UserGuide\Http\Controllers\ProgramsController;


Route::get('/register', [UserGuideController::class, 'register'])->name('register');
Route::get('/prices', [UserGuideController::class, 'prices'])->name('prices');
    // عرض صفحة التواصل
Route::get('/contact-us', [UserGuideController::class, 'contactUs'])->name('userguide.contact.us');
// إرسال رسالة التواصل
Route::post('/contact/send', [UserGuideController::class, 'sendContactMessage'])->name('contact.send');
Route::get('/success_partners', [UserGuideController::class, 'successPartners'])->name('userguide.success_partners');
Route::get('/system_functions', [UserGuideController::class, 'systemFunctions'])->name('system.functions');


// توجيهات البرامج
Route::prefix('programs')->group(function () {
    Route::get('/features', [ProgramsController::class, 'features'])->name('programs.features');
    Route::get('/userguides', [ProgramsController::class, 'userguides'])->name('programs.userguides');

    // قسم المبيعات
    Route::get('/sales', [ProgramsController::class, 'sales'])->name('programs.sales');
    Route::get('/invoices-quotes', [ProgramsController::class, 'invoicesQuotes'])->name('programs.invoices.quotes');
    Route::get('/point-of-sale', [ProgramsController::class, 'pointOfSale'])->name('programs.point.of.sale');
    Route::get('/offers', [ProgramsController::class, 'offers'])->name('programs.offers');
    Route::get('/installments', [ProgramsController::class, 'installments'])->name('programs.installments');
    Route::get('/targeted-sales-commissions', [ProgramsController::class, 'targetedSalesCommissions'])->name('programs.targeted.sales.commissions');
    Route::get('/insurance', [ProgramsController::class, 'insurance'])->name('programs.insurance');
    Route::get('/saudi-electronic-invoice', [ProgramsController::class, 'saudiElectronicInvoice'])->name('programs.saudi.electronic.invoice');
    Route::get('/egyptian-electronic-invoice', [ProgramsController::class, 'egyptianElectronicInvoice'])->name('programs.egyptian.electronic.invoice');
    Route::get('/jordanian-electronic-invoice', [ProgramsController::class, 'jordanianElectronicInvoice'])->name('programs.jordanian.electronic.invoice');

    // قسم العملاء
    Route::get('/customer-management', [ProgramsController::class, 'customerManagement'])->name('programs.customer.management');
    Route::get('/customer-follow-up', [ProgramsController::class, 'customerFollowUp'])->name('programs.customer.follow.up');
    Route::get('/customer-loyalty-points', [ProgramsController::class, 'customerLoyaltyPoints'])->name('programs.customer.loyalty.points');
    Route::get('/points-balances', [ProgramsController::class, 'pointsBalances'])->name('programs.points.balances');
    Route::get('/subscriptions-memberships', [ProgramsController::class, 'subscriptionsMemberships'])->name('programs.subscriptions.memberships');

    // قسم الحسابات
    Route::get('/accounts', [ProgramsController::class, 'accounts'])->name('programs.accounts');
    Route::get('/expenses', [ProgramsController::class, 'expenses'])->name('programs.expenses');
    Route::get('/accounting-program', [ProgramsController::class, 'accountingProgram'])->name('programs.accounting.program');
    Route::get('/chart-of-accounts', [ProgramsController::class, 'chartOfAccounts'])->name('programs.chart.of.accounts');
    Route::get('/asset-management', [ProgramsController::class, 'assetManagement'])->name('programs.asset.management');
    Route::get('/cost-centers', [ProgramsController::class, 'costCenters'])->name('programs.cost.centers');
    Route::get('/check-cycle', [ProgramsController::class, 'checkCycle'])->name('programs.check.cycle');

    // قسم المخزون
    Route::get('/inventory-warehouses', [ProgramsController::class, 'inventoryWarehouses'])->name('programs.inventory.warehouses');
    Route::get('/product-management', [ProgramsController::class, 'productManagement'])->name('programs.product.management');
    Route::get('/purchases', [ProgramsController::class, 'purchases'])->name('programs.purchases');
    Route::get('/purchase-cycle', [ProgramsController::class, 'purchaseCycle'])->name('programs.purchase.cycle');
    Route::get('/supplier-management', [ProgramsController::class, 'supplierManagement'])->name('programs.supplier.management');
    Route::get('/inventory-permissions', [ProgramsController::class, 'inventoryPermissions'])->name('programs.inventory.permissions');
    Route::get('/inventory-management', [ProgramsController::class, 'inventoryManagement'])->name('programs.inventory.management');
    Route::get('/manufacturing-management', [ProgramsController::class, 'manufacturingManagement'])->name('programs.manufacturing.management');
    Route::get('/production-order-management', [ProgramsController::class, 'productionOrderManagement'])->name('programs.production.order.management');

    // قسم شؤون الموظفين
    Route::get('/employee-affairs', [ProgramsController::class, 'employeeAffairs'])->name('programs.employee.affairs');
    Route::get('/organizational-structure-management', [ProgramsController::class, 'organizationalStructureManagement'])->name('programs.organizational.structure.management');
    Route::get('/attendance-departure', [ProgramsController::class, 'attendanceDeparture'])->name('programs.attendance.departure');
    Route::get('/contract-management', [ProgramsController::class, 'contractManagement'])->name('programs.contract.management');
    Route::get('/salary-management', [ProgramsController::class, 'salaryManagement'])->name('programs.salary.management');
    Route::get('/request-management', [ProgramsController::class, 'requestManagement'])->name('programs.request.management');

    // قسم التشغيل
    Route::get('/operations', [ProgramsController::class, 'operations'])->name('programs.operations');
    Route::get('/work-cycle', [ProgramsController::class, 'workCycle'])->name('programs.work.cycle');
    Route::get('/work-orders', [ProgramsController::class, 'workOrders'])->name('programs.work.orders');
    Route::get('/reservations', [ProgramsController::class, 'reservations'])->name('programs.reservations');
    Route::get('/rental-unit-management', [ProgramsController::class, 'rentalUnitManagement'])->name('programs.rental.unit.management');
    Route::get('/time-tracking', [ProgramsController::class, 'timeTracking'])->name('programs.time.tracking');

    // قسم تطبيقات الجوال
    Route::get('/mobile-apps', [ProgramsController::class, 'mobileApps'])->name('programs.mobile.apps');
    Route::get('/mobile-business-management', [ProgramsController::class, 'mobileBusinessManagement'])->name('programs.mobile.business.management');
    Route::get('/mobile-pos', [ProgramsController::class, 'mobilePOS'])->name('programs.mobile.pos');
    Route::get('/desktop-pos', [ProgramsController::class, 'desktopPOS'])->name('programs.desktop.pos');
    Route::get('/mobile-attendance', [ProgramsController::class, 'mobileAttendance'])->name('programs.mobile.attendance');
    Route::get('/mobile-expense-tracking', [ProgramsController::class, 'mobileExpenseTracking'])->name('programs.mobile.expense.tracking');
    Route::get('/mobile-invoice-reader', [ProgramsController::class, 'mobileInvoiceReader'])->name('programs.mobile.invoice.reader');

    // توجيهات القائمة الجانبية الإضافية
    Route::get('/fawtura-agents', [ProgramsController::class, 'fawturaAgents'])->name('programs.fawtura.agents');
    Route::get('/account-setup-services', [ProgramsController::class, 'accountSetupServices'])->name('programs.account.setup.services');
    Route::get('/accounting-services', [ProgramsController::class, 'accountingServices'])->name('programs.accounting.services');
    Route::get('/our-clients', [ProgramsController::class, 'ourClients'])->name('programs.our.clients');
    Route::get('/about-fawtura', [ProgramsController::class, 'aboutFawtura'])->name('programs.about.fawtura');
    Route::get('/blog', [ProgramsController::class, 'blog'])->name('programs.blog');
    Route::get('/contact-us-programs', [ProgramsController::class, 'contactUs'])->name('programs.contact.us');
    Route::get('/learning-center', [ProgramsController::class, 'learningCenter'])->name('programs.learning.center');
    Route::get('/latest-updates', [ProgramsController::class, 'latestUpdates'])->name('programs.latest.updates');
});
// توجيه مجالات العمل
Route::prefix('business-areas')->group(function () {
    // المحلات التجارية ونقطة البيع
    Route::get('/retail/hardware-paint', [BusinessAreasController::class, 'hardwarePaint'])->name('business.retail.hardware-paint');
    Route::get('/retail/perfume', [BusinessAreasController::class, 'perfume'])->name('business.retail.perfume');
    Route::get('/retail/mobile', [BusinessAreasController::class, 'mobile'])->name('business.retail.mobile');
    Route::get('/retail/commercial_accounting', [BusinessAreasController::class, 'commercialAccounting'])->name('business.retail.commercial_accounting');
    Route::get('/retail/commercial', [BusinessAreasController::class, 'commercial'])->name('business.retail.commercial');
    Route::get('/retail/ceramic', [BusinessAreasController::class, 'ceramic'])->name('business.retail.ceramic');
    Route::get('/retail/bookstore', [BusinessAreasController::class, 'bookstore'])->name('business.retail.bookstore');
    Route::get('/retail/computer', [BusinessAreasController::class, 'computer'])->name('business.retail.computer');
    Route::get('/retail/auto-parts', [BusinessAreasController::class, 'autoParts'])->name('business.retail.auto-parts');
    Route::get('/retail/jewelry', [BusinessAreasController::class, 'jewelry'])->name('business.retail.jewelry');
    Route::get('/retail/optics', [BusinessAreasController::class, 'optics'])->name('business.retail.optics');

    // الحرف والخدمات المهنية
    Route::get('/hardware/landscape', [BusinessAreasController::class, 'landscape'])->name('business.hardware.landscape');
    Route::get('/hardware/hvac', [BusinessAreasController::class, 'hvac'])->name('business.hardware.hvac');
    Route::get('/hardware/furniture', [BusinessAreasController::class, 'furniture'])->name('business.hardware.furniture');
    Route::get('/hardware/factory', [BusinessAreasController::class, 'factory'])->name('business.hardware.factory');
    Route::get('/hardware/coworking', [BusinessAreasController::class, 'coworking'])->name('business.hardware.coworking');
    Route::get('/hardware/gaming', [BusinessAreasController::class, 'gaming'])->name('business.hardware.gaming');
    Route::get('/hardware/laundry', [BusinessAreasController::class, 'laundry'])->name('business.hardware.laundry');
    Route::get('/hardware/maintenance', [BusinessAreasController::class, 'maintenance'])->name('business.hardware.maintenance');
    Route::get('/hardware/cleaning', [BusinessAreasController::class, 'cleaning'])->name('business.hardware.cleaning');
    Route::get('/hardware/equipment-rental', [BusinessAreasController::class, 'equipmentRental'])->name('business.hardware.equipment-rental');

    // خدمات الأعمال
    Route::get('/business/marketing', [BusinessAreasController::class, 'marketing'])->name('business.business.marketing');
    Route::get('/business/consulting', [BusinessAreasController::class, 'consulting'])->name('business.business.consulting');
    Route::get('/business/hosting-web', [BusinessAreasController::class, 'hostingWeb'])->name('business.business.hosting-web');
    Route::get('/business/accounting', [BusinessAreasController::class, 'accounting'])->name('business.business.accounting');
    Route::get('/business/translation', [BusinessAreasController::class, 'translation'])->name('business.business.translation');
    Route::get('/business/legal', [BusinessAreasController::class, 'legal'])->name('business.business.legal');
    Route::get('/business/insurance', [BusinessAreasController::class, 'insurance'])->name('business.business.insurance');
    Route::get('/business/real-estate', [BusinessAreasController::class, 'realEstate'])->name('business.business.real-estate');
    Route::get('/business/event-planning', [BusinessAreasController::class, 'eventPlanning'])->name('business.business.event-planning');
    Route::get('/business/photography', [BusinessAreasController::class, 'photography'])->name('business.business.photography');

    // الرعاية الطبية
    Route::get('/medical/clinic', [BusinessAreasController::class, 'clinic'])->name('business.medical.clinic');
    Route::get('/medical/dental', [BusinessAreasController::class, 'dental'])->name('business.medical.dental');
    Route::get('/medical/pharmacy', [BusinessAreasController::class, 'pharmacy'])->name('business.medical.pharmacy');
    Route::get('/medical/lab', [BusinessAreasController::class, 'lab'])->name('business.medical.lab');
    Route::get('/medical/veterinary', [BusinessAreasController::class, 'veterinary'])->name('business.medical.veterinary');
    Route::get('/medical/physiotherapy', [BusinessAreasController::class, 'physiotherapy'])->name('business.medical.physiotherapy');
    Route::get('/medical/radiology', [BusinessAreasController::class, 'radiology'])->name('business.medical.radiology');
    Route::get('/medical/hospital', [BusinessAreasController::class, 'hospital'])->name('business.medical.hospital');
    Route::get('/medical/home-care', [BusinessAreasController::class, 'homeCare'])->name('business.medical.home-care');
    Route::get('/medical/mental-health', [BusinessAreasController::class, 'mentalHealth'])->name('business.medical.mental-health');

    // الخدمات اللوجستية
    Route::get('/logistics/shipping', [BusinessAreasController::class, 'shipping'])->name('business.logistics.shipping');
    Route::get('/logistics/delivery', [BusinessAreasController::class, 'delivery'])->name('business.logistics.delivery');
    Route::get('/logistics/warehouse', [BusinessAreasController::class, 'warehouse'])->name('business.logistics.warehouse');
    Route::get('/logistics/customs', [BusinessAreasController::class, 'customs'])->name('business.logistics.customs');
    Route::get('/logistics/freight', [BusinessAreasController::class, 'freight'])->name('business.logistics.freight');
    Route::get('/logistics/courier', [BusinessAreasController::class, 'courier'])->name('business.logistics.courier');
    Route::get('/logistics/supply-chain', [BusinessAreasController::class, 'supplyChain'])->name('business.logistics.supply-chain');
    Route::get('/logistics/moving', [BusinessAreasController::class, 'moving'])->name('business.logistics.moving');
    Route::get('/logistics/cold-storage', [BusinessAreasController::class, 'coldStorage'])->name('business.logistics.cold-storage');
    Route::get('/logistics/fleet', [BusinessAreasController::class, 'fleet'])->name('business.logistics.fleet');

    // السياحة والنقل والضيافة
    Route::get('/hospitality/hotel', [BusinessAreasController::class, 'hotel'])->name('business.hospitality.hotel');
    Route::get('/hospitality/restaurant', [BusinessAreasController::class, 'restaurant'])->name('business.hospitality.restaurant');
    Route::get('/hospitality/travel', [BusinessAreasController::class, 'travel'])->name('business.hospitality.travel');
    Route::get('/hospitality/taxi', [BusinessAreasController::class, 'taxi'])->name('business.hospitality.taxi');
    Route::get('/hospitality/catering', [BusinessAreasController::class, 'catering'])->name('business.hospitality.catering');
    Route::get('/hospitality/resort', [BusinessAreasController::class, 'resort'])->name('business.hospitality.resort');
    Route::get('/hospitality/cafe', [BusinessAreasController::class, 'cafe'])->name('business.hospitality.cafe');
    Route::get('/hospitality/car-rental', [BusinessAreasController::class, 'carRental'])->name('business.hospitality.car-rental');
    Route::get('/hospitality/tour-guide', [BusinessAreasController::class, 'tourGuide'])->name('business.hospitality.tour-guide');
    Route::get('/hospitality/event-venue', [BusinessAreasController::class, 'eventVenue'])->name('business.hospitality.event-venue');

    // العناية بالجسم واللياقة البدنية
    Route::get('/fitness/gym', [BusinessAreasController::class, 'gym'])->name('business.fitness.gym');
    Route::get('/fitness/spa', [BusinessAreasController::class, 'spa'])->name('business.fitness.spa');
    Route::get('/fitness/salon', [BusinessAreasController::class, 'salon'])->name('business.fitness.salon');
    Route::get('/fitness/yoga', [BusinessAreasController::class, 'yoga'])->name('business.fitness.yoga');
    Route::get('/fitness/massage', [BusinessAreasController::class, 'massage'])->name('business.fitness.massage');
    Route::get('/fitness/nutrition', [BusinessAreasController::class, 'nutrition'])->name('business.fitness.nutrition');
    Route::get('/fitness/personal-training', [BusinessAreasController::class, 'personalTraining'])->name('business.fitness.personal-training');
    Route::get('/fitness/swimming', [BusinessAreasController::class, 'swimming'])->name('business.fitness.swimming');
    Route::get('/fitness/martial-arts', [BusinessAreasController::class, 'martialArts'])->name('business.fitness.martial-arts');
    Route::get('/fitness/dance', [BusinessAreasController::class, 'dance'])->name('business.fitness.dance');

    // التعليم
    Route::get('/education/school', [BusinessAreasController::class, 'school'])->name('business.education.school');
    Route::get('/education/university', [BusinessAreasController::class, 'university'])->name('business.education.university');
    Route::get('/education/training', [BusinessAreasController::class, 'training'])->name('business.education.training');
    Route::get('/education/language', [BusinessAreasController::class, 'language'])->name('business.education.language');
    Route::get('/education/nursery', [BusinessAreasController::class, 'nursery'])->name('business.education.nursery');
    Route::get('/education/tutoring', [BusinessAreasController::class, 'tutoring'])->name('business.education.tutoring');
    Route::get('/education/online', [BusinessAreasController::class, 'online'])->name('business.education.online');
    Route::get('/education/vocational', [BusinessAreasController::class, 'vocational'])->name('business.education.vocational');
    Route::get('/education/music', [BusinessAreasController::class, 'music'])->name('business.education.music');
    Route::get('/education/art', [BusinessAreasController::class, 'art'])->name('business.education.art');

    // السيارات
    Route::get('/automotive/dealership', [BusinessAreasController::class, 'dealership'])->name('business.automotive.dealership');
    Route::get('/automotive/repair', [BusinessAreasController::class, 'repair'])->name('business.automotive.repair');
    Route::get('/automotive/parts', [BusinessAreasController::class, 'parts'])->name('business.automotive.parts');
    Route::get('/automotive/rental', [BusinessAreasController::class, 'rental'])->name('business.automotive.rental');
    Route::get('/automotive/insurance', [BusinessAreasController::class, 'autoInsurance'])->name('business.automotive.insurance');
    Route::get('/automotive/wash', [BusinessAreasController::class, 'wash'])->name('business.automotive.wash');
    Route::get('/automotive/towing', [BusinessAreasController::class, 'towing'])->name('business.automotive.towing');
    Route::get('/automotive/driving-school', [BusinessAreasController::class, 'drivingSchool'])->name('business.automotive.driving-school');
    Route::get('/automotive/motorcycle', [BusinessAreasController::class, 'motorcycle'])->name('business.automotive.motorcycle');
    Route::get('/automotive/accessories', [BusinessAreasController::class, 'accessories'])->name('business.automotive.accessories');

    // المشارية والمقاولات والاستثمار العقاري
    Route::get('/construction/general', [BusinessAreasController::class, 'generalConstruction'])->name('business.construction.general');
    Route::get('/construction/electrical', [BusinessAreasController::class, 'electrical'])->name('business.construction.electrical');
    Route::get('/construction/plumbing', [BusinessAreasController::class, 'plumbing'])->name('business.construction.plumbing');
    Route::get('/construction/architecture', [BusinessAreasController::class, 'architecture'])->name('business.construction.architecture');
    Route::get('/construction/interior', [BusinessAreasController::class, 'interior'])->name('business.construction.interior');
    Route::get('/construction/property-management', [BusinessAreasController::class, 'propertyManagement'])->name('business.construction.property-management');
    Route::get('/construction/real-estate-sales', [BusinessAreasController::class, 'realEstateSales'])->name('business.construction.real-estate-sales');
    Route::get('/construction/project-management', [BusinessAreasController::class, 'projectManagement'])->name('business.construction.project-management');
    Route::get('/construction/surveying', [BusinessAreasController::class, 'surveying'])->name('business.construction.surveying');
    Route::get('/construction/demolition', [BusinessAreasController::class, 'demolition'])->name('business.construction.demolition');
});

Route::prefix('guides')->middleware(['auth'])->group(function () {
    Route::get('/userguides', [UserGuideController::class,'index'])->name('userguide');
    Route::get('/payment', [UserGuideController::class,'paymentUser'])->name('paymentUser');
    Route::get('/my-company', [UserGuideController::class,'myCompany'])->name('myCompany');
    Route::get('/registered-referrals', [UserGuideController::class,'referrals'])->name('referrals');
    Route::get('/account-statement', [UserGuideController::class,'accountStatement'])->name('accountStatement');
    Route::get('/activate-coupon', [UserGuideController::class,'activateCouponPage'])->name('activateCouponPage');
    Route::post('/activate-coupon', [UserGuideController::class,'activateCoupon'])->name('activateCoupon');
    Route::get('/change-email', [UserGuideController::class, 'changeEmailPage'])->name('changeEmailPage');
    Route::post('/change-email', [UserGuideController::class, 'changeEmail'])->name('changeEmail');
    Route::get('/change-password', [UserGuideController::class, 'changePassword'])->name('changePassword');
    Route::get('/payment-settings', [UserGuideController::class, 'paymentSettings'])->name('payment.settings');
    Route::post('/payment-settings/update', [UserGuideController::class, 'updateSettings'])->name('payment.settings.update');
    Route::post('/payment-card/store', [UserGuideController::class, 'storeCard'])->name('payment.card.store');
    Route::delete('/payment-card/{id}', [UserGuideController::class, 'deleteCard'])->name('payment.card.delete');
// تعيين بطاقة كافتراضية
    Route::post('/payment-card/{id}/set-default', [UserGuideController::class, 'setDefault'])->name('payment.card.set-default');

});
