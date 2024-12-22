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
    // get data to feed chart

    drawTotalStats();

    function drawTotalStats() {
        $.ajax({
            type: "GET",
            url: "ajax/getstats.php",
            data: { type: "words" },
            dataType: "json"
        })
            .done(function (data) {
                const count_learned   = parseInt(data[0]);
                const count_learning  = parseInt(data[1]);
                const count_new       = parseInt(data[2]);
                const count_forgotten = parseInt(data[3]);
                const count_total     = parseInt(data[4]);

                // build chart
                const ctx = document.getElementById("total-stats-canvas").getContext("2d");

                const chart_data = {
                    labels: ['Words'],
                    datasets: [
                        {
                            label: 'Learned',
                            data: [count_learned],
                            backgroundColor: '#3cb371'
                        }, {
                            label: 'Learning',
                            data: [count_learning],
                            backgroundColor: '#ffa500'
                        }, {
                            label: 'New',
                            data: [count_new],
                            backgroundColor: '#1e90ff'
                        }, {
                            label: 'Forgotten',
                            data: [count_forgotten],
                            backgroundColor: '#ff6347'
                        }
                    ]
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
                const total_stats_chart = new Chart(ctx, {
                    type: "bar",
                    data: chart_data,
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        scales: {
                            x: {
                                stacked: true,
                                max: count_total || 10
                            },
                            y: {
                                stacked: true,
                                display: false
                            }
                        },
                        plugins: {
                            legend: { display: true },
                        }
                    },
                    plugins: [noDataPlugin] // Add the plugin here              
                });

                if (count_total === 0) {
                    $("#learned-count").text("0");
                    $("#learned-percentage").text("0");
                
                    $("#learning-count").text("0");
                    $("#learning-percentage").text("0");
                
                    $("#new-count").text("0");
                    $("#new-percentage").text("0");
                
                    $("#forgotten-count").text("0");
                    $("#forgotten-percentage").text("0");
                
                    $("#total-count").text("0");
                } else {
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
            })
            .fail(function () {
                let error_div = "<div class='d-flex align-items-center justify-content-center' " +
                    "style='min-height:400px;'><p class='text-danger'>Error: no data to display</p></div>";
                $("#total-stats-canvas").replaceWith(error_div);
            });

    } // end drawTotalStats

});
