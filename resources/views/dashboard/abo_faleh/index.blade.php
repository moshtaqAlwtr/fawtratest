@extends('master')

@section('title')
    لوحة التحكم
@stop

@section('content')

@php
  $today = \Carbon\Carbon::today();
@endphp

<style>
  @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;600;700;900&family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap');

  :root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
    --secondary-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    --light-gradient: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    --glass-bg: rgba(255, 255, 255, 0.8);
    --glass-border: rgba(0, 0, 0, 0.1);
    --shadow-light: rgba(0, 0, 0, 0.05);
    --shadow-dark: rgba(0, 0, 0, 0.15);
    --text-primary: #1a202c;
    --text-secondary: #4a5568;
    --accent-blue: #3182ce;
    --accent-green: #38a169;
    --accent-purple: #805ad5;
    --accent-red: #e53e3e;
    --accent-orange: #dd6b20;
    --accent-pink: #d53f8c;
  }

  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body {
    font-family: 'Inter', 'Cairo', sans-serif;
    background: #ffffff;
    overflow-x: hidden;
  }

  /* خلفية ديناميكية مع جزيئات متحركة */
  .dashboard-bg {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 50%, #e2e8f0 100%);
    z-index: -2;
  }

  .dashboard-bg::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image:
      radial-gradient(circle at 20% 30%, rgba(120, 119, 198, 0.1) 0%, transparent 50%),
      radial-gradient(circle at 80% 70%, rgba(255, 119, 198, 0.1) 0%, transparent 50%),
      radial-gradient(circle at 40% 80%, rgba(120, 219, 255, 0.1) 0%, transparent 50%);
    animation: float 20s ease-in-out infinite;
  }

  @keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    33% { transform: translateY(-20px) rotate(1deg); }
    66% { transform: translateY(-10px) rotate(-1deg); }
  }

  /* جزيئات متحركة */
  .particles {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1;
    overflow: hidden;
  }

  .particle {
    position: absolute;
    background: rgba(120, 119, 198, 0.2);
    border-radius: 50%;
    animation: floatParticle 15s infinite linear;
  }

  .particle:nth-child(1) { width: 4px; height: 4px; left: 10%; animation-delay: 0s; }
  .particle:nth-child(2) { width: 6px; height: 6px; left: 20%; animation-delay: -5s; }
  .particle:nth-child(3) { width: 3px; height: 3px; left: 30%; animation-delay: -10s; }
  .particle:nth-child(4) { width: 5px; height: 5px; left: 40%; animation-delay: -15s; }
  .particle:nth-child(5) { width: 4px; height: 4px; left: 50%; animation-delay: -20s; }
  .particle:nth-child(6) { width: 7px; height: 7px; left: 60%; animation-delay: -25s; }
  .particle:nth-child(7) { width: 3px; height: 3px; left: 70%; animation-delay: -30s; }
  .particle:nth-child(8) { width: 5px; height: 5px; left: 80%; animation-delay: -35s; }
  .particle:nth-child(9) { width: 4px; height: 4px; left: 90%; animation-delay: -40s; }

  @keyframes floatParticle {
    from {
      transform: translateY(100vh) rotate(0deg);
      opacity: 0;
    }
    10% {
      opacity: 1;
    }
    90% {
      opacity: 1;
    }
    to {
      transform: translateY(-100px) rotate(360deg);
      opacity: 0;
    }
  }

  .dashboard-container {
    position: relative;
    min-height: 100vh;
    padding: 2rem 0;
    z-index: 1;
  }

  .dashboard-header {
    text-align: center;
    margin-bottom: 4rem;
    position: relative;
  }

  .dashboard-title {
    font-size: 3.5rem;
    font-weight: 900;
    background: linear-gradient(135deg, #1a202c 0%, #a855f7 50%, #06b6d4 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 1rem;
    text-shadow: 0 2px 20px rgba(168, 85, 247, 0.3);
    animation: pulse 3s ease-in-out infinite;
  }

  @keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.02); }
  }

  .dashboard-subtitle {
    color: #4a5568;
    font-size: 1.1rem;
    font-weight: 500;
    letter-spacing: 2px;
  }

  /* الإحصائيات الإجمالية */
  .total-stats-section {
    margin-bottom: 4rem;
    padding: 0 1rem;
  }

  .total-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
  }

  .total-stat-item {
    position: relative;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(0, 0, 0, 0.08);
    border-radius: 20px;
    padding: 2rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 1.5rem;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
    animation: fadeInScale 0.6s ease forwards;
    opacity: 0;
    transform: scale(0.9);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  }

  .total-stat-item:nth-child(1) { animation-delay: 0.1s; }
  .total-stat-item:nth-child(2) { animation-delay: 0.2s; }
  .total-stat-item:nth-child(3) { animation-delay: 0.3s; }
  .total-stat-item:nth-child(4) { animation-delay: 0.4s; }
  .total-stat-item:nth-child(5) { animation-delay: 0.5s; }
  .total-stat-item:nth-child(6) { animation-delay: 0.6s; }

  @keyframes fadeInScale {
    to {
      opacity: 1;
      transform: scale(1);
    }
  }

  .total-stat-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.6), transparent);
    transition: left 0.8s;
  }

  .total-stat-item:hover::before {
    left: 100%;
  }

  .total-stat-item:hover {
    transform: translateY(-5px) scale(1.02);
    background: rgba(255, 255, 255, 0.95);
    border-color: rgba(0, 0, 0, 0.15);
    box-shadow:
      0 20px 40px rgba(0, 0, 0, 0.15),
      0 0 0 1px rgba(0, 0, 0, 0.05),
      inset 0 1px 0 rgba(255, 255, 255, 0.8);
  }

  .total-stat-icon {
    font-size: 3rem;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 80px;
    height: 80px;
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(0, 0, 0, 0.1);
    filter: drop-shadow(0 4px 15px rgba(0, 0, 0, 0.1));
    transition: all 0.3s ease;
  }

  .total-stat-item:hover .total-stat-icon {
    transform: scale(1.1) rotate(5deg);
  }

  .total-stat-content {
    flex: 1;
    text-align: right;
  }

  .total-stat-value {
    font-size: 2.2rem;
    font-weight: 900;
    line-height: 1;
    margin-bottom: 0.5rem;
    color: #1a202c;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  }

  .total-stat-currency {
    font-size: 1rem;
    font-weight: 500;
    color: #4a5568;
  }

  .total-stat-title {
    font-size: 1rem;
    font-weight: 600;
    color: #4a5568;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  /* ألوان الإحصائيات الإجمالية */
  .total-visits {
    border-left: 4px solid #3b82f6;
  }
  .total-visits .total-stat-icon {
    color: #3b82f6;
    background: rgba(59, 130, 246, 0.1);
  }

  .total-payments {
    border-left: 4px solid #10b981;
  }
  .total-payments .total-stat-icon {
    color: #10b981;
    background: rgba(16, 185, 129, 0.1);
  }

  .total-paid-invoices {
    border-left: 4px solid #06b6d4;
  }
  .total-paid-invoices .total-stat-icon {
    color: #06b6d4;
    background: rgba(6, 182, 212, 0.1);
  }

  .total-unpaid {
    border-left: 4px solid #ef4444;
  }
  .total-unpaid .total-stat-icon {
    color: #ef4444;
    background: rgba(239, 68, 68, 0.1);
  }

  .total-expenses {
    border-left: 4px solid #ec4899;
  }
  .total-expenses .total-stat-icon {
    color: #ec4899;
    background: rgba(236, 72, 153, 0.1);
  }

  .total-notes {
    border-left: 4px solid #f59e0b;
  }
  .total-notes .total-stat-icon {
    color: #f59e0b;
    background: rgba(245, 158, 11, 0.1);
  }

  .employees-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 2rem;
    padding: 0 1rem;
  }

  /* بطاقة الموظف */
  .employee-card {
    position: relative;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(0, 0, 0, 0.08);
    border-radius: 24px;
    padding: 0;
    overflow: hidden;
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    animation: slideInUp 0.8s ease forwards;
    opacity: 0;
    transform: translateY(50px);
    box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1);
  }

  .employee-card:nth-child(1) { animation-delay: 0.1s; }
  .employee-card:nth-child(2) { animation-delay: 0.2s; }
  .employee-card:nth-child(3) { animation-delay: 0.3s; }
  .employee-card:nth-child(4) { animation-delay: 0.4s; }
  .employee-card:nth-child(5) { animation-delay: 0.5s; }
  .employee-card:nth-child(6) { animation-delay: 0.6s; }

  @keyframes slideInUp {
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .employee-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--primary-gradient);
    background-size: 200% 200%;
    animation: gradientShift 3s ease infinite;
  }

  @keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
  }

  .employee-card:hover {
    transform: translateY(-8px) scale(1.02);
    background: rgba(255, 255, 255, 0.95);
    border-color: rgba(0, 0, 0, 0.15);
    box-shadow:
      0 25px 50px rgba(0, 0, 0, 0.2),
      0 0 0 1px rgba(0, 0, 0, 0.05),
      inset 0 1px 0 rgba(255, 255, 255, 0.8);
  }

  /* رأس البطاقة */
  .card-header {
    padding: 2rem 2rem 1rem;
    position: relative;
  }

  .employee-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 0.5rem;
    text-align: center;
  }

  .employee-role {
    font-size: 0.9rem;
    color: #4a5568;
    text-align: center;
    text-transform: uppercase;
    letter-spacing: 1px;
  }

  /* شبكة الإحصائيات */
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    padding: 1rem 2rem 2rem;
  }

  .stat-full {
    grid-column: 1 / -1;
  }

  /* عنصر الإحصائية */
  .stat-item {
    position: relative;
    background: rgba(255, 255, 255, 0.7);
    border: 1px solid rgba(0, 0, 0, 0.08);
    border-radius: 16px;
    padding: 1.5rem 1rem;
    text-align: center;
    transition: all 0.3s ease;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  }

  .stat-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.6), transparent);
    transition: left 0.6s;
  }

  .stat-item:hover::before {
    left: 100%;
  }

  .stat-item:hover {
    background: rgba(255, 255, 255, 0.9);
    border-color: rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  }

  .stat-icon {
    font-size: 2rem;
    margin-bottom: 0.8rem;
    display: block;
    filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.1));
  }

  .stat-title {
    font-size: 0.75rem;
    color: #4a5568;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 0.8rem;
  }

  .stat-value {
    font-size: 1.8rem;
    font-weight: 800;
    line-height: 1;
    margin-bottom: 0.3rem;
    color: #1a202c;
  }

  .stat-currency {
    font-size: 0.8rem;
    font-weight: 400;
    color: #4a5568;
  }

  /* ألوان الإحصائيات */
  .stat-visits { }
  .stat-visits .stat-icon { color: #3b82f6; }
  .stat-visits .stat-value { color: #3b82f6; }

  .stat-payments { }
  .stat-payments .stat-icon { color: #10b981; }
  .stat-payments .stat-value { color: #10b981; }

  .stat-paid-invoices { }
  .stat-paid-invoices .stat-icon { color: #06b6d4; }
  .stat-paid-invoices .stat-value { color: #06b6d4; }

  .stat-unpaid { }
  .stat-unpaid .stat-icon { color: #ef4444; }
  .stat-unpaid .stat-value { color: #ef4444; }

  .stat-notes { }
  .stat-notes .stat-icon { color: #f59e0b; }
  .stat-notes .stat-value { color: #f59e0b; }

  .stat-expenses { }
  .stat-expenses .stat-icon { color: #ec4899; }
  .stat-expenses .stat-value { color: #ec4899; }

  /* شبكة البطاقات */
  .employees-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 2rem;
    padding: 0 1rem;
  }

  @media (max-width: 1200px) {
    .employees-grid {
      grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    }

    .total-stats-grid {
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 1rem;
    }

    .total-stat-item {
      padding: 1.5rem 1rem;
      gap: 1rem;
    }

    .total-stat-icon {
      width: 60px;
      height: 60px;
      font-size: 2.5rem;
    }

    .total-stat-value {
      font-size: 1.8rem;
    }
  }

  @media (max-width: 768px) {
    .dashboard-title {
      font-size: 2.5rem;
    }

    .dashboard-container {
      padding: 1rem 0;
    }

    .total-stats-section {
      margin-bottom: 3rem;
    }

    .total-stats-grid {
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }

    .total-stat-item {
      flex-direction: column;
      text-align: center;
      padding: 1.5rem 1rem;
      gap: 1rem;
    }

    .total-stat-content {
      text-align: center;
    }

    .employees-grid {
      grid-template-columns: 1fr;
      gap: 1.5rem;
      padding: 0 0.5rem;
    }

    .card-header {
      padding: 1.5rem 1.5rem 1rem;
    }

    .stats-grid {
      padding: 1rem 1.5rem 1.5rem;
      gap: 0.8rem;
    }

    .stat-item {
      padding: 1.2rem 0.8rem;
    }

    .stat-value {
      font-size: 1.5rem;
    }
  }

  @media (max-width: 480px) {
    .dashboard-title {
      font-size: 2rem;
    }

    .total-stats-grid {
      grid-template-columns: 1fr;
    }

    .total-stat-item {
      flex-direction: row;
      text-align: right;
    }

    .total-stat-content {
      text-align: right;
    }

    .stats-grid {
      grid-template-columns: 1fr;
    }

    .stat-full {
      grid-column: 1;
    }
  }

  /* تأثيرات إضافية */
  .glow {
    box-shadow: 0 0 20px rgba(168, 85, 247, 0.3);
  }

  /* تحسين الخطوط العربية */
  .arabic-text {
    font-family: 'Cairo', sans-serif;
    direction: rtl;
    text-align: center;
  }
</style>

<div class="dashboard-bg"></div>
<div class="particles">
  <div class="particle"></div>
  <div class="particle"></div>
  <div class="particle"></div>
  <div class="particle"></div>
  <div class="particle"></div>
  <div class="particle"></div>
  <div class="particle"></div>
  <div class="particle"></div>
  <div class="particle"></div>
</div>

<div class="dashboard-container">
  <div class="container-fluid">

    <div class="dashboard-header">
      <h1 class="dashboard-title arabic-text">لوحة التحكم التفاعلية</h1>
      <p class="dashboard-subtitle">Dashboard Analytics • Real-time Data</p>
    </div>

    {{-- إجمالي الإحصائيات --}}
    <div class="total-stats-section">
      <div class="total-stats-grid">

        <div class="total-stat-item total-visits">
          <div class="total-stat-icon">👥</div>
          <div class="total-stat-content">
            <div class="total-stat-value">{{ number_format($employees->sum('visits_count') ?? 0) }}</div>
            <div class="total-stat-title arabic-text">إجمالي الزيارات</div>
          </div>
        </div>

        <div class="total-stat-item total-payments">
          <div class="total-stat-icon">💰</div>
          <div class="total-stat-content">
            <div class="total-stat-value">{{ number_format($employees->sum('payments_receipts_sum') ?? 0) }} <span class="total-stat-currency">ر.س</span></div>
            <div class="total-stat-title arabic-text">إجمالي السندات والمدفوعات</div>
          </div>
        </div>

        <div class="total-stat-item total-paid-invoices">
          <div class="total-stat-icon">✅</div>
          <div class="total-stat-content">
            <div class="total-stat-value">{{ number_format($employees->sum('paid_invoices_sum') ?? 0) }} <span class="total-stat-currency">ر.س</span></div>
            <div class="total-stat-title arabic-text">إجمالي الفواتير المدفوعة</div>
          </div>
        </div>

        <div class="total-stat-item total-unpaid">
          <div class="total-stat-icon">⚠️</div>
          <div class="total-stat-content">
            <div class="total-stat-value">{{ number_format($employees->sum('unpaid_invoices_sum') ?? 0) }} <span class="total-stat-currency">ر.س</span></div>
            <div class="total-stat-title arabic-text">إجمالي الفواتير غير المدفوعة</div>
          </div>
        </div>

        <div class="total-stat-item total-expenses">
          <div class="total-stat-icon">💸</div>
          <div class="total-stat-content">
            <div class="total-stat-value">{{ number_format($employees->sum('expenses_sum') ?? 0) }} <span class="total-stat-currency">ر.س</span></div>
            <div class="total-stat-title arabic-text">إجمالي المصروفات</div>
          </div>
        </div>

        <div class="total-stat-item total-notes">
          <div class="total-stat-icon">📝</div>
          <div class="total-stat-content">
            <div class="total-stat-value">{{ number_format($employees->sum('notes_count') ?? 0) }}</div>
            <div class="total-stat-title arabic-text">إجمالي الملاحظات</div>
          </div>
        </div>

      </div>
    </div>

    <div class="employees-grid">
      @foreach($employees as $index => $user)
        @php $emp = $user->employee; @endphp
        <div class="employee-card" style="animation-delay: {{ ($index + 1) * 0.15 }}s">

          <div class="card-header">
            <h3 class="employee-name arabic-text" title="{{ $emp?->full_name ?? 'غير معروف' }}">
              {{ $emp->full_name ?? $user->name ?? 'غير معروف' }}
            </h3>
            <p class="employee-role">Employee Analytics</p>
          </div>

          <div class="stats-grid">

            <div class="stat-item stat-visits">
              <span class="stat-icon">👥</span>
              <div class="stat-title arabic-text">عدد الزيارات</div>
              <div class="stat-value">
                {{ number_format($user->visits_count ?? 0) }}
              </div>
            </div>

            <div class="stat-item stat-payments">
              <span class="stat-icon">💰</span>
              <div class="stat-title arabic-text">السندات والمدفوعات</div>
              <div class="stat-value">
                {{ number_format($user->payments_receipts_sum ?? 0) }}
                <span class="stat-currency">ر.س</span>
              </div>
            </div>

            <div class="stat-item stat-paid-invoices">
              <span class="stat-icon">✅</span>
              <div class="stat-title arabic-text">الفواتير المدفوعة</div>
              <div class="stat-value">
                {{ number_format($user->paid_invoices_sum ?? 0) }}
                <span class="stat-currency">ر.س</span>
              </div>
            </div>

            <div class="stat-item stat-unpaid">
              <span class="stat-icon">⚠️</span>
              <div class="stat-title arabic-text">عدد الفواتير غير المدفوعة</div>
              <div class="stat-value">
                {{ number_format($user->unpaid_invoices_count ?? 0) }}
              </div>
            </div>

            <div class="stat-item stat-unpaid stat-full">
              <span class="stat-icon">💳</span>
              <div class="stat-title arabic-text">إجمالي الفواتير غير المدفوعة</div>
              <div class="stat-value">
                {{ number_format($user->unpaid_invoices_sum ?? 0) }}
                <span class="stat-currency">ر.س</span>
              </div>
            </div>

            <div class="stat-item stat-notes">
              <span class="stat-icon">📝</span>
              <div class="stat-title arabic-text">الملاحظات</div>
              <div class="stat-value">
                {{ number_format($user->notes_count ?? 0) }}
              </div>
            </div>

            <div class="stat-item stat-expenses">
              <span class="stat-icon">💸</span>
              <div class="stat-title arabic-text">المصروفات</div>
              <div class="stat-value">
                {{ number_format($user->expenses_sum ?? 0) }}
                <span class="stat-currency">ر.س</span>
              </div>
            </div>

          </div>
        </div>
      @endforeach
    </div>
  </div>
</div>

@endsection
