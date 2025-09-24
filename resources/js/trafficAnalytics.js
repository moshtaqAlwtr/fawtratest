$(document).ready(function() {
    let isLoading = false;
    let currentWeeksCount = 4;
    const maxWeeks = 52;

    // تحميل البيانات عند تغيير عدد الأسابيع
    $('#weeks-count-selector').change(function() {
        currentWeeksCount = $(this).val();
        loadData();
    });

    // تحميل البيانات عند تغيير المجموعة
    $('#group-filter').change(function() {
        loadData();
    });

    // التحميل عند التمرير لأسفل
    $(window).scroll(function() {
        if($(window).scrollTop() + $(window).height() >= $(document).height() - 200 
           && !isLoading 
           && currentWeeksCount < maxWeeks) {
            currentWeeksCount += 4;
            loadData();
        }
    });

    function loadData() {
        if(isLoading) return;
        
        isLoading = true;
        $('#loading-spinner').show();

        $.ajax({
            url: '{{ route("tracktaff") }}',
            type: 'GET',
            data: {
                weeks_count: currentWeeksCount,
                group_id: $('#group-filter').val(),
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if(response.success) {
                    $('#traffic-table-container').html(response.html);
                    $('#total-clients-count').text(response.total_clients);
                    updateWeekHeaders(response.weeks);
                }
            },
            complete: function() {
                isLoading = false;
                $('#loading-spinner').hide();
            }
        });
    }

    function updateWeekHeaders(weeks) {
        $('.week-header').remove();
        // إعادة بناء عناوين الأسابيع
    }
});