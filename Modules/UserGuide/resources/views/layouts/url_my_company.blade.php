  <div class="widget">
      <div class="widget-header widget-header-wrapper d-flex justify-content-between align-items-center">
          <h5 class="heading mb-0">
              <i class="fas fa-link ms-2"></i> رابط الشراكة الخاص بك
          </h5>
      </div>

      <div class="widget-content p-4 rtl-text">
          <h4 class="text-dark fw-bold mb-3">اخبر أصدقائك عن <span class="text-primary">فوتره</span></h4>

          <p class="text-muted" style="font-size: 15px; line-height: 1.9;">
              انسخ رابط الشراكة الخاص بك وشاركه مع أصدقائك واحصل على
              <strong class="text-dark">100% عمولة</strong> من أول شهر من مبيعاته و
              <strong class="text-dark">10%</strong> من مصاريف التجديد مدى الحياة.
          </p>

          <hr class="my-4">

          <label class="form-label fw-bold mb-2" style="font-size: 15px;">رابط الإحالة الخاص بك:</label>
          <div class="input-group flex-row-reverse">
              <input type="text" id="referralLink" class="form-control fw-bold text-end" readonly
                  value="https://www.fawtra.com/?ref_id=3230757" style="direction: ltr;">
              <button class="color-btn btn btn-primary" style=" padding:revert"
                  onclick="copyReferralLink()">نسخ</button>
          </div>
      </div>
  </div>


  @push('scripts')
      <script>
          function copyReferralLink() {
              const input = document.getElementById("referralLink");
              input.select();
              input.setSelectionRange(0, 99999);
              document.execCommand("copy");
              alert("تم نسخ الرابط بنجاح!");
          }
      </script>
  @endpush
