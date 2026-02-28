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
            const container = document.getElementById("faqAccordion");
            container.innerHTML = "";

            if (!Array.isArray(data) || !data.length) {
                container.innerHTML = "<p class='text-muted'>No FAQs available.</p>";
                return;
            }

            data.forEach((faq, index) => {
                const accordionItem = document.createElement("div");
                accordionItem.classList.add("accordion-item");

                accordionItem.innerHTML = `
                    <h2 class="accordion-header" id="heading${index}">
                        <button class="accordion-button ${index !== 0 ? 'collapsed' : ''}" 
                                type="button" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#collapse${index}" 
                                aria-expanded="${index === 0 ? 'true' : 'false'}" 
                                aria-controls="collapse${index}">
                            ${escapeHTML(faq.question)}
                        </button>
                    </h2>
                    <div id="collapse${index}" 
                         class="accordion-collapse collapse ${index === 0 ? 'show' : ''}" 
                         aria-labelledby="heading${index}" 
                         data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            ${escapeHTML(faq.answer)}
                        </div>
                    </div>
                `;

                container.appendChild(accordionItem);
            });
        })
        .catch(error => {
            console.error("Error loading FAQs:", error);
            const container = document.getElementById("faqAccordion");
            container.innerHTML = "<p class='text-danger'>Failed to load FAQs. Please try again later.</p>";
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
