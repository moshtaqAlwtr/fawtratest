document.addEventListener('DOMContentLoaded', function() {
    // Hide statistical analysis by default
    const statisticalAnalysis = document.getElementById('statisticalAnalysis');
    statisticalAnalysis.style.display = 'none';

    // View Toggle Functionality
    const summaryViewBtn = document.getElementById('summaryViewBtn');
    const detailViewBtn = document.getElementById('detailViewBtn');
    const mainReportTable = document.getElementById('mainReportTable');

    summaryViewBtn.addEventListener('click', function() {
        summaryViewBtn.classList.add('active');
        detailViewBtn.classList.remove('active');
        mainReportTable.style.display = 'none';
        statisticalAnalysis.style.display = 'block';
    });

    detailViewBtn.addEventListener('click', function() {
        detailViewBtn.classList.add('active');
        summaryViewBtn.classList.remove('active');
        mainReportTable.style.display = 'block';
        statisticalAnalysis.style.display = 'none';
    });

    // Print Functionality
    document.getElementById('printBtn').addEventListener('click', function() {
        window.print();
    });

    // Report Type Update Function
    window.updateReportType = function(type) {
        var form = document.getElementById('reportForm');
        var fromDateInput = form.querySelector('input[name="from_date"]');
        var toDateInput = form.querySelector('input[name="to_date"]');
        var today = new Date();

        // Remove any existing report type inputs
        var existingReportTypeInputs = form.querySelectorAll('input[name="report_type"]');
        existingReportTypeInputs.forEach(input => input.remove());

        // Create hidden input for report type
        var hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'report_type';
        hiddenInput.value = type;
        form.appendChild(hiddenInput);

        // Update date inputs based on report type
        switch(type) {
            case 'yearly':
                fromDateInput.value = (today.getFullYear() + '-01-01');
                toDateInput.value = (today.getFullYear() + '-12-31');
                break;
            case 'monthly':
                fromDateInput.value = today.getFullYear() + '-' +
                    String(today.getMonth() + 1).padStart(2, '0') + '-01';
                toDateInput.value = today.getFullYear() + '-' +
                    String(today.getMonth() + 1).padStart(2, '0') + '-' +
                    new Date(today.getFullYear(), today.getMonth() + 1, 0).getDate();
                break;
            case 'weekly':
                let firstDay = new Date(today.setDate(today.getDate() - today.getDay()));
                let lastDay = new Date(today.setDate(today.getDate() - today.getDay() + 6));
                fromDateInput.value = firstDay.getFullYear() + '-' +
                    String(firstDay.getMonth() + 1).padStart(2, '0') + '-' +
                    String(firstDay.getDate()).padStart(2, '0');
                toDateInput.value = lastDay.getFullYear() + '-' +
                    String(lastDay.getMonth() + 1).padStart(2, '0') + '-' +
                    String(lastDay.getDate()).padStart(2, '0');
                break;
            case 'daily':
                let todayFormatted = today.getFullYear() + '-' +
                    String(today.getMonth() + 1).padStart(2, '0') + '-' +
                    String(today.getDate()).padStart(2, '0');
                fromDateInput.value = todayFormatted;
                toDateInput.value = todayFormatted;
                break;
        }

        // Submit the form
        form.submit();
    };

    // Chart Rendering Function
    function renderCharts(salesData, employeeData) {
        // Sales Breakdown Pie Chart
        var salesBreakdownCtx = document.getElementById('salesBreakdownChart').getContext('2d');
        new Chart(salesBreakdownCtx, {
            type: 'pie',
            data: {
                labels: ['مدفوعة', 'غير مدفوعة', 'مرتجعة'],
                datasets: [{
                    data: [
                        salesData.paidAmount,
                        salesData.unpaidAmount,
                        salesData.returnedAmount
                    ],
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.7)',   // Green for Paid
                        'rgba(255, 193, 7, 0.7)',   // Yellow for Unpaid
                        'rgba(220, 53, 69, 0.7)'    // Red for Returned
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'توزيع المبيعات'
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Employee Performance Bar Chart
        var employeePerformanceCtx = document.getElementById('employeePerformanceChart').getContext('2d');
        new Chart(employeePerformanceCtx, {
            type: 'bar',
            data: {
                labels: employeeData.map(emp => emp.name),
                datasets: [
                    {
                        label: 'إجمالي المبيعات',
                        data: employeeData.map(emp => emp.totalAmount),
                        backgroundColor: 'rgba(54, 162, 235, 0.6)'
                    },
                    {
                        label: 'مدفوعة',
                        data: employeeData.map(emp => emp.paidAmount),
                        backgroundColor: 'rgba(40, 167, 69, 0.6)'
                    },
                    {
                        label: 'غير مدفوعة',
                        data: employeeData.map(emp => emp.unpaidAmount),
                        backgroundColor: 'rgba(255, 193, 7, 0.6)'
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'المبيعات (SAR)'
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'أداء الموظفين'
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }


    // Expose renderCharts to global scope for Blade template to call
    window.renderCharts = renderCharts;
});
document.addEventListener('DOMContentLoaded', function() {
    // View Toggle Functionality
    const summaryViewBtn = document.getElementById('summaryViewBtn');
    const detailViewBtn = document.getElementById('detailViewBtn');
    const mainReportTable = document.getElementById('mainReportTable');
    const detailedReportTable = document.getElementById('detailedReportTable');

    // Function to reset view
    function resetView() {
        mainReportTable.style.display = 'none';
        detailedReportTable.style.display = 'none';

        summaryViewBtn.classList.remove('active');
        detailViewBtn.classList.remove('active');
    }

    // Initially show main report table
    mainReportTable.style.display = 'block';
    summaryViewBtn.classList.add('active');

    // Summary View Handler
    summaryViewBtn.addEventListener('click', function() {
        resetView();

        summaryViewBtn.classList.add('active');
        mainReportTable.style.display = 'block';
    });

    // Detailed View Handler
    detailViewBtn.addEventListener('click', function() {
        resetView();

        detailViewBtn.classList.add('active');
        detailedReportTable.style.display = 'block';
    });

    // Print Functionality
    document.getElementById('printBtn').addEventListener('click', function() {
        window.print();
    });
});
