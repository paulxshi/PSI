document.addEventListener("DOMContentLoaded", function () {
<<<<<<< HEAD

    // ── Element References ────────────────────────────────────────────────────
    const form         = document.getElementById("scheduleForm");
    const publishBtn   = document.getElementById("publishBtn");
    const confirmBtn   = document.getElementById("confirmCreateBtn");
    const mealList     = document.getElementById("mealList");
    const mealsInput   = document.getElementById("mealsInput");
    const saveMealBtn  = document.getElementById("saveMealBtn");

    const regionSelect = document.getElementById("exam_region");
    const venueInput   = document.getElementById("exam_area");
    const dateInput    = document.getElementById("exam_date");
    const limitInput   = document.getElementById("exam_limit");
    const priceInput   = document.getElementById("exam_price");

    const previewRegion = document.getElementById("previewRegion");
    const previewVenue  = document.getElementById("previewVenue");
    const previewDate   = document.getElementById("previewDate");
    const previewLimit  = document.getElementById("previewLimit");
    const previewPrice  = document.getElementById("previewPrice");
    const previewMeals  = document.getElementById("previewMeals");

    const mealNameInput  = document.getElementById("mealName");
    const mealPriceInput = document.getElementById("mealPrice");

    const confirmModal = new bootstrap.Modal(document.getElementById("confirmScheduleModal"));
    const mealModal    = new bootstrap.Modal(document.getElementById("mealModal"));

    // ── State ─────────────────────────────────────────────────────────────────
    let meals = [];

    // ── Flatpickr ─────────────────────────────────────────────────────────────
    flatpickr("#exam_date", {
        dateFormat: "Y-m-d",
        minDate: "today",
        altInput: true,
        altFormat: "F j, Y",
        allowInput: true,
        onChange: function () {
            updatePreview();
        }
    });

    // ── Helpers ───────────────────────────────────────────────────────────────
    function formatPeso(value) {
        return "₱" + Number(value).toLocaleString("en-PH", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // ── Preview ───────────────────────────────────────────────────────────────
    function updatePreview() {
        const region = regionSelect.value;
        const venue  = venueInput.value.trim();
        const date   = dateInput.value;
        const limit  = limitInput.value;
        const price  = priceInput.value;

        previewRegion.textContent = region || "—";
        previewVenue.textContent  = venue  || "—";
        previewLimit.textContent  = limit  || "—";
        previewPrice.textContent  = price  ? formatPeso(price) : "—";

        if (date) {
            const dateObj = new Date(date + "T00:00:00");
            previewDate.textContent = dateObj.toLocaleDateString("en-US", {
                year: "numeric",
                month: "long",
                day: "numeric"
            });
        } else {
            previewDate.textContent = "—";
        }

        if (meals.length > 0) {
            previewMeals.innerHTML = meals
                .map(meal => `<div>${meal.name} - ${formatPeso(meal.price)}</div>`)
                .join("");
        } else {
            previewMeals.textContent = "—";
        }

        publishBtn.disabled = !(region && venue && date && limit && price);
    }

    // ── Meal List Render ──────────────────────────────────────────────────────
    function renderMeals() {
        if (meals.length === 0) {
            mealList.innerHTML = '<span class="meal-empty">No meals added yet.</span>';
        } else {
            mealList.innerHTML = meals.map((meal, index) => `
                <div class="meal-card d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <div class="fw-semibold">${meal.name}</div>
                        <small class="text-muted">${formatPeso(meal.price)}</small>
                    </div>
                    <button
                        type="button"
                        class="btn btn-sm btn-outline-danger remove-meal-btn"
                        data-index="${index}"
                    >
                        Remove
                    </button>
                </div>
            `).join("");
        }

        // Keep hidden input in sync — this is what gets submitted
        mealsInput.value = JSON.stringify(meals);

        updatePreview();
    }

    // ── Save Meal ─────────────────────────────────────────────────────────────
    saveMealBtn.addEventListener("click", function () {
        const mealName  = mealNameInput.value.trim();
        const mealPrice = mealPriceInput.value.trim();

        if (!mealName || !mealPrice || isNaN(parseFloat(mealPrice))) {
            alert("Please enter a valid meal name and price.");
            return;
        }

        meals.push({
            name:  mealName,
            price: parseFloat(mealPrice)
        });

        renderMeals();

        mealNameInput.value  = "";
        mealPriceInput.value = "";

        mealModal.hide();
    });

    // ── Remove Meal ───────────────────────────────────────────────────────────
    mealList.addEventListener("click", function (e) {
        if (e.target.classList.contains("remove-meal-btn")) {
            const index = Number(e.target.getAttribute("data-index"));
            meals.splice(index, 1);
            renderMeals();
        }
    });

    // ── Live Preview on Input ─────────────────────────────────────────────────
    [regionSelect, venueInput, dateInput, limitInput, priceInput].forEach(el => {
        el.addEventListener("input", updatePreview);
        el.addEventListener("change", updatePreview);
    });

    // ── Publish Button — Open Confirmation Modal ──────────────────────────────
    publishBtn.addEventListener("click", function (e) {
        e.preventDefault();

        const region = regionSelect.value;
        const venue  = venueInput.value.trim();
        const date   = dateInput.value;
        const limit  = limitInput.value;
        const price  = priceInput.value;

        if (!region || !venue || !date || !limit || !price) {
            alert("Please complete all required fields.");
=======
    const form = document.getElementById("scheduleForm");
    const publishBtn = document.getElementById("publishBtn");
    const modalElement = document.getElementById("confirmScheduleModal");
    const confirmBtn = document.getElementById("confirmCreateBtn");
    const confirmModal = new bootstrap.Modal(modalElement);

    const mealModalElement = document.getElementById("mealModal");
    const mealModal = new bootstrap.Modal(mealModalElement);
    const saveMealBtn = document.getElementById("saveMealBtn");

    const regionInput = document.getElementById("exam_region");
    const venueInput = document.getElementById("exam_area");
    const dateInput = document.getElementById("exam_date");
    const limitInput = document.querySelector("[name='exam_limit']");
    const priceInput = document.querySelector("[name='exam_price']");

    const previewRegion = document.getElementById("previewRegion");
    const previewVenue = document.getElementById("previewVenue");
    const previewDate = document.getElementById("previewDate");
    const previewLimit = document.getElementById("previewLimit");
    const previewPrice = document.getElementById("previewPrice");
    const previewMeals = document.getElementById("previewMeals");

    const mealTypeInput = document.getElementById("mealType");
    const mealPriceInput = document.getElementById("mealPrice");
    const mealList = document.getElementById("mealList");
    const mealsInput = document.getElementById("mealsInput");

    let meals = [];

    function formatPeso(value) {
        return `₱${Number(value).toLocaleString("en-PH", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        })}`;
    }

    function updatePreview() {
        const region = regionInput.value;
        const venue = venueInput.value;
        const date = dateInput.value;
        const limit = limitInput.value;
        const price = priceInput.value;

        previewRegion.textContent = region || "—";
        previewVenue.textContent = venue || "—";
        previewLimit.textContent = limit || "—";
        previewPrice.textContent = price ? formatPeso(price) : "—";

        if (date) {
            const dateObj = new Date(date + "T00:00:00");
            previewDate.textContent = dateObj.toLocaleDateString("en-US", {
                year: "numeric",
                month: "long",
                day: "numeric"
            });
        } else {
            previewDate.textContent = "—";
        }

        if (meals.length > 0) {
            previewMeals.innerHTML = meals
                .map(meal => `<div>${meal.type} - ${formatPeso(meal.price)}</div>`)
                .join("");
        } else {
            previewMeals.textContent = "—";
        }

        const isComplete = region && venue && date && limit && price;
        publishBtn.disabled = !isComplete;
    }

    function renderMeals() {
        if (meals.length === 0) {
            mealList.innerHTML = `<span class="text-muted small">No meals added yet.</span>`;
        } else {
            mealList.innerHTML = meals.map((meal, index) => `
                <div class="d-flex justify-content-between align-items-center border rounded-3 bg-white px-3 py-2 mb-2">
                    <div>
                        <div class="fw-semibold">${meal.type}</div>
                        <small class="text-muted">${formatPeso(meal.price)}</small>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-meal-btn" data-index="${index}">
                        Remove
                    </button>
                </div>
            `).join("");
        }

        mealsInput.value = JSON.stringify(meals);
        updatePreview();
    }

    saveMealBtn.addEventListener("click", function () {
        const type = mealTypeInput.value;
        const price = mealPriceInput.value;

        if (!type || !price) {
            alert("Please complete meal type and meal price.");
            return;
        }

        meals.push({
            type: type,
            price: parseFloat(price)
        });

        renderMeals();

        mealTypeInput.value = "";
        mealPriceInput.value = "";

        mealModal.hide();
    });

    mealList.addEventListener("click", function (e) {
        if (e.target.classList.contains("remove-meal-btn")) {
            const index = Number(e.target.dataset.index);
            meals.splice(index, 1);
            renderMeals();
        }
    });

    [regionInput, venueInput, dateInput, limitInput, priceInput].forEach(el => {
        el.addEventListener("input", updatePreview);
        el.addEventListener("change", updatePreview);
    });

    publishBtn.addEventListener("click", function (e) {
        e.preventDefault();

        const region = regionInput.value;
        const venue = venueInput.value;
        const date = dateInput.value;
        const limit = limitInput.value;
        const price = priceInput.value;

        if (!region || !venue || !date || !limit || !price) {
            alert("Please complete all fields.");
>>>>>>> c2e8593a1ad4020f5eae02badf0b05bef60e8cf1
            return;
        }

        confirmModal.show();
    });

<<<<<<< HEAD
    // ── Confirm Submit ────────────────────────────────────────────────────────
=======
>>>>>>> c2e8593a1ad4020f5eae02badf0b05bef60e8cf1
    confirmBtn.addEventListener("click", function () {
        if (confirmBtn.disabled) return;
        confirmBtn.disabled = true;
        confirmModal.hide();

        publishBtn.disabled = true;
        publishBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';

        // Sync hidden meals input one final time before building FormData
        mealsInput.value = JSON.stringify(meals);

        const formData = new FormData(form);
        formData.set("meals", JSON.stringify(meals));

        fetch("php/save_schedule.php", {
            method: "POST",
            body: formData,
            credentials: "same-origin"
<<<<<<< HEAD
        })
        .then(response => {
            if (!response.ok) throw new Error("Server response was not OK");
            return response.json();
=======
>>>>>>> c2e8593a1ad4020f5eae02badf0b05bef60e8cf1
        })
        .then(data => {
            if (data.success) {
                alert(data.message || "Schedule successfully created!");
<<<<<<< HEAD
                window.location.href = "managesched.html";
=======
                window.location.href = "dashboard.html";
>>>>>>> c2e8593a1ad4020f5eae02badf0b05bef60e8cf1
            } else {
                alert(data.message || "Failed to create schedule. Please try again.");
                publishBtn.disabled = false;
                publishBtn.textContent = "Publish Schedule";
                confirmBtn.disabled = false;
            }
        })
        .catch(error => {
<<<<<<< HEAD
            console.error("Schedule submit error:", error);
=======
            console.error("Error creating schedule:", error);
>>>>>>> c2e8593a1ad4020f5eae02badf0b05bef60e8cf1
            alert("Network error. Please try again.");
            publishBtn.disabled = false;
            publishBtn.textContent = "Publish Schedule";
            confirmBtn.disabled = false;
        });
    });

<<<<<<< HEAD
    // ── Form Reset ────────────────────────────────────────────────────────────
=======
>>>>>>> c2e8593a1ad4020f5eae02badf0b05bef60e8cf1
    form.addEventListener("reset", function () {
        setTimeout(() => {
            meals = [];
            renderMeals();

            previewRegion.textContent = "—";
<<<<<<< HEAD
            previewVenue.textContent  = "—";
            previewDate.textContent   = "—";
            previewLimit.textContent  = "—";
            previewPrice.textContent  = "—";
            previewMeals.textContent  = "—";

            publishBtn.disabled    = true;
            publishBtn.textContent = "Publish Schedule";

            mealNameInput.value  = "";
            mealPriceInput.value = "";
        }, 0);
    });

    // ── Init ──────────────────────────────────────────────────────────────────
=======
            previewVenue.textContent = "—";
            previewDate.textContent = "—";
            previewLimit.textContent = "—";
            previewPrice.textContent = "—";
            previewMeals.textContent = "—";

            publishBtn.disabled = true;
        }, 0);
    });

>>>>>>> c2e8593a1ad4020f5eae02badf0b05bef60e8cf1
    renderMeals();
    updatePreview();
});