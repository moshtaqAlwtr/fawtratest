
{{-- resources/views/client/partials/search_card.blade.php --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center p-2">
        <div class="d-flex gap-2">
            <span class="hide-button-text">بحث وتصفية</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-secondary btn-sm" onclick="toggleSearchFields(this)">
                <i class="fa fa-times"></i>
                <span class="hide-button-text">إخفاء</span>
            </button>
            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse"
                data-bs-target="#advancedSearchForm" onclick="toggleSearchText(this)">
                <i class="fa fa-filter"></i>
                <span class="button-text">متقدم</span>
            </button>
        </div>
    </div>
    <div class="card-body">
        <form class="form" id="searchForm">
            <div class="card p-3 mb-4">
                <div class="row g-3 align-items-end">
                    <!-- اسم العميل -->
                    <div class="col-md-4 col-12">
                        <label for="client" class="form-label">العميل</label>
                        <select name="client" id="client" class="form-control select2">
                            <option value="">اختر العميل</option>
                            @foreach ($allClients as $client)
                                <option value="{{ $client->id }}" {{ request('client') == $client->id ? 'selected' : '' }}>
                                    {{ $client->trade_name }} - {{ $client->code }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- الاسم التجاري -->
                    <div class="col-md-4 col-12">
                        <label for="name" class="form-label">الاسم التجاري</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="الاسم التجاري" value="{{ request('name') }}">
                    </div>

                    <!-- الحالة -->
                    <div class="col-md-4 col-12">
                        <label for="status" class="form-label">الحالة</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">اختر الحالة</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->id }}" {{ request('status') == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- المجموعة -->
                    <div class="col-md-4 col-12">
                        <label for="region" class="form-label">المجموعة</label>
                        <select name="region" id="region" class="form-control select2">
                            <option value="">اختر المجموعة</option>
                            @foreach ($Region_groups as $Region_group)
                                <option value="{{ $Region_group->id }}" {{ request('region') == $Region_group->id ? 'selected' : '' }}>{{ $Region_group->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- الحي -->
                    <div class="col-md-4 col-12">
                        <label for="neighborhood" class="form-label">الحي</label>
                        <input type="text" name="neighborhood" id="neighborhood" class="form-control" placeholder="الحي" value="{{ request('neighborhood') }}">
                    </div>

                    <!-- تاريخ من -->
                    <div class="col-md-4 col-12">
                        <label for="date_from" class="form-label">تاريخ من</label>
                        <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>

                    <!-- تاريخ الى -->
                    <div class="col-md-4 col-12">
                        <label for="date_to" class="form-label">تاريخ الى</label>
                        <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                </div>
            </div>

            <div class="collapse" id="advancedSearchForm">
                <div class="row g-3 mt-2">
                    <!-- التصنيف -->
                    <div class="col-md-4 col-12">
                        <label for="classifications" class="form-label">التصنيف</label>
                        <select name="categories" id="classifications" class="form-control">
                            <option value="">اختر التصنيف</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ request('categories') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- أضيفت بواسطة -->
                    <div class="col-md-4 col-12">
                        <label for="user" class="form-label">أضيفت بواسطة</label>
                        <select name="user" id="user" class="form-control select2">
                            <option value="">أضيفت بواسطة</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ request('user') == $user->id ? 'selected' : '' }}>{{ $user->name }} - {{ $user->id }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- الموظفين المعيين -->
                    <div class="col-md-4 col-12">
                        <label for="employee" class="form-label">الموظفين المعيين</label>
                        <select id="employee" class="form-control select2" name="employee">
                            <option value="">اختر الموظف</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" {{ request('employee') == $employee->id ? 'selected' : '' }}>{{ $employee->full_name }} - {{ $employee->id }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-actions mt-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-1"></i>
                    بحث
                </button>
                <button type="button" class="btn btn-outline-warning" id="resetFilters">
                    <i class="fas fa-undo me-1"></i>
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>
