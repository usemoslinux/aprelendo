/**
 * Copyright (C) 2018 Pablo Castagnino
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
 * along with aprelendo.  If not, see <http://www.gnu.org/licenses/>.
 */

$(document).ready(function () {
    // get data to feed chart
    var created = [],
        modified = [],
        learned = [],
        forgotten = [];

    $.ajax({
            type: "GET",
            url: "db/getstats.php",
            async: false,
            //data: "data",
            dataType: "json"
        })
        .done(function (data) {
            //alert(data);
            created = data['created'];
            reviewed = data['modified'];
            learned = data['learned'];
            forgotten = data['forgotten'];
        })
        .fail(function () {

        });

    // build chart
    // color scheme: { blue: new; green: learned; yellow: learning; red: relearning }
    var ctx = document.getElementById("myChart").getContext("2d");
    var myChart = new Chart(ctx, {
        type: "line",
        data: {
            labels: ["6 days ago", "5 days ago", "4 days ago", "3 days ago", "2 days ago", "Yesterday", "Today"],
            datasets: [{
                    label: "New",
                    data: created,
                    backgroundColor: "#1e90ff", //"rgba(33,150,243,0.4)" // blue
                    borderColor: "#1e90ff",
                    fill: false
                },
                {
                    label: "Reviewed",
                    data: reviewed,
                    backgroundColor: "#ffa500", //"rgba(255,235,59,0.4)" // yellow
                    borderColor: "#ffa500",
                    fill: false
                },
                {
                    label: "Learned",
                    data: learned,
                    backgroundColor: "#3cb371", //"rgba(76,175,80,0.4)" // green
                    borderColor: "#3cb371",
                    fill: false
                },
                {
                    label: "Forgotten",
                    data: forgotten,
                    backgroundColor: "#ff6347", //"rgba(244,67,54,0.4)" // red
                    borderColor: "#ff6347",
                    fill: false
                }
            ]
        },
        options: {
            title: {
                display: true,
                text: 'Your progress this week'
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        userCallback: function (label, index, labels) {
                            // when the floored value is the same as the value we have a whole number
                            if (Math.floor(label) === label) {
                                return label;
                            }

                        },
                    },
                    scaleLabel: {
                        display: true,
                        labelString: 'Number of words',
                    }
                }]
            }
        }
    });
});