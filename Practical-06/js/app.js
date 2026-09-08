/**
 * Practical 6: Rendering External JSON Data using Fetch API, Search, Filter & Pagination
 * Course: ITUE203 Web Development Frameworks
 */

// Application State
const state = {
    currentTab: 'events', // 'events' | 'students' | 'faqs'
    rawDatasets: {
        events: [],
        students: [],
        faqs: []
    },
    filteredData: [],
    searchQuery: '',
    categoryFilter: 'ALL',
    sortCriteria: 'default',
    currentPage: 1,
    pageSize: 6,
    isOffline: false
};

// Dependent Dropdown Data Map (Intermediate Extension)
const ACADEMIC_TAXONOMY = {
    "FTE (Faculty of Technology & Engineering)": {
        "CSPIT": ["B.Tech Information Technology", "B.Tech Computer Engineering", "B.Tech Electronics & Comm"],
        "DEPSTAR": ["B.Tech Computer Science & Eng", "B.Tech Information Technology", "B.Tech Artificial Intelligence"]
    },
    "FAS (Faculty of Applied Sciences)": {
        "PDPIAS": ["B.Sc Data Science", "M.Sc Information Technology", "B.Sc Biotechnology"]
    },
    "FMS (Faculty of Management Studies)": {
        "I2IM": ["BBA", "MBA - Information Technology", "MBA - Finance"]
    }
};

document.addEventListener('DOMContentLoaded', () => {
    initTabs();
    initControls();
    initDependentDropdowns();
    loadDataset(state.currentTab);
});

/* ==========================================================================
   1. TABS MANAGEMENT
   ========================================================================== */
function initTabs() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            tabBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            state.currentTab = btn.getAttribute('data-tab');
            state.currentPage = 1;
            state.searchQuery = '';
            state.categoryFilter = 'ALL';
            state.sortCriteria = 'default';

            document.getElementById('searchInput').value = '';
            document.getElementById('sortSelect').value = 'default';

            loadDataset(state.currentTab);
        });
    });
}

/* ==========================================================================
   2. DATA FETCHING WITH LOCALSTORAGE CACHE (Advanced Extension)
   ========================================================================== */
async function loadDataset(datasetName) {
    const displayContainer = document.getElementById('dataDisplay');
    const statusText = document.getElementById('cacheStatusText');
    const cacheKey = `studenthub_cache_${datasetName}`;
    const cacheTimeKey = `studenthub_cache_time_${datasetName}`;

    showLoading();

    try {
        // Attempt Fetch API network request
        const response = await fetch(`data/${datasetName}.json`);
        if (!response.ok) throw new Error(`HTTP Error: ${response.status} ${response.statusText}`);

        const data = await response.json();
        state.rawDatasets[datasetName] = data;

        // Cache in LocalStorage for offline display
        try {
            localStorage.setItem(cacheKey, JSON.stringify(data));
            localStorage.setItem(cacheTimeKey, new Date().toLocaleTimeString());
        } catch (e) {
            console.warn('LocalStorage quota exceeded or unavailable', e);
        }

        statusText.innerHTML = `🟢 Live Dataset Synchronized (${data.length} records). Cached locally.`;
        populateFilterDropdown(datasetName, data);
        applyFiltersAndRender();

    } catch (networkError) {
        console.warn('Network fetch failed. Checking local cache...', networkError);

        // Fallback to LocalStorage offline cache
        const cachedRaw = localStorage.getItem(cacheKey);
        const cachedTime = localStorage.getItem(cacheTimeKey);

        if (cachedRaw) {
            const cachedData = JSON.parse(cachedRaw);
            state.rawDatasets[datasetName] = cachedData;
            statusText.innerHTML = `🟡 Offline Mode: Rendering cached data from ${cachedTime || 'earlier session'}.`;
            populateFilterDropdown(datasetName, cachedData);
            applyFiltersAndRender();
        } else {
            displayContainer.innerHTML = `
                <div class="empty-state">
                    <h3>⚠️ Error Fetching Dataset</h3>
                    <p style="color:var(--danger);">${networkError.message}</p>
                    <p style="margin-top:10px;">Please ensure you are running this file via a local web server (e.g., Live Server or Apache) to allow JSON fetch.</p>
                </div>
            `;
            statusText.innerHTML = `🔴 Network unreachable and no offline cache available.`;
        }
    }
}

