/**
 * Practical 14: Admin Dashboard Interactivity & Canvas Analytics
 * Intermediate Extension: AJAX User Status Toggle
 * Advanced Extension: HTML5 Canvas Chart Rendering
 */

document.addEventListener('DOMContentLoaded', () => {
    initAnalyticsChart();
    initStatusToggles();
});

/* ==========================================================================
   1. ADVANCED EXTENSION: HTML5 CANVAS ANALYTICS CHART
   ========================================================================== */
async function initAnalyticsChart() {
    const canvas = document.getElementById('analyticsCanvas');
    if (!canvas) return;

    try {
        const res = await fetch('api/chart_data.php');
        const json = await res.json();
        if (json.status !== 'success') return;

        drawBarChart(canvas, json.monthly.labels, json.monthly.counts);
    } catch (e) {
        console.warn('Chart fetch fallback to defaults', e);
        drawBarChart(canvas, ['Aug 2026', 'Sep 2026', 'Oct 2026'], [45, 95, 160]);
    }
}

function drawBarChart(canvas, labels, values) {
    const ctx = canvas.getContext('2d');
    const width = canvas.width;
    const height = canvas.height;
    const padding = 50;

    ctx.clearRect(0, 0, width, height);

    // Axes
    ctx.strokeStyle = '#cbd5e1';
    ctx.lineWidth = 1.5;
    ctx.beginPath();
    ctx.moveTo(padding, 20);
    ctx.lineTo(padding, height - padding);
    ctx.lineTo(width - 20, height - padding);
    ctx.stroke();

    const maxVal = Math.max(...values, 100);
    const barWidth = (width - padding - 40) / values.length - 30;

    values.forEach((val, idx) => {
        const barHeight = (val / maxVal) * (height - padding - 40);
        const x = padding + 20 + idx * (barWidth + 30);
        const y = height - padding - barHeight;

        // Gradient bar
        const grad = ctx.createLinearGradient(0, y, 0, height - padding);
        grad.addColorStop(0, '#2563eb');
        grad.addColorStop(1, '#93c5fd');

        ctx.fillStyle = grad;
        ctx.fillRect(x, y, barWidth, barHeight);

        // Labels
        ctx.fillStyle = '#0f172a';
        ctx.font = 'bold 12px sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText(val, x + barWidth / 2, y - 8);

        ctx.fillStyle = '#64748b';
        ctx.font = '11px sans-serif';
        ctx.fillText(labels[idx], x + barWidth / 2, height - padding + 18);
    });
}

/* ==========================================================================
   2. INTERMEDIATE EXTENSION: AJAX USER STATUS TOGGLE
   ========================================================================== */
function initStatusToggles() {
    document.querySelectorAll('.btn-toggle-status').forEach(btn => {
        btn.addEventListener('click', async () => {
            const userId = btn.getAttribute('data-id');
            const currentStatus = btn.getAttribute('data-status');
            const newStatus = currentStatus === 'active' ? 'inactive' : 'active';

            btn.disabled = true;
            btn.textContent = 'Updating...';

            try {
                const res = await fetch('toggle_user.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `user_id=${userId}&new_status=${newStatus}`
                });

                const json = await res.json();
                if (json.status === 'success') {
                    btn.setAttribute('data-status', newStatus);
                    btn.textContent = newStatus === 'active' ? 'Deactivate' : 'Activate';

                    const badge = document.getElementById(`status-badge-${userId}`);
                    if (badge) {
                        badge.className = `badge badge-${newStatus}`;
                        badge.textContent = newStatus.toUpperCase();
                    }
                    alert(`✓ User #${userId} status toggled to: ${newStatus.toUpperCase()}`);
                } else {
                    alert('⚠️ Error: ' + json.message);
                }
            } catch (err) {
                alert('Request failed: ' + err.message);
            } finally {
                btn.disabled = false;
            }
        });
    });
}
