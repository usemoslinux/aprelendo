// SPDX-License-Identifier: GPL-3.0-or-later

(function () {
    const el = document.getElementById('heatmap');
    if (!el) { console.error('#heatmap not found'); return; }

    let cal = null;
    let payload = null;
    let currentRange = null;

    function monthsForViewport() {
        if (window.matchMedia('(min-width: 1024px)').matches) return 12; // desktop
        if (window.matchMedia('(min-width: 640px)').matches) return 6;   // tablet
        return 1;                                                        // phone
    }

    function paint(range) {
        if (!payload) return;
        if (cal) cal.destroy();

        // get max value for color scale
        const values = payload.map(d => d.count).filter(v => v != null);
        const cap = Math.max(...values);
        const domain = [0, cap];

        cal = new CalHeatmap();
        cal.paint(
            {
                itemSelector: el,
                domain: { type: 'month', gutter: 8, label: { position: 'top' } },
                subDomain: { type: 'day', width: 12, height: 12, radius: 2, gutter: 2 },
                range: range,

                data: {
                    source: payload,
                    x: 'date',
                    y: 'count',
                    groupY: 'sum',
                },

                date: {
                    start: new Date(new Date().getFullYear(), new Date().getMonth() - (range - 1), 1),
                },

                scale: { color: { type: "linear", range: ['#BFE6FF', '#0062E6'], interpolate: 'hsl', domain: domain, } },
                theme: 'light'
            },
            [
                [Tooltip, {
                    enabled: true,
                    text: function (timestamp, value, dayjsDate) {
                        const v = value ?? 0; // force null/undefined → 0
                        return `${dayjsDate.format('MMMM D, YYYY')}: ${v} review${v === 1 ? '' : 's'}`;
                    }
                }],
                [CalendarLabel, {
                    position: 'left',
                    key: 'weekday-left',
                    width: 20,
                    textAlign: 'end',
                    padding: [38, 6, 0, 0],
                    text: () => ['Mon', '', 'Wed', '', 'Fri', '', ''],
                }]
            ]
        );
    }

    async function init() {
        try {
            // Get the 'u' parameter from the current page URL
            const url_params = new URLSearchParams(window.location.search);
            const u = url_params.get('u') ?? '';
    
            // Send it as GET parameter to getreviewsperday.php
            const response = await fetch(`/ajax/getreviewsperday.php?u=${encodeURIComponent(u)}`, {
                cache: 'no-store'
            });

            if (!response.ok) throw new Error(`HTTP error: ${response.status}`);
    
            const data = await response.json();
    
            if (!data.success) {
                el.innerHTML = `
                    <div class="cal-heatmap-error">
                    <p>No data available for this period.</p>
                    </div>
                `;
                throw new Error(data.error_msg || 'Failed to fetch review data');
            }
    
            payload = data.payload;
    
            currentRange = monthsForViewport();
            paint(currentRange);
    
            let t = null;
            window.addEventListener('resize', function () {
                clearTimeout(t);
                t = setTimeout(() => {
                    const next = monthsForViewport();
                    if (next !== currentRange) {
                        currentRange = next;
                        paint(currentRange);
                    }
                }, 150);
            });
        } catch (error) {
            console.error(error);
        }
    }

    el.style.width = '100%';

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