function showLoading() {
    const displayContainer = document.getElementById('dataDisplay');
    displayContainer.innerHTML = `
        <div class="spinner-container">
            <div class="spinner"></div>
            <p>Fetching JSON data via Fetch API...</p>
        </div>
    `;
}

/* ==========================================================================
   3. CONTROLS: SEARCH, FILTER, SORT & PAGINATION
   ========================================================================== */
function initControls() {
    const searchInput = document.getElementById('searchInput');
    const categorySelect = document.getElementById('categoryFilter');
    const sortSelect = document.getElementById('sortSelect');
    const pageSizeSelect = document.getElementById('pageSizeSelect');

    searchInput.addEventListener('input', (e) => {
        state.searchQuery = e.target.value.toLowerCase().trim();
        state.currentPage = 1;
        applyFiltersAndRender();
    });

    categorySelect.addEventListener('change', (e) => {
        state.categoryFilter = e.target.value;
        state.currentPage = 1;
        applyFiltersAndRender();
    });

    sortSelect.addEventListener('change', (e) => {
        state.sortCriteria = e.target.value;
        applyFiltersAndRender();
    });

    pageSizeSelect.addEventListener('change', (e) => {
        state.pageSize = parseInt(e.target.value, 10);
        state.currentPage = 1;
        applyFiltersAndRender();
    });
}

function populateFilterDropdown(datasetName, data) {
    const categorySelect = document.getElementById('categoryFilter');
    categorySelect.innerHTML = '<option value="ALL">All Categories / Departments</option>';

    const categories = new Set();
    data.forEach(item => {
        if (item.category) categories.add(item.category);
        if (item.department) categories.add(item.department);
    });

    categories.forEach(cat => {
        const option = document.createElement('option');
        option.value = cat;
        option.textContent = cat;
        categorySelect.appendChild(option);
    });
}

function applyFiltersAndRender() {
    const currentData = state.rawDatasets[state.currentTab] || [];

    // 1. FILTER: Search query & Category
    state.filteredData = currentData.filter(item => {
        let matchesSearch = true;
        let matchesCategory = true;

        if (state.searchQuery) {
            const query = state.searchQuery;
            if (state.currentTab === 'events') {
                matchesSearch = item.title.toLowerCase().includes(query) || 
                                item.category.toLowerCase().includes(query) ||
                                item.venue.toLowerCase().includes(query);
            } else if (state.currentTab === 'students') {
                matchesSearch = item.name.toLowerCase().includes(query) ||
                                item.roll.toLowerCase().includes(query) ||
                                item.email.toLowerCase().includes(query) ||
                                item.city.toLowerCase().includes(query);
            } else if (state.currentTab === 'faqs') {
                matchesSearch = item.question.toLowerCase().includes(query) ||
                                item.answer.toLowerCase().includes(query);
            }
        }

        if (state.categoryFilter !== 'ALL') {
            const filter = state.categoryFilter;
            matchesCategory = item.category === filter || item.department === filter;
        }

        return matchesSearch && matchesCategory;
    });

    // 2. SORT: Array.prototype.sort()
    state.filteredData.sort((a, b) => {
        switch (state.sortCriteria) {
            case 'name_asc':
                return (a.title || a.name || a.question).localeCompare(b.title || b.name || b.question);
            case 'name_desc':
                return (b.title || b.name || b.question).localeCompare(a.title || a.name || a.question);
            case 'date_asc':
                return (a.date || '').localeCompare(b.date || '');
            case 'date_desc':
                return (b.date || '').localeCompare(a.date || '');
            case 'score_high':
                return (b.gpa || b.capacity || 0) - (a.gpa || a.capacity || 0);
            case 'score_low':
                return (a.gpa || a.capacity || 0) - (b.gpa || b.capacity || 0);
            default:
                return a.id - b.id;
        }
    });

    // 3. PAGINATE: Array.prototype.slice()
    const totalRecords = state.filteredData.length;
    const totalPages = Math.ceil(totalRecords / state.pageSize) || 1;
    if (state.currentPage > totalPages) state.currentPage = totalPages;

    const startIndex = (state.currentPage - 1) * state.pageSize;
    const paginatedItems = state.filteredData.slice(startIndex, startIndex + state.pageSize);

    // 4. RENDER VIEW
    renderView(paginatedItems);
    renderPagination(totalRecords, totalPages);
}

