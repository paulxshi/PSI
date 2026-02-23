document.addEventListener("DOMContentLoaded", function () {
    loadFAQs();
});

function loadFAQs() {
    fetch("php/get_faqs.php")
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error: ${response.status}`);
            return response.json();
        })
        .then(data => {
            const container = document.getElementById("faqContainer");
            container.innerHTML = "";

            if (!Array.isArray(data) || !data.length) {
                container.innerHTML = "<p class='text-muted'>No FAQs available.</p>";
                return;
            }

            data.forEach(faq => {
                const faqRow = document.createElement("div");
                faqRow.classList.add("faq-row");

                faqRow.innerHTML = `
                    <button class="faq-toggle">
                        <div class="faq-left">
                            <div class="faq-icon"><i class="bx bx-info-circle"></i></div>
                            <span>${escapeHTML(faq.question)}</span>
                        </div>
                        <i class="bx bx-chevron-down faq-chevron"></i>
                    </button>

                    <div class="faq-content-wrapper">
                        <div class="faq-content">
                            ${escapeHTML(faq.answer)}
                        </div>
                    </div>
                `;

                container.appendChild(faqRow);
            });

            activateFAQToggle();
        })
        .catch(error => {
            console.error("Error loading FAQs:", error);
            document.getElementById("faqContainer").innerHTML =
                "<p class='text-danger'>Failed to load FAQs. Please try again later.</p>";
        });
}

function escapeHTML(str) {
    if (!str) return "";
    return str.replace(/[&<>'"]/g, function(tag) {
        const chars = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            "'": '&#39;',
            '"': '&quot;'
        };
        return chars[tag] || tag;
    });
}

function activateFAQToggle() {
    const container = document.getElementById("faqContainer");

    container.addEventListener("click", function (e) {
        const toggle = e.target.closest(".faq-toggle");
        if (!toggle) return;

        const clickedWrapper = toggle.nextElementSibling;
        const isAlreadyOpen = clickedWrapper.classList.contains("open");

        // Collapse all open FAQs
        container.querySelectorAll(".faq-content-wrapper.open").forEach(wrapper => {
            wrapper.classList.remove("open");
            wrapper.previousElementSibling.querySelector(".faq-chevron").classList.remove("rotate");
        });

        // If the clicked one was not already open, open it
        if (!isAlreadyOpen) {
            clickedWrapper.classList.add("open");
            toggle.querySelector(".faq-chevron").classList.add("rotate");
        }
    });
}