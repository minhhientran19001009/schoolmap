@php
    $period = $statistics['period'];
    $totals = $statistics['totals'];
    $rows = array_slice($statistics['rows'], 0, 10);
    $priorities = $statistics['priorities'];
    $maxValue = max(1, $statistics['max_value']);
@endphp

<x-filament-widgets::widget>
    <section class="sd-dashboard" wire:loading.class="sd-dashboard-loading">
        <header class="sd-header">
            <div>
                <h2>Bức tranh cung–cầu</h2>
                <p>{{ $period?->name ?? 'Chưa có kỳ báo cáo' }} · Đối chiếu theo ngành/nghề và trình độ</p>
            </div>

            <div class="sd-header-actions">
                <label class="sd-period-select">
                    <span>Kỳ báo cáo</span>
                    <select wire:model.live="periodId">
                        @forelse ($periodOptions as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @empty
                            <option value="">Chưa có dữ liệu</option>
                        @endforelse
                    </select>
                </label>

                <button type="button" class="sd-export-button" wire:click="exportSummary" @disabled(! $period)>
                    <x-filament::icon icon="heroicon-o-arrow-down-tray" />
                    <span>Xuất tổng hợp</span>
                </button>
            </div>
        </header>

        <div class="sd-stat-grid">
            <article class="sd-stat-card sd-stat-demand">
                <div class="sd-stat-title"><span>Nhu cầu nhân lực</span><x-filament::icon icon="heroicon-o-briefcase" /></div>
                <strong>{{ number_format($totals['demand'], 0, ',', '.') }} <small>người</small></strong>
                <p>Tuyển dụng và đào tạo</p>
            </article>

            <article class="sd-stat-card sd-stat-capacity">
                <div class="sd-stat-title"><span>Năng lực đào tạo</span><x-filament::icon icon="heroicon-o-academic-cap" /></div>
                <strong>{{ number_format($totals['capacity'], 0, ',', '.') }} <small>người</small></strong>
                <p>Khả năng đáp ứng dự kiến</p>
            </article>

            <article class="sd-stat-card sd-stat-gap">
                <div class="sd-stat-title"><span>Chênh lệch thiếu</span><x-filament::icon icon="heroicon-o-clipboard-document-list" /></div>
                <strong>{{ number_format($totals['gap'], 0, ',', '.') }} <small>người</small></strong>
                <p>Tổng thiếu theo từng nghề, trình độ</p>
            </article>

            <article class="sd-stat-card sd-stat-proposed">
                <div class="sd-stat-title"><span>Đã được đề xuất</span><x-filament::icon icon="heroicon-o-check-circle" /></div>
                <strong>{{ number_format($totals['proposed'], 0, ',', '.') }} <small>người</small></strong>
                <p>Đề xuất chưa phải cam kết</p>
            </article>
        </div>

        <div class="sd-content-grid">
            <article class="sd-panel sd-comparison-panel">
                <div class="sd-panel-header">
                    <h3>Nhu cầu và năng lực theo nghề</h3>
                    <div class="sd-legend">
                        <span><i class="sd-legend-demand"></i>Nhu cầu</span>
                        <span><i class="sd-legend-capacity"></i>Năng lực</span>
                    </div>
                </div>

                @forelse ($rows as $row)
                    @php
                        $demandWidth = max(0, min(100, ($row['demand'] / $maxValue) * 100));
                        $capacityWidth = max(0, min(100, ($row['capacity'] / $maxValue) * 100));
                    @endphp
                    <div class="sd-comparison-row">
                        <div class="sd-row-label">
                            <strong title="{{ $row['major'] }}">{{ $row['major'] }}</strong>
                            <span>{{ $row['level'] }}</span>
                        </div>
                        <div class="sd-bars">
                            <div class="sd-bar-line">
                                <div class="sd-bar sd-bar-demand" style="width: {{ $demandWidth }}%"></div>
                                <span>{{ number_format($row['demand'], 0, ',', '.') }}</span>
                            </div>
                            <div class="sd-bar-line">
                                <div class="sd-bar sd-bar-capacity" style="width: {{ $capacityWidth }}%"></div>
                                <span>{{ number_format($row['capacity'], 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="sd-balance {{ $row['gap'] > 0 ? 'is-gap' : ($row['surplus'] > 0 ? 'is-surplus' : 'is-balanced') }}">
                            @if ($row['gap'] > 0)
                                Thiếu {{ number_format($row['gap'], 0, ',', '.') }}
                            @elseif ($row['surplus'] > 0)
                                Dư {{ number_format($row['surplus'], 0, ',', '.') }}
                            @else
                                Cân bằng
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="sd-empty-state">
                        <x-filament::icon icon="heroicon-o-chart-bar" />
                        <strong>Chưa có dữ liệu cung–cầu trong kỳ này</strong>
                        <span>Hãy công bố nhu cầu doanh nghiệp và năng lực của cơ sở đào tạo.</span>
                    </div>
                @endforelse

                @if (count($statistics['rows']) > count($rows))
                    <p class="sd-more-note">Đang hiển thị 10 nhóm có mức thiếu hụt cao nhất trong tổng số {{ count($statistics['rows']) }} nhóm.</p>
                @endif
            </article>

            <aside class="sd-priority-panel">
                <span class="sd-priority-kicker">Ưu tiên kết nối</span>
                <h3>Thu hẹp khoảng cách</h3>

                <div class="sd-priority-list">
                    @forelse ($priorities as $index => $row)
                        <div class="sd-priority-item">
                            <span class="sd-priority-rank">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                            <div>
                                <strong>{{ $row['major'] }}</strong>
                                <p>{{ $row['level'] }} · Thiếu {{ number_format($row['gap'], 0, ',', '.') }} người</p>
                            </div>
                        </div>
                    @empty
                        <div class="sd-priority-empty">
                            <x-filament::icon icon="heroicon-o-check-badge" />
                            <strong>Chưa có nhóm thiếu hụt</strong>
                            <span>Dữ liệu hiện tại chưa ghi nhận chênh lệch thiếu.</span>
                        </div>
                    @endforelse
                </div>
            </aside>
        </div>
    </section>

    <style>
        .sd-dashboard { --sd-navy: #123f78; --sd-blue: #174b91; --sd-orange: #e2734c; --sd-green: #188563; color: #14233b; transition: opacity .18s ease; }
        .sd-dashboard-loading { opacity: .65; pointer-events: none; }
        .sd-header { display: flex; align-items: flex-end; justify-content: space-between; gap: 1rem; margin-bottom: 1.25rem; }
        .sd-header h2 { margin: 0; font-size: 1.5rem; line-height: 1.2; font-weight: 800; letter-spacing: -.025em; color: #102c54; }
        .sd-header p { margin: .4rem 0 0; color: #5f7089; font-size: .9rem; }
        .sd-header-actions { display: flex; align-items: flex-end; gap: .75rem; }
        .sd-period-select { display: grid; gap: .28rem; }
        .sd-period-select span { color: #64748b; font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; }
        .sd-period-select select { min-width: 10.5rem; height: 2.6rem; border: 1px solid #d7e0ec; border-radius: .7rem; background: #fff; padding: 0 2.2rem 0 .8rem; color: #243651; font: inherit; font-size: .82rem; font-weight: 600; outline: none; }
        .sd-period-select select:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, .12); }
        .sd-export-button { display: inline-flex; align-items: center; justify-content: center; gap: .45rem; height: 2.6rem; border: 1px solid #c9d6e6; border-radius: .7rem; background: #f7fafc; padding: 0 .95rem; color: #193454; font-size: .82rem; font-weight: 700; cursor: pointer; box-shadow: 0 1px 2px rgba(15, 23, 42, .05); }
        .sd-export-button:hover { border-color: #9eb4cf; background: #eef5fb; }
        .sd-export-button:disabled { opacity: .45; cursor: not-allowed; }
        .sd-export-button svg { width: 1rem; height: 1rem; }
        .sd-stat-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1rem; margin-bottom: 1rem; }
        .sd-stat-card { min-width: 0; border: 1px solid #d9e2ee; border-radius: .85rem; background: #fff; padding: 1.2rem 1.35rem; box-shadow: 0 1px 2px rgba(15, 23, 42, .03); }
        .sd-stat-title { display: flex; align-items: center; justify-content: space-between; gap: .75rem; color: #526785; font-size: .86rem; }
        .sd-stat-title svg { width: 1.35rem; height: 1.35rem; color: #5d89c5; }
        .sd-stat-card strong { display: block; margin-top: 1.1rem; color: #0f2d55; font-size: 2rem; line-height: 1; font-weight: 800; }
        .sd-stat-card strong small { color: #53667f; font-size: .76rem; font-weight: 500; }
        .sd-stat-card > p { margin: 1rem 0 0; color: #60728a; font-size: .78rem; }
        .sd-stat-gap { border-top: 3px solid #d3563d; }
        .sd-stat-gap strong { color: #b5432c; }
        .sd-stat-proposed { border-top: 3px solid #2d8b6d; }
        .sd-content-grid { display: grid; grid-template-columns: minmax(0, 1fr) minmax(17rem, 22rem); gap: 1rem; align-items: stretch; }
        .sd-panel { border: 1px solid #d9e2ee; border-radius: .85rem; background: #fff; padding: 1.35rem; box-shadow: 0 1px 2px rgba(15, 23, 42, .03); }
        .sd-panel-header { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.4rem; }
        .sd-panel-header h3 { margin: 0; color: #14233b; font-size: 1rem; font-weight: 800; }
        .sd-legend { display: flex; align-items: center; gap: .9rem; color: #53667f; font-size: .72rem; white-space: nowrap; }
        .sd-legend span { display: inline-flex; align-items: center; gap: .35rem; }
        .sd-legend i { width: .55rem; height: .55rem; border-radius: .14rem; }
        .sd-legend-demand { background: var(--sd-blue); }
        .sd-legend-capacity { background: var(--sd-orange); }
        .sd-comparison-row { display: grid; grid-template-columns: minmax(9rem, 12rem) minmax(12rem, 1fr) 5.8rem; gap: .9rem; align-items: center; padding: .7rem 0; }
        .sd-row-label { min-width: 0; }
        .sd-row-label strong { display: block; overflow: hidden; color: #172b49; font-size: .79rem; font-weight: 750; text-overflow: ellipsis; white-space: nowrap; }
        .sd-row-label span { display: block; margin-top: .24rem; color: #64748b; font-size: .7rem; }
        .sd-bars { display: grid; gap: .42rem; min-width: 0; }
        .sd-bar-line { display: flex; align-items: center; gap: .5rem; height: .48rem; }
        .sd-bar-line > span { min-width: 2.8rem; color: #203755; font-size: .67rem; font-variant-numeric: tabular-nums; }
        .sd-bar { min-width: 2px; height: .48rem; border-radius: .12rem; transition: width .25s ease; }
        .sd-bar-demand { background: var(--sd-blue); }
        .sd-bar-capacity { background: var(--sd-orange); }
        .sd-balance { justify-self: end; font-size: .72rem; font-weight: 750; white-space: nowrap; }
        .sd-balance.is-gap { color: #c2412d; }
        .sd-balance.is-surplus { color: #15805e; }
        .sd-balance.is-balanced { color: #64748b; }
        .sd-more-note { margin: 1rem 0 0; border-top: 1px solid #edf1f6; padding-top: .8rem; color: #64748b; font-size: .7rem; text-align: center; }
        .sd-priority-panel { border-radius: .85rem; background: linear-gradient(160deg, #17477f, #0f3769); padding: 1.6rem; color: #fff; box-shadow: 0 5px 18px rgba(15, 55, 105, .12); }
        .sd-priority-kicker { color: #b9d5f5; font-size: .68rem; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }
        .sd-priority-panel h3 { margin: .75rem 0 1.2rem; font-size: 1.35rem; font-weight: 800; letter-spacing: -.02em; }
        .sd-priority-list { border-top: 1px solid rgba(255, 255, 255, .14); }
        .sd-priority-item { display: grid; grid-template-columns: 1.8rem 1fr; gap: .65rem; padding: 1.05rem 0; border-bottom: 1px solid rgba(255, 255, 255, .14); }
        .sd-priority-rank { color: #8ec4fa; font-size: .7rem; padding-top: .12rem; }
        .sd-priority-item strong { display: block; color: #fff; font-size: .82rem; line-height: 1.35; }
        .sd-priority-item p { margin: .3rem 0 0; color: #d4e5f8; font-size: .7rem; line-height: 1.4; }
        .sd-priority-empty, .sd-empty-state { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: .5rem; min-height: 12rem; text-align: center; }
        .sd-priority-empty svg, .sd-empty-state svg { width: 2rem; height: 2rem; }
        .sd-priority-empty span, .sd-empty-state span { max-width: 24rem; color: #718096; font-size: .73rem; }
        .sd-priority-empty span { color: #c4d8ef; }
        .sd-empty-state { color: #60728a; }
        .sd-empty-state strong { color: #263b57; font-size: .85rem; }
        .dark .sd-dashboard { color: #e5edf8; }
        .dark .sd-header h2, .dark .sd-panel-header h3, .dark .sd-row-label strong, .dark .sd-empty-state strong { color: #f1f5f9; }
        .dark .sd-header p, .dark .sd-stat-card > p, .dark .sd-stat-title, .dark .sd-row-label span { color: #94a3b8; }
        .dark .sd-period-select select, .dark .sd-stat-card, .dark .sd-panel { border-color: #334155; background: #182234; color: #e2e8f0; }
        .dark .sd-export-button { border-color: #475569; background: #1e293b; color: #e2e8f0; }
        .dark .sd-stat-card strong, .dark .sd-bar-line > span { color: #e2e8f0; }
        .dark .sd-stat-gap strong { color: #fb8c72; }
        @media (max-width: 1024px) { .sd-stat-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .sd-content-grid { grid-template-columns: 1fr; } .sd-priority-panel { min-height: auto; } }
        @media (max-width: 700px) { .sd-header { align-items: stretch; flex-direction: column; } .sd-header-actions { align-items: stretch; flex-direction: column; } .sd-period-select select, .sd-export-button { width: 100%; } .sd-stat-grid { grid-template-columns: 1fr; gap: .75rem; } .sd-stat-card { padding: 1rem; } .sd-stat-card strong { margin-top: .8rem; font-size: 1.65rem; } .sd-comparison-row { grid-template-columns: 1fr; gap: .55rem; padding: .9rem 0; border-bottom: 1px solid #edf1f6; } .sd-balance { justify-self: start; } .sd-row-label strong { white-space: normal; } .sd-panel-header { align-items: flex-start; flex-direction: column; } }
    </style>
</x-filament-widgets::widget>
