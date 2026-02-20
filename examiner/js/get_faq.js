document.addEventListener("DOMContentLoaded", function () {
    loadFAQs();
});

function loadFAQs() {
    fetch("php/get_faqs.php")
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById("faqContainer");
            container.innerHTML = "";

            if (!data.length) {
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
        });
}

// Prevents HTML injection / formatting issues
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
    const toggles = document.querySelectorAll(".faq-toggle");

    toggles.forEach(toggle => {
        toggle.addEventListener("click", function () {
            const wrapper = this.nextElementSibling;
            wrapper.classList.toggle("open");
            this.querySelector(".faq-chevron").classList.toggle("rotate");
        });
    });
}