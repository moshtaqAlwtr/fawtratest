
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        .calendar-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .calendar-header {
            background: linear-gradient(135deg, #dbe2e8, #ededed);
            color: rgb(0, 0, 0);
            padding: 30px;
            text-align: center;
            position: relative;
        }

        .calendar-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .calendar-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .nav-button {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            padding: 12px 16px;
            border-radius: 50%;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .nav-button:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .month-year {
            font-size: 1.8rem;
            font-weight: 600;
        }

        .view-filters {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .filter-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-btn.active {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.05);
        }

        .view-toggle {
            position: absolute;
            top: 30px;
            left: 30px;
            display: flex;
            gap: 10px;
        }

        .toggle-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .toggle-btn.active {
            background: rgba(255, 255, 255, 0.3);
        }

        .calendar-body {
            padding: 30px;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background: #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
        }

        .day-header {
            background: linear-gradient(135deg, #3498db, #5dade2);
            color: white;
            padding: 20px 10px;
            text-align: center;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .day-cell {
            background: white;
            min-height: 140px;
            padding: 15px 10px;
            position: relative;
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent;
        }

        .day-cell:hover {
            background: #f8f9fa;
            border-color: #3498db;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .day-cell.today {
            background: linear-gradient(135deg, #e8f5e8, #f0f8f0);
            border-color: #27ae60;
        }

        .day-cell.other-month {
            background: #f8f9fa;
            color: #bbb;
        }

        .day-number {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .day-cell.other-month .day-number {
            color: #ccc;
        }

        .bookings-list {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .booking-item {
            background: linear-gradient(135deg, #3498db, #5dade2);
            color: white;
            padding: 8px 10px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .booking-item:hover {
            transform: scale(1.05);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        }

        .booking-item.confirmed {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
        }

        .booking-item.pending {
            background: linear-gradient(135deg, #f39c12, #f1c40f);
        }

        .booking-item.cancelled {
            background: linear-gradient(135deg, #e74c3c, #e67e22);
        }

        .booking-item.completed {
            background: linear-gradient(135deg, #8e44ad, #9b59b6);
        }

        .booking-icon {
            font-size: 10px;
        }

        .booking-time {
            font-size: 9px;
            opacity: 0.9;
            margin-top: 2px;
        }

        .more-bookings {
            background: #95a5a6;
            color: white;
            padding: 4px 8px;
            border-radius: 10px;
            font-size: 10px;
            text-align: center;
            margin-top: 5px;
            cursor: pointer;
        }

        .booking-details-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: white;
            border-radius: 20px;
            padding: 30px;
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #eee;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2c3e50;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #95a5a6;
        }

        .modal-booking-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
            border-right: 4px solid #3498db;
        }

        .modal-booking-item.confirmed {
            border-right-color: #27ae60;
        }

        .modal-booking-item.pending {
            border-right-color: #f39c12;
        }

        .modal-booking-item.cancelled {
            border-right-color: #e74c3c;
        }

        .modal-booking-item.completed {
            border-right-color: #8e44ad;
        }

        .legend {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
        }

        .legend-color {
            width: 15px;
            height: 15px;
            border-radius: 50%;
        }

        .legend-color.confirmed {
            background: #27ae60;
        }

        .legend-color.pending {
            background: #f39c12;
        }

        .legend-color.cancelled {
            background: #e74c3c;
        }

        .legend-color.completed {
            background: #8e44ad;
        }

        @media (max-width: 768px) {
            .calendar-container {
                margin: 10px;
                border-radius: 15px;
            }

            .calendar-header {
                padding: 20px;
            }

            .calendar-title {
                font-size: 1.8rem;
            }

            .calendar-body {
                padding: 15px;
            }

            .day-cell {
                min-height: 100px;
                padding: 10px 5px;
            }

            .day-number {
                font-size: 1rem;
            }

            .booking-item {
                font-size: 10px;
                padding: 6px 8px;
            }

            .view-toggle {
                position: static;
                justify-content: center;
                margin-top: 15px;
            }

            .legend {
                gap: 10px;
            }
        }
    </style>

    <div class="calendar-container">
        <div class="calendar-header">
            <div class="view-toggle">
                <button class="toggle-btn active" onclick="toggleView('calendar')">
                    <i class="fas fa-calendar-alt"></i>
                </button>
                <button class="toggle-btn" onclick="toggleView('list')">
                    <i class="fas fa-list"></i>
                </button>
            </div>

            <h1 class="calendar-title">
                <i class="fas fa-calendar-check"></i>
                تقويم الحجوزات
            </h1>

            <div class="calendar-controls">
                <button class="nav-button" onclick="previousMonth()">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <div class="month-year" id="monthYear"></div>
                <button class="nav-button" onclick="nextMonth()">
                    <i class="fas fa-chevron-left"></i>
                </button>
            </div>

            <div class="view-filters">
                <button class="filter-btn active" onclick="filterBookings('all')">الكل</button>
                <button class="filter-btn" onclick="filterBookings('today')">اليوم</button>
                <button class="filter-btn" onclick="filterBookings('week')">الأسبوع</button>
                <button class="filter-btn" onclick="filterBookings('month')">الشهر</button>
            </div>
        </div>

        <div class="calendar-body">
            <div class="calendar-grid" id="calendarGrid"></div>

            <div class="legend">
                <div class="legend-item">
                    <div class="legend-color confirmed"></div>
                    <span>مؤكد</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color pending"></div>
                    <span>تحت المراجعة</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color cancelled"></div>
                    <span>ملغي</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color completed"></div>
                    <span>مكتمل</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for booking details -->
    <div class="booking-details-modal" id="bookingModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">تفاصيل الحجوزات</h3>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <div id="modalBookings"></div>
        </div>
    </div>

    <script>
        const bookingsData = @json($calendarBookings);

        let currentDate = new Date();

        document.addEventListener('DOMContentLoaded', () => {
            generateCalendar();
            document.getElementById('prevMonth').addEventListener('click', () => changeMonth(-1));
            document.getElementById('nextMonth').addEventListener('click', () => changeMonth(1));
            document.querySelector('.close-btn').addEventListener('click', () => {
                document.getElementById('bookingModal').style.display = 'none';
            });
        });
        let currentFilter = 'all';

        const monthNames = [
            'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو',
            'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'
        ];

        const dayNames = ['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];

        function generateCalendar() {
            const calendarGrid = document.getElementById('calendarGrid');
            const monthYear = document.getElementById('monthYear');

            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();

            monthYear.textContent = `${monthNames[month]} ${year}`;

            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - firstDay.getDay());

            calendarGrid.innerHTML = '';

            // Add day headers
            dayNames.forEach(day => {
                const dayHeader = document.createElement('div');
                dayHeader.className = 'day-header';
                dayHeader.textContent = day;
                calendarGrid.appendChild(dayHeader);
            });

            // Add calendar days
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            for (let i = 0; i < 42; i++) {
                const cellDate = new Date(startDate);
                cellDate.setDate(startDate.getDate() + i);

                const dayCell = document.createElement('div');
                dayCell.className = 'day-cell';

                if (cellDate.getMonth() !== month) {
                    dayCell.classList.add('other-month');
                }

                if (cellDate.getTime() === today.getTime()) {
                    dayCell.classList.add('today');
                }

                const dayNumber = document.createElement('div');
                dayNumber.className = 'day-number';
                dayNumber.textContent = cellDate.getDate();
                dayCell.appendChild(dayNumber);

                const bookingsList = document.createElement('div');
                bookingsList.className = 'bookings-list';

                const dateKey = cellDate.toISOString().split('T')[0];
                const dayBookings = bookingsData[dateKey] || [];

                if (dayBookings.length > 0) {
                    const displayBookings = dayBookings.slice(0, 3);
                    displayBookings.forEach(booking => {
                        const bookingItem = document.createElement('div');
                        bookingItem.className = `booking-item ${booking.status}`;
                        bookingItem.innerHTML = `
                            <i class="fas fa-user booking-icon"></i>
                            <div>
                                <div>${booking.client}</div>
                                <div class="booking-time">${booking.time}</div>
                            </div>
                        `;
                        bookingsList.appendChild(bookingItem);
                    });

                    if (dayBookings.length > 3) {
                        const moreBookings = document.createElement('div');
                        moreBookings.className = 'more-bookings';
                        moreBookings.textContent = `+${dayBookings.length - 3} أخرى`;
                        bookingsList.appendChild(moreBookings);
                    }

                    dayCell.addEventListener('click', () => showBookingDetails(cellDate, dayBookings));
                }

                dayCell.appendChild(bookingsList);
                calendarGrid.appendChild(dayCell);
            }
        }

        function showBookingDetails(date, bookings) {
            const modal = document.getElementById('bookingModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalBookings = document.getElementById('modalBookings');

            modalTitle.textContent = `حجوزات يوم ${date.toLocaleDateString('ar-SA')}`;

            modalBookings.innerHTML = '';
            bookings.forEach(booking => {
                const bookingDiv = document.createElement('div');
                bookingDiv.className = `modal-booking-item ${booking.status}`;

                const statusText = {
                    'confirmed': 'مؤكد',
                    'pending': 'تحت المراجعة',
                    'cancelled': 'ملغي',
                    'completed': 'مكتمل'
                };

                bookingDiv.innerHTML = `
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h4 style="margin: 0 0 5px 0; color: #2c3e50;">${booking.client.trade_name}</h4>
                            <p style="margin: 0; color: #7f8c8d;">
                                <i class="fas fa-concierge-bell"></i> ${booking.product.name}
                            </p>
                        </div>
                        <div style="text-align: left;">
                            <div style="font-weight: bold; margin-bottom: 5px;">
                                <i class="fas fa-clock"></i> ${booking.time}
                            </div>
                            <span style="background: #3498db; color: white; padding: 4px 8px; border-radius: 10px; font-size: 11px;">
                                ${statusText[booking.status]}
                            </span>
                        </div>
                    </div>
                `;

                modalBookings.appendChild(bookingDiv);
            });

            modal.style.display = 'flex';
        }

        function closeModal() {
            const modal = document.getElementById('bookingModal');
            modal.style.display = 'none';
        }

        function previousMonth() {
            currentDate.setMonth(currentDate.getMonth() - 1);
            generateCalendar();
        }

        function nextMonth() {
            currentDate.setMonth(currentDate.getMonth() + 1);
            generateCalendar();
        }

        function filterBookings(filter) {
            currentFilter = filter;

            // Update active filter button
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');

            // Here you would implement the actual filtering logic
            // For now, just regenerate the calendar
            generateCalendar();
        }

        function toggleView(view) {
            document.querySelectorAll('.toggle-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');

            if (view === 'list') {
                // Switch to list view (implement as needed)
                alert('عرض القائمة قيد التطوير');
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('bookingModal');
            if (event.target === modal) {
                closeModal();
            }
        }

        // Initialize calendar
        generateCalendar();
    </script>

