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

$(document).ready(function () {
    drawTotalStats(); // get data to feed chart

    async function drawTotalStats() {
        // get user from URL param, if any
        const url_params = new URLSearchParams(window.location.search);
        const user_name = url_params.get('u');

        try {
            const params = new URLSearchParams({ type: "words" });
            if (user_name) {
                params.append('u', user_name);
            }

            const response = await fetch(`/ajax/getwordsbystatus.php?${params.toString()}`);

            if (!response.ok) throw new Error(`HTTP error: ${response.status}`);

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error_msg || 'Stats retrieval unsuccessful.');
            }
            
            handleData(data.payload);
        } catch (error) {
            console.error(error);
            handleError();
        }

        function handleData(data) {
            const count_learned = parseInt(data[0]);
            const count_learning = parseInt(data[1]);
            const count_new = parseInt(data[2]);
            const count_forgotten = parseInt(data[3]);
            const count_total = parseInt(data[4]);

            // build chart
            const ctx = document.getElementById("total-stats-canvas").getContext("2d");

            const chart_data = {
                labels: [
                    'Learned',
                    'Learning',
                    'New',
                    'Forgotten'
                ],
                datasets: [{
                    data: [count_learned, count_learning, count_new, count_forgotten],
                    backgroundColor: [
                        '#3cb371',
                        '#ffa500',
                        '#1e90ff',
                        '#E0115F'
                    ],
                    hoverOffset: 4
                }]
            };

            const noDataPlugin = {
                id: 'noDataPlugin',
                afterDraw: (chart) => {
                    // If no data, show message
                    if (count_total === 0) {
                        const ctx = chart.ctx;
                        const { width, height } = chart;
                        ctx.save();
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.font = '16px Arial';
                        ctx.fillStyle = 'gray';
                        ctx.fillText('No Data Available', width / 2, height / 2);
                        ctx.restore();
                    }
                }
            };

            //create Chart class object
            const chart = new Chart(ctx, {
                type: "doughnut",
                data: chart_data,

                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: count_total, position: 'right' },
                        tooltip: {
                            callbacks: {
                                label: tooltipLabelCallback
                            }
                        }
                    }
                },
                plugins: [noDataPlugin] // Add the plugin here              
            });

            if (count_total) {
                // show legend in table
                $("#learned-count").text(count_learned);
                $("#learned-percentage").text((count_learned / count_total * 100).toFixed(2).toLocaleString('en-US'));

                $("#learning-count").text(count_learning);
                $("#learning-percentage").text((count_learning / count_total * 100).toFixed(2).toLocaleString('en-US'));

                $("#new-count").text(count_new);
                $("#new-percentage").text((count_new / count_total * 100).toFixed(2).toLocaleString('en-US'));

                $("#forgotten-count").text(count_forgotten);
                $("#forgotten-percentage").text((count_forgotten / count_total * 100).toFixed(2).toLocaleString('en-US'));

                $("#total-count").text(count_learned + count_learning + count_new + count_forgotten);
            }
        }

        function handleError() {
            let error_div = "<div class='d-flex align-items-center justify-content-center' " +
                "style='min-height:400px;'><p class='text-danger'>Error: no data to display</p></div>";
            $("#total-stats-canvas").replaceWith(error_div);
        }

        function tooltipLabelCallback(context) {
            const value = context.raw; // Raw value of the slice
            const total = context.dataset.data.reduce((a, b) => a + b, 0); // Sum of all values
            const percentage = ((value / total) * 100).toFixed(1); // Calculate percentage
            return `${context.label}: ${value} (${percentage}%)`;
        }
    } // end drawTotalStats
});
