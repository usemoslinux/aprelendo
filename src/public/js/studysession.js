// SPDX-License-Identifier: GPL-3.0-or-later

const StudySession = (() => {
    const DEFAULT_ANSWERS = [
        ["0", 0, "bg-success", "Excellent"],
        ["1", 0, "bg-warning", "Partial"],
        ["2", 0, "bg-primary", "Fuzzy"],
        ["3", 0, "bg-danger", "No recall"]
    ];

    function create({
        answers = DEFAULT_ANSWERS,
        card_filter = {},
        on_card_ready,
        on_empty,
        on_complete
    }) {
        let words = [];
        let current_card_index = 0;
        let max_cards = 10;

        function setAnswerButtonsDisabled(is_disabled) {
            $(".btn-answer").prop("disabled", is_disabled);
        }

        function setLayoutState(layout_state) {
            const is_empty = layout_state === "empty";

            $("#study-column")
                .toggleClass("col-md-12", is_empty)
                .toggleClass("col-md-6", !is_empty);
            $("#review-column").toggleClass("d-none", is_empty);
        }

        function adaptCardStyleToWordStatus(status) {
            const $card = $("#study-card");
            const $card_header = $("#study-card-header");

            $card.removeClass(function (index, class_name) {
                return (class_name.match(/\bborder-\S+/g) || []).join(" ");
            });
            $card_header.removeClass(function (index, class_name) {
                return (class_name.match(/\bborder-\S+|\bbg-\S+/g) || []).join(" ");
            });

            switch (Number(status)) {
                case 0:
                    $card.addClass("border-success");
                    $card_header.addClass("bg-gradient bg-success border-success");
                    break;
                case 1:
                    $card.addClass("border-warning");
                    $card_header.addClass("bg-gradient bg-warning border-warning");
                    break;
                case 2:
                    $card.addClass("border-primary");
                    $card_header.addClass("bg-gradient bg-primary border-primary");
                    break;
                case 3:
                    $card.addClass("border-danger");
                    $card_header.addClass("bg-gradient bg-danger border-danger");
                    break;
                default:
                    $card.addClass("border-secondary");
                    $card_header.addClass("bg-gradient bg-secondary border-secondary");
                    break;
            }
        }

        function updateLiveProgressBar() {
            const percentage = Math.round((current_card_index + 1) / max_cards * 100);
            $("#live-progress-bar")
                .css("width", percentage + "%")
                .attr("aria-valuenow", percentage);
        }

        function buildResultsTable() {
            const table_header = `
                <table class="table table-bordered table-striped text-end small mx-auto mt-3"
                    aria-describedby="" style="max-width: 550px">
                    <thead>
                        <tr class="table-light">
                            <th>Word</th>
                            <th>Recall level</th>
                        </tr>
                    </thead>
                    <tbody>`;
            let table_rows = "";

            words.forEach((word) => {
                table_rows += "<tr>"
                    + '<td><a class="word fw-bold">' + encodeHtml(word.word) + "</a></td>"
                    + '<td><span class="word-description ' + answers[word.status][2] + '">'
                    + answers[word.status][3] + "</span></td>"
                    + "</tr>";
            });

            return table_header + table_rows + "</tbody></table>";
        }

        function showEmptyDeckState() {
            setLayoutState("empty");
            $("#study-card-header").html('<h4 id="study-no-cards" class="my-0 fw-bold">Sorry, no cards to practice</h4>');
            adaptCardStyleToWordStatus(3);
            $("#card-counter").addClass("d-none");
            on_empty();
        }

        function showCompleteState() {
            setLayoutState("complete");
            $("#study-card-word-title")
                .removeClass("placeholder w-50 rounded")
                .text("Congratulations!");
            $("#study-card-freq-badge").addClass("d-none");
            adaptCardStyleToWordStatus(0);

            let progress_html = "";
            for (const answer of answers) {
                const subtotal = answer[1];
                const percentage = subtotal / max_cards * 100;
                progress_html += `
                    <div class="progress-bar ${answer[2]}"
                        role="progressbar"
                        aria-valuenow="${percentage}"
                        aria-valuemin="0"
                        aria-valuemax="100"
                        style="width: ${percentage}%"
                        title="${answer[3]}: ${subtotal} answer(s)">
                        ${Math.round(percentage)} %
                    </div>`;
            }

            $("#study-card-body").addClass("d-flex flex-column justify-content-center");
            $("#study-card-body").after(`
                <div class="card-footer small">
                    To continue, press F5. Keep your study sessions short and take rest intervals.
                </div>
            `);
            $("#answer-card-title").text("Review your answers");
            $("#answer-card-body").html(`
                <div class="progress mx-auto mt-3 fw-bold" style="height: 25px; max-width: 550px">
                    ${progress_html}
                </div>
                ${buildResultsTable()}
            `);
            $("#card-counter").addClass("d-none");
            $("#answer-card .card-footer").addClass("d-none");
            on_complete();
            scrollToPageTop();
        }

        async function load() {
            try {
                const form_data = new URLSearchParams({ limit: max_cards, ...card_filter });
                const response = await fetch("/ajax/getcards.php", {
                    method: "POST",
                    body: form_data
                });

                if (!response.ok) {
                    throw new Error(`HTTP error: ${response.status}`);
                }

                const data = await response.json();
                if (!data.success) {
                    throw new Error(data.error_msg || "Failed to fetch list of cards.");
                }

                if (data.payload.length === 0) {
                    showEmptyDeckState();
                    return;
                }

                words = data.payload.map((item) => ({
                    ...item,
                    word: item.word.replace(/\r?\n|\r/g, " ")
                }));
                max_cards = Math.min(words.length, max_cards);
                setLayoutState("active");
                await on_card_ready(getCurrentCard());
            } catch (error) {
                console.error(error);
                alert(`Oops! ${error.message}`);
            }
        }

        async function saveAnswer(answer) {
            const word = getCurrentCard();
            if (!word || !answers[answer]) {
                return false;
            }

            setAnswerButtonsDisabled(true);

            try {
                const response = await fetch("/ajax/updatecard.php", {
                    method: "POST",
                    body: new URLSearchParams({ word: word.word, answer: answer })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error: ${response.status}`);
                }

                const data = await response.json();
                if (!data.success) {
                    throw new Error(data.error_msg || "Failed to update card status.");
                }

                answers[answer][1]++;
                word.status = answer;
                current_card_index++;

                if (current_card_index >= max_cards) {
                    showCompleteState();
                    return true;
                }

                await on_card_ready(getCurrentCard());
                scrollToPageTop();
                return true;
            } catch (error) {
                setAnswerButtonsDisabled(false);
                console.error(error);
                alert(`Oops! ${error.message}`);
                return false;
            }
        }

        function getCurrentCard() {
            return words[current_card_index] || null;
        }

        function recordAnswer(answer) {
            const word = getCurrentCard();
            if (!word || !answers[answer]) {
                return false;
            }

            answers[answer][1]++;
            word.status = answer;
            current_card_index++;
            return current_card_index >= max_cards;
        }

        return {
            adaptCardStyleToWordStatus,
            getCurrentCard,
            getCurrentCardIndex: () => current_card_index,
            getMaxCards: () => max_cards,
            load,
            recordAnswer,
            saveAnswer,
            setAnswerButtonsDisabled,
            showCompleteState,
            updateLiveProgressBar
        };
    }

    function encodeHtml(value) {
        return String(value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    return { create, encodeHtml };
})();
