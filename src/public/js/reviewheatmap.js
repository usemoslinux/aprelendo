/**
 * Copyright (C) 2019 Pablo Castagnino
 *
 * This file is part of aprelendo.
 *
 * aprelendo is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * aprelendo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with aprelendo.  If not, see <https://www.gnu.org/licenses/>.
 */

(function () {
    const el = document.getElementById('heatmap');
    if (!el) { console.error('#heatmap not found'); return; }

    let cal = null;
    let data = null;
    let currentRange = null;

    function monthsForViewport() {
        if (window.matchMedia('(min-width: 1024px)').matches) return 12; // desktop
        if (window.matchMedia('(min-width: 640px)').matches) return 6;   // tablet
        return 1;                                                        // phone
    }

    function paint(range) {
        if (!data) return;
        if (cal) cal.destroy();

        // get max value for color scale
        const values = data.map(d => d.count).filter(v => v != null);
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
                    source: data,
                    x: 'date',
                    y: 'count',
                    groupY: 'sum',
                },

                date: {
                    start: new Date(new Date().getFullYear(), new Date().getMonth() - (range - 1), 1),
                },

                scale: { color: { type: "linear", range: ['#BFE6FF', '#0062E6'], interpolate: 'hsl', domain: o, } },
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
        const res = await fetch('/ajax/getreviewsperday.php', { cache: 'no-store' });
        data = await res.json();

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
    }

    el.style.width = '100%';

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
