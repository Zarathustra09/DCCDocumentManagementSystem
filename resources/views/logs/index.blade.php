@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0"><i class='bx bx-clipboard'></i> Activity Logs</h3>
                <span class="badge bg-primary">Spatie Activity</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    {!! $dataTable->table(['class' => 'table table-striped table-hover w-100'], true) !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}

<script>
document.addEventListener('click', function (ev) {
    if (!ev.target.closest('.view-log')) return;
    ev.preventDefault();
    const btn = ev.target.closest('.view-log');
    const id = btn.dataset.id;

    Swal.fire({
        title: 'Loading...',
        html: 'Fetching activity details...',
        didOpen: () => {
            Swal.showLoading();
            fetch(`/logs/${id}`)
                .then(res => {
                    if (!res.ok) throw new Error('Network error');
                    return res.json();
                })
                .then(data => {
                    // Build a structured meta block:
                    const loggedTime = data.created_at_human ?? data.created_at ?? '';
                    const heading = data.description ? String(data.description).trim() : (data.event ? String(data.event).trim() : 'Activity Details');
                    const eventText = data.event ? String(data.event).trim() : '';
                    const showEventSubtitle = eventText && eventText.toLowerCase() !== (heading || '').toLowerCase();

                    const subjectLabel = data.subject_type ? `${escapeHtml(data.subject_type)}${data.subject_id ? ' #' + escapeHtml(data.subject_id) : ''}` : '-';
                    const causerLabel = data.causer ? escapeHtml(data.causer) : '-';

                    const metaHtml = `
                        <div style="text-align:left">
                            <div style="margin-bottom:8px;">
                                <div style="font-weight:700;font-size:1.1rem;color:#212529;line-height:1.1">${escapeHtml(heading)}</div>
                                ${ showEventSubtitle ? `<div class="small text-muted" style="margin-top:4px">${escapeHtml(eventText)}</div>` : '' }
                            </div>

                            <div style="display:flex;flex-wrap:wrap;gap:8px;align-items:center;">
                                <span style="background:#f8fafc;color:#0f172a;padding:6px 10px;border-radius:6px;font-size:0.85rem;">Subject: ${subjectLabel}</span>
                                <span style="background:#f8fafc;color:#0f172a;padding:6px 10px;border-radius:6px;font-size:0.85rem;">Causer: ${causerLabel}</span>
                                <span style="background:#f8fafc;color:#0f172a;padding:6px 10px;border-radius:6px;font-size:0.85rem;">Logged: ${escapeHtml(loggedTime)}</span>
                            </div>
                        </div>
                    `;

                    let content = `
                        <div style="text-align:left">
                            ${metaHtml}
                        </div>
                    `;

                    // If server provided a diff, render a compact table showing changed fields
                    if (Array.isArray(data.diff) && data.diff.length > 0) {
                        let rows = '';
                        data.diff.forEach(ch => {
                            // stringify values
                            let oldVal = safeStringify(ch.old);
                            let newVal = safeStringify(ch.new);

                            // if value looks like an ISO timestamp, format it to "MM/DD/YYYY h:mm AM/PM"
                            oldVal = tryFormatIsoFull(oldVal);
                            newVal = tryFormatIsoFull(newVal);

                            rows += `<tr>
                                <td style="vertical-align:top;padding:10px;border-bottom:1px solid #f1f1f1;width:28%;"><code>${escapeHtml(ch.field)}</code></td>
                                <td style="vertical-align:top;padding:10px;border-bottom:1px solid #f1f1f1;width:36%;white-space:pre-wrap;word-break:break-word;"><code>${escapeHtml(oldVal)}</code></td>
                                <td style="vertical-align:top;padding:10px;border-bottom:1px solid #f1f1f1;width:36%;white-space:pre-wrap;word-break:break-word;"><code>${escapeHtml(newVal)}</code></td>
                            </tr>`;
                        });

                        content += `
                            <div style="margin-top:12px;">
                                <table style="width:100%;border-collapse:collapse;background:#fff;border-radius:6px;overflow:hidden;border:1px solid #e9ecef">
                                    <thead style="background:#f8f9fa">
                                        <tr>
                                            <th style="text-align:left;padding:10px;border-bottom:1px solid #e9ecef">Field</th>
                                            <th style="text-align:left;padding:10px;border-bottom:1px solid #e9ecef">Old</th>
                                            <th style="text-align:left;padding:10px;border-bottom:1px solid #e9ecef">New</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${rows}
                                    </tbody>
                                </table>
                            </div>
                        `;
                    } else {
                        // fallback: pretty-print properties JSON with copy button
                        let propsText = 'No properties.';
                        try {
                            propsText = JSON.stringify(data.properties ?? {}, null, 2);
                        } catch (e) {
                            propsText = 'Unable to parse properties.';
                        }

                        content += `
                            <div style="margin-top:12px;">
                                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
                                    <div class="small text-muted">Properties</div>
                                    <button id="copyPropsBtn" class="swal2-confirm swal2-styled" style="background:#0d6efd;border:none;padding:4px 8px;font-size:0.85rem">Copy</button>
                                </div>
                                <pre id="propsPre" style="text-align:left;white-space:pre-wrap;word-break:break-word;max-height:400px;overflow:auto;background:#f8f9fa;padding:12px;border-radius:6px;border:1px solid #e9ecef;">${escapeHtml(propsText)}</pre>
                            </div>
                        `;
                    }

                    Swal.fire({
                        html: content,
                        width: '900px',
                        showConfirmButton: true,
                        confirmButtonText: 'Close'
                    }).then(() => { /* noop */ });

                    // bind copy button if present
                    setTimeout(() => {
                        const copyBtn = document.getElementById('copyPropsBtn');
                        if (copyBtn) {
                            copyBtn.addEventListener('click', () => {
                                const pre = document.getElementById('propsPre');
                                if (!pre) return;
                                navigator.clipboard.writeText(pre.innerText || pre.textContent || '')
                                    .then(() => {
                                        Swal.fire({icon: 'success', title: 'Copied', timer: 1000, showConfirmButton: false});
                                    })
                                    .catch(() => {
                                        Swal.fire({icon: 'error', title: 'Copy failed', text: 'Unable to copy to clipboard.'});
                                    });
                            });
                        }
                    }, 200);
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load activity details'
                    });
                });
        }
    });
});

// helper to stringify arrays/objects concisely for table cells
function safeStringify(v) {
    if (v === null || v === undefined) return '';
    try {
        if (typeof v === 'object') return JSON.stringify(v, null, 2);
        return String(v);
    } catch (e) {
        return String(v);
    }
}

// try to detect ISO timestamp strings and format to "MM/DD/YYYY h:mm AM/PM"
function tryFormatIsoFull(val) {
    if (typeof val !== 'string') return val;
    const isoRegex = /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d+)?Z?$/;
    if (!isoRegex.test(val.trim())) return val;
    try {
        const d = new Date(val);
        if (isNaN(d.getTime())) return val;

        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const dd = String(d.getDate()).padStart(2, '0');
        const yyyy = d.getFullYear();

        let hours = d.getHours();
        const minutes = String(d.getMinutes()).padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // 0 -> 12

        return `${mm}/${dd}/${yyyy} ${hours}:${minutes} ${ampm}`;
    } catch (e) {
        return val;
    }
}

// simple HTML escape
function escapeHtml(str) {
    return String(str ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#39;');
}
</script>
@endpush
