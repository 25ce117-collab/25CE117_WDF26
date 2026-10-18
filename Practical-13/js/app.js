/**
 * Practical 13: Asynchronous AJAX/Fetch CRUD Engine
 * Intermediate Extension: 300ms Debounced AJAX Search
 * Advanced Extension: RESTful JSON API integration with HTTP Status Codes
 */

const API_URL = 'api/events.php';

let currentEditingId = null;
let searchDebounceTimer = null;

document.addEventListener('DOMContentLoaded', () => {
    loadEvents();
    initDebouncedSearch();
    initModal();
    initForm();
});

/* ==========================================================================
   1. ASYNCHRONOUS GET (Fetch All Events)
   ========================================================================== */
async function loadEvents(search = '', category = 'ALL') {
    const tableBody = document.getElementById('eventsTableBody');
    tableBody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding:30px; color:var(--text-secondary);">⏳ Loading events asynchronously...</td></tr>`;

    try {
        let url = `${API_URL}?search=${encodeURIComponent(search)}&category=${encodeURIComponent(category)}`;
        const res = await fetch(url);
        const json = await res.json();

        if (!res.ok) throw new Error(json.message || `HTTP ${res.status}`);

        renderTable(json.data || []);
    } catch (err) {
        tableBody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding:25px; color:var(--danger);">⚠️ Error: ${err.message}</td></tr>`;
        showToast(err.message, 'error');
    }
}

function renderTable(events) {
    const tableBody = document.getElementById('eventsTableBody');

    if (!events.length) {
        tableBody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding:30px; color:var(--text-secondary);">🔍 No events found matching your criteria.</td></tr>`;
        return;
    }

    tableBody.innerHTML = events.map(ev => `
        <tr id="row-event-${ev.id}">
            <td><strong>#${ev.id}</strong></td>
            <td>
                <strong>${escapeHTML(ev.title)}</strong>
                <p style="font-size:0.8rem; color:var(--text-secondary); max-width:280px;">${escapeHTML(ev.description || '')}</p>
            </td>
            <td><span style="background:#e0e7ff; color:#3730a3; padding:2px 8px; border-radius:4px; font-size:0.8rem; font-weight:bold;">${escapeHTML(ev.category)}</span></td>
            <td>📅 ${ev.event_date}</td>
            <td>📍 ${escapeHTML(ev.venue)}</td>
            <td><strong>${ev.max_capacity}</strong> seats</td>
            <td style="text-align:right; white-space:nowrap;">
                <button class="btn btn-secondary btn-sm" onclick="openEditModal(${ev.id})">✏️ Edit</button>
                <button class="btn btn-danger btn-sm" onclick="deleteEvent(${ev.id}, '${escapeHTML(ev.title)}')">🗑️ Delete</button>
            </td>
        </tr>
    `).join('');
}

/* ==========================================================================
   2. INTERMEDIATE EXTENSION: DEBOUNCED SEARCH (300ms)
   ========================================================================== */
function initDebouncedSearch() {
    const searchInput = document.getElementById('searchInput');
    const categorySelect = document.getElementById('categoryFilter');

    searchInput.addEventListener('input', () => {
        clearTimeout(searchDebounceTimer);
        // Wait 300ms after user stops typing before making API call
        searchDebounceTimer = setTimeout(() => {
            loadEvents(searchInput.value.trim(), categorySelect.value);
        }, 300);
    });

    categorySelect.addEventListener('change', () => {
        loadEvents(searchInput.value.trim(), categorySelect.value);
    });
}

/* ==========================================================================
   3. ASYNCHRONOUS POST / PUT (Create & Update)
   ========================================================================== */
function initForm() {
    const form = document.getElementById('eventForm');
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const payload = {
            title: document.getElementById('evTitle').value.trim(),
            category: document.getElementById('evCategory').value.trim(),
            event_date: document.getElementById('evDate').value,
            venue: document.getElementById('evVenue').value.trim(),
            max_capacity: parseInt(document.getElementById('evCapacity').value, 10),
            description: document.getElementById('evDescription').value.trim()
        };

        const isUpdate = currentEditingId !== null;
        const method = isUpdate ? 'PUT' : 'POST';
        const url = isUpdate ? `${API_URL}?id=${currentEditingId}` : API_URL;

        try {
            const res = await fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const json = await res.json();
            if (!res.ok) throw new Error(json.message || `HTTP ${res.status}`);

            showToast(json.message, 'success');
            closeModal();
            loadEvents(document.getElementById('searchInput').value.trim(), document.getElementById('categoryFilter').value);
        } catch (err) {
            showToast(`Save failed: ${err.message}`, 'error');
        }
    });
}

/* ==========================================================================
   4. ASYNCHRONOUS DELETE
   ========================================================================== */
async function deleteEvent(id, title) {
    if (!confirm(`Are you sure you want to delete event "${title}" without reloading the page?`)) {
        return;
    }

    try {
        const res = await fetch(`${API_URL}?id=${id}`, {
            method: 'DELETE'
        });
        const json = await res.json();
        if (!res.ok) throw new Error(json.message || `HTTP ${res.status}`);

        showToast(json.message, 'success');

        // Optimistic DOM update: smoothly fade out row
        const row = document.getElementById(`row-event-${id}`);
        if (row) {
            row.style.transition = 'all 0.3s';
            row.style.opacity = '0';
            setTimeout(() => row.remove(), 300);
        }
    } catch (err) {
        showToast(`Delete failed: ${err.message}`, 'error');
    }
}

/* ==========================================================================
   5. MODAL MANAGEMENT
   ========================================================================== */
function initModal() {
    const overlay = document.getElementById('eventModal');
    const closeBtn = document.getElementById('closeModalBtn');
    const cancelBtn = document.getElementById('cancelModalBtn');
    const openAddBtn = document.getElementById('openAddBtn');

    openAddBtn.addEventListener('click', () => {
        currentEditingId = null;
        document.getElementById('modalTitle').textContent = 'Schedule New Campus Event';
        document.getElementById('eventForm').reset();
        overlay.classList.add('active');
    });

    closeBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);

    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) closeModal();
    });
}

async function openEditModal(id) {
    currentEditingId = id;
    document.getElementById('modalTitle').textContent = `Edit Event #${id}`;

    try {
        const res = await fetch(`${API_URL}?id=${id}`);
        const json = await res.json();
        if (!res.ok) throw new Error(json.message);

        const ev = json.data;
        document.getElementById('evTitle').value = ev.title;
        document.getElementById('evCategory').value = ev.category;
        document.getElementById('evDate').value = ev.event_date;
        document.getElementById('evVenue').value = ev.venue;
        document.getElementById('evCapacity').value = ev.max_capacity;
        document.getElementById('evDescription').value = ev.description;

        document.getElementById('eventModal').classList.add('active');
    } catch (err) {
        showToast(`Failed to load event details: ${err.message}`, 'error');
    }
}

function closeModal() {
    document.getElementById('eventModal').classList.remove('active');
}

/* ==========================================================================
   6. TOAST NOTIFICATIONS
   ========================================================================== */
function showToast(msg, type = 'success') {
    let box = document.getElementById('toastBox');
    if (!box) {
        box = document.createElement('div');
        box.id = 'toastBox';
        box.className = 'toast-box';
        document.body.appendChild(box);
    }

    const t = document.createElement('div');
    t.className = `toast toast-${type}`;
    t.textContent = (type === 'success' ? '✓ ' : '⚠️ ') + msg;
    box.appendChild(t);

    setTimeout(() => {
        t.style.opacity = '0';
        setTimeout(() => t.remove(), 300);
    }, 3500);
}

function escapeHTML(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}
