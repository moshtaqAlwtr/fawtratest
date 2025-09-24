<?php

namespace Modules\UserGuide\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BusinessAreasController extends Controller
{
    // المحلات التجارية ونقطة البيع
    public function hardwarePaint() {
        return view('userguide::home_pages', ['page' => 'hardware_paint']);
    }

    public function perfume() {
        return view('userguide::home_pages', ['page' => 'perfume']);
    }

    public function mobile() {
        return view('userguide::home_pages', ['page' => 'mobile']);
    }

    public function commercial() {
        return view('userguide::home_pages', ['page' => 'commercial']);
    }
    public function commercialAccounting() {
        return view('userguide::home_pages', ['page' => 'commercial_accounting']);
    }

    public function ceramic() {
        return view('userguide::home_pages', ['page' => 'ceramic']);
    }

    public function bookstore() {
        return view('userguide::home_pages', ['page' => 'bookstore']);
    }

    public function computer() {
        return view('userguide::home_pages', ['page' => 'computer']);
    }

    public function autoParts() {
        return view('userguide::home_pages', ['page' => 'auto_parts']);
    }

    public function jewelry() {
        return view('userguide::home_pages', ['page' => 'jewelry']);
    }

    public function optics() {
        return view('userguide::home_pages', ['page' => 'optics']);
    }

    // الحرف والخدمات المهنية
    public function landscape() {
        return view('userguide::home_pages', ['page' => 'landscape']);
    }

    public function hvac() {
        return view('userguide::home_pages', ['page' => 'hvac']);
    }

    public function furniture() {
        return view('userguide::home_pages', ['page' => 'furniture']);
    }

    public function factory() {
        return view('userguide::home_pages', ['page' => 'factory']);
    }

    public function coworking() {
        return view('userguide::home_pages', ['page' => 'coworking']);
    }

    public function gaming() {
        return view('userguide::home_pages', ['page' => 'gaming']);
    }

    public function laundry() {
        return view('userguide::home_pages', ['page' => 'laundry']);
    }

    public function maintenance() {
        return view('userguide::home_pages', ['page' => 'maintenance']);
    }

    public function cleaning() {
        return view('userguide::home_pages', ['page' => 'cleaning']);
    }

    public function equipmentRental() {
        return view('userguide::home_pages', ['page' => 'equipment_rental']);
    }

    // خدمات الأعمال
    public function marketing() {
        return view('userguide::home_pages', ['page' => 'marketing']);
    }

    public function consulting() {
        return view('userguide::home_pages', ['page' => 'consulting']);
    }

    public function hostingWeb() {
        return view('userguide::home_pages', ['page' => 'hosting_web']);
    }

    public function accounting() {
        return view('userguide::home_pages', ['page' => 'accounting']);
    }

    public function translation() {
        return view('userguide::home_pages', ['page' => 'translation']);
    }

    public function legal() {
        return view('userguide::home_pages', ['page' => 'legal']);
    }

    public function insurance() {
        return view('userguide::home_pages', ['page' => 'insurance']);
    }

    public function realEstate() {
        return view('userguide::home_pages', ['page' => 'real_estate']);
    }

    public function eventPlanning() {
        return view('userguide::home_pages', ['page' => 'event_planning']);
    }

    public function photography() {
        return view('userguide::home_pages', ['page' => 'photography']);
    }

    // الرعاية الطبية
    public function clinic() {
        return view('userguide::home_pages', ['page' => 'clinic']);
    }

    public function dental() {
        return view('userguide::home_pages', ['page' => 'dental']);
    }

    public function pharmacy() {
        return view('userguide::home_pages', ['page' => 'pharmacy']);
    }

    public function lab() {
        return view('userguide::home_pages', ['page' => 'lab']);
    }

    public function veterinary() {
        return view('userguide::home_pages', ['page' => 'veterinary']);
    }

    public function physiotherapy() {
        return view('userguide::home_pages', ['page' => 'physiotherapy']);
    }

    public function radiology() {
        return view('userguide::home_pages', ['page' => 'radiology']);
    }

    public function hospital() {
        return view('userguide::home_pages', ['page' => 'hospital']);
    }

    public function homeCare() {
        return view('userguide::home_pages', ['page' => 'home_care']);
    }

    public function mentalHealth() {
        return view('userguide::home_pages', ['page' => 'mental_health']);
    }

    // الخدمات اللوجستية
    public function shipping() {
        return view('userguide::home_pages', ['page' => 'shipping']);
    }

    public function delivery() {
        return view('userguide::home_pages', ['page' => 'delivery']);
    }

    public function warehouse() {
        return view('userguide::home_pages', ['page' => 'warehouse']);
    }

    public function customs() {
        return view('userguide::home_pages', ['page' => 'customs']);
    }

    public function freight() {
        return view('userguide::home_pages', ['page' => 'freight']);
    }

    public function courier() {
        return view('userguide::home_pages', ['page' => 'courier']);
    }

    public function supplyChain() {
        return view('userguide::home_pages', ['page' => 'supply_chain']);
    }

    public function moving() {
        return view('userguide::home_pages', ['page' => 'moving']);
    }

