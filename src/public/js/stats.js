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
                const nr_learned   = parseInt(data[0]["count"]);
                const nr_learning  = parseInt(data[1]["count"]);
                const nr_new       = parseInt(data[2]["count"]);
                const nr_forgotten = parseInt(data[3]["count"]);
                const nr_total     = nr_learned + nr_learning + nr_new + nr_forgotten;

                // build chart
                const ctx = document.getElementById("total-stats-canvas").getContext("2d");

                //create Chart class object
                new Chart(ctx, {
                    type: "bar",
                    data: {
                        labels: ['Words'],
                        datasets: [
                            {
                                label: 'Learned',
                                data: [nr_learned],
                                backgroundColor: '#3cb371'
                            }, {
                                label: 'Learning',
                                data: [nr_learning],
                                backgroundColor: '#ffa500'
                            }, {
                                label: 'New',
                                data: [nr_new],
                                backgroundColor: '#1e90ff'
                            }, {
                                label: 'Forgotten',
                                data: [nr_forgotten],
                                backgroundColor: '#ff6347'
                            }
                        ]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        scales: {
                            x: {
                                stacked: true
                            },
                            y: {
                                stacked: true,
                                display: false
                            }
                        }
                    }                
                });

                // show legend in table
                $("#learned-count").text(nr_learned);
                $("#learned-percentage").text((nr_learned / nr_total * 100).toFixed(2).toLocaleString('en-US'));

                $("#learning-count").text(nr_learning);
                $("#learning-percentage").text((nr_learning / nr_total * 100).toFixed(2).toLocaleString('en-US'));

                $("#new-count").text(nr_new);
                $("#new-percentage").text((nr_new / nr_total * 100).toFixed(2).toLocaleString('en-US'));

                $("#forgotten-count").text(nr_forgotten);
                $("#forgotten-percentage").text((nr_forgotten / nr_total * 100).toFixed(2).toLocaleString('en-US'));

                $("#total-count").text(nr_learned + nr_learning + nr_new + nr_forgotten);

            })
            .fail(function () {
                let error_div = "<div class='d-flex align-items-center justify-content-center' " +
                    "style='min-height:400px;'><p class='text-danger'>Error: no data to display</p></div>";
                $("#total-stats-canvas").replaceWith(error_div);
            });

    } // end drawTotalStats

});
