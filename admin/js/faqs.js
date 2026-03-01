document.addEventListener("DOMContentLoaded", function () {
    loadFaqs();
});

function loadFaqs() {
    fetch("php/get_faqs.php")
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                console.error("Server error:", data.message);
                return;
            }
            console.log("Returned data:", data);
            buildAccordion(data.registered, "examAccordion", "exam");
            buildAccordion(data.unregistered, "pmmaAccordion", "pmma");
        })
        .catch(error => {
            console.error("Fetch error:", error);
        });
}

function editFaq(faq_id, category, event) {
    event.stopPropagation();
    // Get current data from DOM
    const item = event.target.closest('.accordion-item');
    const question = item.querySelector('.flex-grow-1').textContent.trim();
    const answer = item.querySelector('.accordion-body').textContent.trim();

    // Populate modal
    document.getElementById('editFaqId').value = faq_id;
    document.getElementById('editFaqQuestion').value = question;
    document.getElementById('editFaqAnswer').value = answer;

    // Show modal
    new bootstrap.Modal(document.getElementById('editFaqModal')).show();
}

function deleteFaq(faq_id, event) {
    event.stopPropagation();
    window.currentDeleteFaqId = faq_id;
    new bootstrap.Modal(document.getElementById('deleteFaqModal')).show();
}

function confirmDelete() {
    if (window.currentDeleteFaqId) {
        fetch('php/delete_faq.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                faq_id: window.currentDeleteFaqId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadFaqs(); // Reload FAQs
                bootstrap.Modal.getInstance(document.getElementById('deleteFaqModal')).hide();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

function saveEditFaq() {
    const faq_id = document.getElementById('editFaqId').value;
    const question = document.getElementById('editFaqQuestion').value.trim();
    const answer = document.getElementById('editFaqAnswer').value.trim();

    if (!question || !answer) {
        alert('Please fill in both fields.');
        return;
    }

    fetch('php/edit_faq.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            faq_id: faq_id,
            question: question,
            answer: answer
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadFaqs(); // Reload FAQs
            bootstrap.Modal.getInstance(document.getElementById('editFaqModal')).hide();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}

function buildAccordion(faqs, accordionId, prefix) {

    const accordion = document.getElementById(accordionId);
    accordion.innerHTML = "";

    if (!faqs || faqs.length === 0) {
        accordion.innerHTML = `<div class="text-muted p-3">No FAQs available.</div>`;
        return;
    }

    faqs.forEach((faq, index) => {

        const collapseId = `${prefix}Collapse_${faq.faq_id}_${index}`;
        const headingId  = `${prefix}Heading_${faq.faq_id}_${index}`;

        const item = `
        <div class="accordion-item">
            <h2 class="accordion-header" id="${headingId}">
                <button class="accordion-button collapsed d-flex align-items-center"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#${collapseId}"
                        aria-expanded="false"
                        aria-controls="${collapseId}">

                    <span class="faq-icon me-2">
                        <i class="bx bx-plus-circle"></i>
                    </span>

                    <span class="flex-grow-1">
                        ${faq.question.trim()}
                    </span>

                    <!-- ACTION BUTTONS -->
                    <span class="faq-actions ms-2 d-flex gap-1"
                          onclick="event.stopPropagation();">

                        <button type="button"
                                class="btn btn-sm btn-light rounded-circle"
                                onclick="event.stopPropagation(); editFaq(${faq.faq_id}, '${faq.category}', event);">
                            <i class="bx bx-edit-alt" style="font-size:13px;"></i>
                        </button>

                        <button type="button"
                                class="btn btn-sm btn-light rounded-circle text-danger"
                                onclick="event.stopPropagation(); deleteFaq(${faq.faq_id}, event);">
                            <i class="bx bx-trash" style="font-size:13px;"></i>
                        </button>

                    </span>

                </button>
            </h2>

            <div id="${collapseId}"
                 class="accordion-collapse collapse"
                 aria-labelledby="${headingId}"
                 data-bs-parent="#${accordionId}">

                <div class="accordion-body">
                    ${faq.answer}
                </div>

            </div>
        </div>
        `;

        accordion.insertAdjacentHTML("beforeend", item);
    });
}