    public function coldStorage() {
        return view('userguide::home_pages', ['page' => 'cold_storage']);
    }

    public function fleet() {
        return view('userguide::home_pages', ['page' => 'fleet']);
    }

    // السياحة والنقل والضيافة
    public function hotel() {
        return view('userguide::home_pages', ['page' => 'hotel']);
    }

    public function restaurant() {
        return view('userguide::home_pages', ['page' => 'restaurant']);
    }

    public function travel() {
        return view('userguide::home_pages', ['page' => 'travel']);
    }

    public function taxi() {
        return view('userguide::home_pages', ['page' => 'taxi']);
    }

    public function catering() {
        return view('userguide::home_pages', ['page' => 'catering']);
    }

    public function resort() {
        return view('userguide::home_pages', ['page' => 'resort']);
    }

    public function cafe() {
        return view('userguide::home_pages', ['page' => 'cafe']);
    }

    public function carRental() {
        return view('userguide::home_pages', ['page' => 'car_rental']);
    }

    public function tourGuide() {
        return view('userguide::home_pages', ['page' => 'tour_guide']);
    }

    public function eventVenue() {
        return view('userguide::home_pages', ['page' => 'event_venue']);
    }

    // العناية بالجسم واللياقة البدنية
    public function gym() {
        return view('userguide::home_pages', ['page' => 'gym']);
    }

    public function spa() {
        return view('userguide::home_pages', ['page' => 'spa']);
    }

    public function salon() {
        return view('userguide::home_pages', ['page' => 'salon']);
    }

    public function yoga() {
        return view('userguide::home_pages', ['page' => 'yoga']);
    }

    public function massage() {
        return view('userguide::home_pages', ['page' => 'massage']);
    }

    public function nutrition() {
        return view('userguide::home_pages', ['page' => 'nutrition']);
    }

    public function personalTraining() {
        return view('userguide::home_pages', ['page' => 'personal_training']);
    }

    public function swimming() {
        return view('userguide::home_pages', ['page' => 'swimming']);
    }

    public function martialArts() {
        return view('userguide::home_pages', ['page' => 'martial_arts']);
    }

    public function dance() {
        return view('userguide::home_pages', ['page' => 'dance']);
    }

    // التعليم
    public function school() {
        return view('userguide::home_pages', ['page' => 'school']);
    }

    public function university() {
        return view('userguide::home_pages', ['page' => 'university']);
    }

    public function training() {
        return view('userguide::home_pages', ['page' => 'training']);
    }

    public function language() {
        return view('userguide::home_pages', ['page' => 'language']);
    }

    public function nursery() {
        return view('userguide::home_pages', ['page' => 'nursery']);
    }

    public function tutoring() {
        return view('userguide::home_pages', ['page' => 'tutoring']);
    }

    public function online() {
        return view('userguide::home_pages', ['page' => 'online']);
    }

    public function vocational() {
        return view('userguide::home_pages', ['page' => 'vocational']);
    }

    public function music() {
        return view('userguide::home_pages', ['page' => 'music']);
    }

    public function art() {
        return view('userguide::home_pages', ['page' => 'art']);
    }

    // السيارات
    public function dealership() {
        return view('userguide::home_pages', ['page' => 'dealership']);
    }

    public function repair() {
        return view('userguide::home_pages', ['page' => 'repair']);
    }

    public function parts() {
        return view('userguide::home_pages', ['page' => 'parts']);
    }

    public function rental() {
        return view('userguide::home_pages', ['page' => 'rental']);
    }

    public function autoInsurance() {
        return view('userguide::home_pages', ['page' => 'auto_insurance']);
    }

    public function wash() {
        return view('userguide::home_pages', ['page' => 'wash']);
    }

    public function towing() {
        return view('userguide::home_pages', ['page' => 'towing']);
    }

    public function drivingSchool() {
        return view('userguide::home_pages', ['page' => 'driving_school']);
    }

    public function motorcycle() {
        return view('userguide::home_pages', ['page' => 'motorcycle']);
    }

    public function accessories() {
        return view('userguide::home_pages', ['page' => 'accessories']);
    }

    // المشارية والمقاولات والاستثمار العقاري
    public function generalConstruction() {
        return view('userguide::home_pages', ['page' => 'general_construction']);
    }

    public function electrical() {
        return view('userguide::home_pages', ['page' => 'electrical']);
    }

    public function plumbing() {
        return view('userguide::home_pages', ['page' => 'plumbing']);
    }

    public function architecture() {
        return view('userguide::home_pages', ['page' => 'architecture']);
    }

    public function interior() {
        return view('userguide::home_pages', ['page' => 'interior']);
    }

    public function propertyManagement() {
        return view('userguide::home_pages', ['page' => 'property_management']);
    }

    public function realEstateSales() {
        return view('userguide::home_pages', ['page' => 'real_estate_sales']);
    }

    public function projectManagement() {
        return view('userguide::home_pages', ['page' => 'project_management']);
    }

    public function surveying() {
        return view('userguide::home_pages', ['page' => 'surveying']);
    }

    public function demolition() {
        return view('userguide::home_pages', ['page' => 'demolition']);
    }
}
