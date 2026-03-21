/* ================================
   BOOKING PAGE - ImmuniTrack
   js/booking.js
================================ */

const vaxData = {
    "Covid":      ["Pfizer", "Moderna", "AstraZeneca", "Sinovac"],
    "Dengue":     ["Dengvaxia"],
    "Flu":        ["Fluad", "Vaxigrip", "Fluarix"],
    "Chickenpox": ["Varivax", "Varilrix"],
    "Rabies":     ["Verorab", "Rabipur"]
};

const doseGuidelines = {
    "Covid": {
        interval: "21–28 days between doses, booster after 6 months",
        note: "Complete all 3 doses for full protection. Booster is strongly recommended for continued immunity.",
        rows: [
            { dose: "Dose 1",  timing: "Day 0 — Initial dose" },
            { dose: "Dose 2",  timing: "21–28 days after Dose 1" },
            { dose: "Booster", timing: "6 months after Dose 2" },
        ]
    },
    "Dengue": {
        interval: "6 months between each dose",
        note: "Only for individuals confirmed seropositive (prior dengue infection). Consult your doctor before proceeding.",
        rows: [
            { dose: "Dose 1", timing: "Day 0 — Initial dose" },
            { dose: "Dose 2", timing: "6 months after Dose 1" },
            { dose: "Dose 3", timing: "6 months after Dose 2" },
        ]
    },
    "Flu": {
        interval: "Once every year",
        note: "Recommended annually before flu season. First-time recipients under age 9 may need 2 doses.",
        rows: [
            { dose: "Annual Dose", timing: "Once every year, ideally before flu season begins" },
        ]
    },
    "Chickenpox": {
        interval: "4–8 weeks between doses",
        note: "Two doses provide up to 98% protection. Recommended for children 12 months and older.",
        rows: [
            { dose: "Dose 1", timing: "12–15 months of age" },
            { dose: "Dose 2", timing: "4–6 years old (or 4–8 weeks after Dose 1 for adults)" },
        ]
    },
    "Rabies": {
        interval: "Days 0, 3, 7, 14, and 28 post-exposure",
        note: "Post-Exposure Prophylaxis (PEP). Must begin immediately after an animal bite or scratch. Do not delay.",
        rows: [
            { dose: "Dose 1", timing: "Day 0 — As soon as possible after exposure" },
            { dose: "Dose 2", timing: "Day 3" },
            { dose: "Dose 3", timing: "Day 7" },
            { dose: "Dose 4", timing: "Day 14" },
            { dose: "Dose 5", timing: "Day 28" },
        ]
    }
};

/* --------------------------------
   Update brands dropdown
-------------------------------- */
function updateBrands() {
    const category    = document.getElementById('vax_category').value;
    const brandSelect = document.getElementById('vax_brand');

    brandSelect.innerHTML = '<option value="">-- Select Brand --</option>';
    if (vaxData[category]) {
        vaxData[category].forEach(brand => brandSelect.add(new Option(brand, brand)));
    }

    showDoseGuideline(category);
    updateSummary();
}

/* --------------------------------
   Show dose guideline dynamically
-------------------------------- */
function showDoseGuideline(category) {
    const guidelineBox     = document.getElementById('doseGuideline');
    const guidelineContent = document.getElementById('guidelineContent');

    if (!guidelineBox || !guidelineContent) return;

    if (doseGuidelines[category]) {
        const g = doseGuidelines[category];

        const rows = g.rows.map(r => `
            <div class="guideline-row">
                <span class="gdose">${r.dose}</span>
                <span class="gtiming">${r.timing}</span>
            </div>
        `).join('');

        guidelineContent.innerHTML = `
            <p class="guideline-note">${g.note}</p>
            <p class="guideline-interval">⏱ Interval: <strong>${g.interval}</strong></p>
            <div class="guideline-table">${rows}</div>
        `;

        guidelineBox.style.display = 'block';
    } else {
        guidelineBox.style.display = 'none';
    }
}

/* --------------------------------
   Update live summary box
-------------------------------- */
function updateSummary() {
    const nameEl   = document.getElementById('full_name');
    const catEl    = document.getElementById('vax_category');
    const brandEl  = document.getElementById('vax_brand');
    const dateEl   = document.getElementById('booking_date');
    const timeEl   = document.getElementById('booking_time');
    const clinicEl = document.getElementById('clinic');
    const summary  = document.getElementById('bookingSummary');

    if (!catEl || !summary) return;

    const catVal    = catEl.options[catEl.selectedIndex]?.text;
    const timeVal   = timeEl?.options[timeEl.selectedIndex]?.text;
    const clinicVal = clinicEl?.options[clinicEl.selectedIndex]?.text;

    const hasData = catVal && catVal !== '-- Select Category --';

    if (hasData) {
        const sumName   = document.getElementById('sum_name');
        const sumClinic = document.getElementById('sum_clinic');
        const sumVax    = document.getElementById('sum_vax');
        const sumBrand  = document.getElementById('sum_brand');
        const sumDate   = document.getElementById('sum_date');
        const sumTime   = document.getElementById('sum_time');

        if (sumName)   sumName.textContent   = nameEl?.value || '—';
        if (sumClinic) sumClinic.textContent  = (clinicVal && clinicVal !== '-- Select Clinic --') ? clinicVal : '—';
        if (sumVax)    sumVax.textContent     = catVal;
        if (sumBrand)  sumBrand.textContent   = brandEl?.value || '—';
        if (sumDate)   sumDate.textContent    = dateEl?.value
            ? new Date(dateEl.value + 'T00:00:00').toLocaleDateString('en-US', {
                month: 'long', day: 'numeric', year: 'numeric'
              })
            : '—';
        if (sumTime)   sumTime.textContent    = (timeVal && timeVal !== '-- Select Time --') ? timeVal : '—';

        summary.style.display = 'block';
    }
}

/* --------------------------------
   Init — attach all listeners
-------------------------------- */
document.addEventListener('DOMContentLoaded', () => {
    const catEl = document.getElementById('vax_category');
    if (catEl) catEl.addEventListener('change', updateBrands);

    const brandEl = document.getElementById('vax_brand');
    if (brandEl) brandEl.addEventListener('change', updateSummary);

    const clinicEl = document.getElementById('clinic');
    if (clinicEl) clinicEl.addEventListener('change', updateSummary);

    const dateEl = document.getElementById('booking_date');
    if (dateEl) dateEl.addEventListener('change', updateSummary);

    const timeEl = document.getElementById('booking_time');
    if (timeEl) timeEl.addEventListener('change', updateSummary);

    const nameEl = document.getElementById('full_name');
    if (nameEl) nameEl.addEventListener('input', updateSummary);
});