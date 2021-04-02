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
 * along with aprelendo.  If not, see <http://www.gnu.org/licenses/>.
 */

$(document).ready(function() {
    $("#btn-premium-1m, #btn-premium-3m, #btn-premium-6m, #btn-premium-1y").on("click", function () {
        var $premium_period = $("#lbl-premium-period").clone();

        $("#lbl-premium-price").text("$" + $(this).data("price"));
        $("#lbl-premium-price").append($premium_period);
        $("#lbl-premium-period").text("/" + $(this).text() + " pass");

        $("#inp-item-nbr").val($(this).data("item-nbr"));
        $(this).siblings().removeClass("active");
        $(this).addClass("active");
    });
});