/* ==========================================================================
   4. DOM RENDERING
   ========================================================================== */
function renderView(items) {
    const displayContainer = document.getElementById('dataDisplay');

    if (!items.length) {
        displayContainer.innerHTML = `
            <div class="empty-state">
                <h3>🔍 No Records Found</h3>
                <p>No matching entries found for your search/filter criteria.</p>
            </div>
        `;
        return;
    }

    if (state.currentTab === 'events') {
        displayContainer.innerHTML = `
            <div class="items-grid">
                ${items.map(ev => `
                    <article class="data-card">
                        <div>
                            <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                                <span class="tag tag-blue">${escapeHTML(ev.category)}</span>
                                <span style="font-size:0.8rem; font-weight:bold; color:${ev.status === 'Full' ? 'var(--danger)' : 'var(--success)'};">${ev.status}</span>
                            </div>
                            <h3 style="margin-bottom:8px;">${escapeHTML(ev.title)}</h3>
                            <p style="font-size:0.9rem; color:var(--text-secondary); margin-bottom:6px;">📍 <strong>Venue:</strong> ${escapeHTML(ev.venue)}</p>
                            <p style="font-size:0.9rem; color:var(--text-secondary); margin-bottom:6px;">📅 <strong>Date:</strong> ${ev.date} at ${ev.time}</p>
                            <p style="font-size:0.9rem; color:var(--text-secondary);">👥 <strong>Attendance:</strong> ${ev.registered} / ${ev.capacity} registered</p>
                        </div>
                        <div style="margin-top:15px; display:flex; justify-content:space-between; align-items:center;">
                            <span style="font-weight:bold; color:var(--primary);">${ev.fee === 0 ? 'FREE' : '₹' + ev.fee}</span>
                            <button class="page-btn" style="background:var(--primary); color:#fff;" ${ev.status === 'Full' ? 'disabled' : ''} onclick="alert('RSVP confirmed for ${escapeHTML(ev.title)}!')">Reserve Seat</button>
                        </div>
                    </article>
                `).join('')}
            </div>
        `;
    } else if (state.currentTab === 'students') {
        displayContainer.innerHTML = `
            <div class="data-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Roll Number</th>
                            <th>Student Name</th>
                            <th>Department</th>
                            <th>Semester</th>
                            <th>Academic GPA</th>
                            <th>City</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${items.map(s => `
                            <tr>
                                <td><strong>${escapeHTML(s.roll)}</strong></td>
                                <td>${escapeHTML(s.name)}</td>
                                <td><span class="tag tag-amber">${escapeHTML(s.department)}</span></td>
                                <td>Sem ${s.semester}</td>
                                <td><strong style="color:var(--primary);">${s.gpa.toFixed(2)}</strong></td>
                                <td>${escapeHTML(s.city)}</td>
                                <td><span class="tag tag-green">${s.status}</span></td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `;
    } else if (state.currentTab === 'faqs') {
        displayContainer.innerHTML = `
            <div style="display:flex; flex-direction:column; gap:12px;">
                ${items.map(faq => `
                    <div class="data-card" style="padding:16px;">
                        <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                            <span class="tag tag-blue">${escapeHTML(faq.category)}</span>
                            <span style="font-size:0.8rem; color:var(--text-secondary);">FAQ #${faq.id}</span>
                        </div>
                        <h3 style="font-size:1.1rem; margin-bottom:6px;">${escapeHTML(faq.question)}</h3>
                        <p style="color:var(--text-secondary); font-size:0.95rem;">${escapeHTML(faq.answer)}</p>
                    </div>
                `).join('')}
            </div>
        `;
    }
}

function renderPagination(totalRecords, totalPages) {
    const container = document.getElementById('paginationControls');
    const infoText = document.getElementById('pageInfoText');

    const start = totalRecords === 0 ? 0 : (state.currentPage - 1) * state.pageSize + 1;
    const end = Math.min(state.currentPage * state.pageSize, totalRecords);
    infoText.textContent = `Showing ${start} - ${end} of ${totalRecords} records (Page ${state.currentPage} of ${totalPages})`;

    let html = `
        <button class="page-btn" ${state.currentPage === 1 ? 'disabled' : ''} onclick="goToPage(1)">&laquo; First</button>
        <button class="page-btn" ${state.currentPage === 1 ? 'disabled' : ''} onclick="goToPage(${state.currentPage - 1})">&lsaquo; Prev</button>
    `;

    for (let p = 1; p <= totalPages; p++) {
        html += `<button class="page-btn ${p === state.currentPage ? 'active' : ''}" onclick="goToPage(${p})">${p}</button>`;
    }

    html += `
        <button class="page-btn" ${state.currentPage === totalPages ? 'disabled' : ''} onclick="goToPage(${state.currentPage + 1})">Next &rsaquo;</button>
        <button class="page-btn" ${state.currentPage === totalPages ? 'disabled' : ''} onclick="goToPage(${totalPages})">Last &raquo;</button>
    `;

    container.innerHTML = html;
}

window.goToPage = function(page) {
    state.currentPage = page;
    applyFiltersAndRender();
};

/* ==========================================================================
   5. DEPENDENT DROPDOWNS (Intermediate Extension)
   ========================================================================== */
function initDependentDropdowns() {
    const facultySelect = document.getElementById('facultySelect');
    const instituteSelect = document.getElementById('instituteSelect');
    const degreeSelect = document.getElementById('degreeSelect');
    if (!facultySelect || !instituteSelect || !degreeSelect) return;

    // Populate Faculty
    facultySelect.innerHTML = '<option value="">-- Choose Faculty --</option>';
    Object.keys(ACADEMIC_TAXONOMY).forEach(fac => {
        const opt = document.createElement('option');
        opt.value = fac;
        opt.textContent = fac;
        facultySelect.appendChild(opt);
    });

    facultySelect.addEventListener('change', () => {
        const chosenFaculty = facultySelect.value;
        instituteSelect.innerHTML = '<option value="">-- Choose Institute --</option>';
        degreeSelect.innerHTML = '<option value="">-- Choose Degree --</option>';
        degreeSelect.disabled = true;

        if (chosenFaculty && ACADEMIC_TAXONOMY[chosenFaculty]) {
            instituteSelect.disabled = false;
            Object.keys(ACADEMIC_TAXONOMY[chosenFaculty]).forEach(inst => {
                const opt = document.createElement('option');
                opt.value = inst;
                opt.textContent = inst;
                instituteSelect.appendChild(opt);
            });
        } else {
            instituteSelect.disabled = true;
        }
    });

    instituteSelect.addEventListener('change', () => {
        const chosenFaculty = facultySelect.value;
        const chosenInstitute = instituteSelect.value;
        degreeSelect.innerHTML = '<option value="">-- Choose Degree --</option>';

        if (chosenFaculty && chosenInstitute && ACADEMIC_TAXONOMY[chosenFaculty][chosenInstitute]) {
            degreeSelect.disabled = false;
            ACADEMIC_TAXONOMY[chosenFaculty][chosenInstitute].forEach(deg => {
                const opt = document.createElement('option');
                opt.value = deg;
                opt.textContent = deg;
                degreeSelect.appendChild(opt);
            });
        } else {
            degreeSelect.disabled = true;
        }
    });
}

function escapeHTML(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}
