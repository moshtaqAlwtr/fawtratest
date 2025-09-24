{{-- resources/views/client/partials/hidden_clients_container.blade.php --}}
<div class="container-fluid mb-4">
    <div class="row">
        <div class="col-12">
            <div class="card border-warning" id="hiddenClientsContainer" style="display: none;">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center"
                     style="cursor: pointer;"
                     data-bs-toggle="collapse"
                     data-bs-target="#hiddenClientsCollapse"
                     aria-expanded="false">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-eye-slash me-2"></i>
                        <strong>العملاء المخفيين</strong>
                        <span class="badge bg-danger ms-2" id="hiddenCountBadge" style="display: none;">0</span>
                    </div>
                    <i class="fas fa-chevron-down" id="toggleIcon"></i>
                </div>

                <div class="collapse" id="hiddenClientsCollapse">
                    <div class="card-body p-2" id="hiddenClientsList">
                        <div class="text-center text-muted py-3" id="emptyMessage">
                            <i class="fas fa-eye fa-2x mb-2"></i>
                            <div>لا يوجد عملاء مخفيين حالياً</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
