<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>GOS 18 — {{ $patient->first_name }} {{ $patient->surname }}</title>
<style>
@page { size: A4; margin: 10mm; }
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { background: #e8e8e8; }
body { font-family: Arial, Helvetica, sans-serif; font-size: 9pt; color: #000; background: #e8e8e8; }
.page { max-width: 190mm; margin: 8mm auto; background: #fff; padding: 8mm 10mm; box-shadow: 0 2px 10px rgba(0,0,0,.25); }
.print-btn { position: fixed; top: 10px; right: 10px; background: #003087; color: #fff; border: none; padding: 7px 18px; font-size: 11pt; border-radius: 4px; cursor: pointer; z-index: 9999; }
.back-btn  { position: fixed; top: 10px; left: 10px; background: #003087; color: #fff; text-decoration: none; padding: 7px 18px; font-size: 11pt; border-radius: 4px; z-index: 9999; }
@media (max-width: 600px) {
    .print-btn { top: auto; bottom: 10px; right: 10px; font-size: 9pt; padding: 6px 12px; }
    .back-btn  { top: auto; bottom: 10px; left: 10px; font-size: 9pt; padding: 6px 12px; }
    .page { padding: 4mm 4mm; margin: 4mm auto; }
}
/* Header */
.form-hdr { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3pt solid #003087; padding-bottom: 4px; margin-bottom: 8px; }
.form-hdr-title h1 { font-size: 12pt; font-weight: 900; color: #003087; letter-spacing: 0.5pt; }
.form-hdr-title p  { font-size: 8pt; color: #444; }
/* Part/section headers */
.part-hdr { background: #003087; color: #fff; font-weight: bold; font-size: 9.5pt; padding: 3px 8px; margin: 8px 0 4px; }
.section { border: 1pt solid #bbb; padding: 5px 7px; margin-bottom: 5px; }
.sub-hdr { font-weight: bold; font-size: 8.5pt; margin: 5px 0 3px; border-bottom: 0.5pt solid #ccc; padding-bottom: 2px; }
/* Layout helpers */
.fl  { display: flex; align-items: baseline; gap: 5px; margin-bottom: 4px; flex-wrap: wrap; }
.lbl { font-size: 8.5pt; font-weight: bold; white-space: nowrap; }
.g2  { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.g3  { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 6px; }
/* Checkboxes / radios */
.chk { display: flex; align-items: flex-start; gap: 5px; margin-bottom: 3px; font-size: 8.5pt; line-height: 1.3; }
.chk input[type=checkbox], .chk input[type=radio] { flex-shrink: 0; width: 11px; height: 11px; margin-top: 1pt; cursor: pointer; }
/* Editable inputs */
.inp { border: none; border-bottom: 1pt solid #777; background: #fffff0; font-size: 9pt; padding: 1pt 2pt; vertical-align: baseline; font-family: inherit; }
.inp:focus { outline: 1px solid #003087; background: #fffde7; }
/* Tables */
table { width: 100%; border-collapse: collapse; font-size: 8.5pt; }
th, td { border: 1pt solid #555; padding: 2px 4px; vertical-align: middle; }
th { background: #e0e0e0; text-align: center; font-weight: bold; }
td input.inp { width: 100%; border-bottom: none; background: transparent; }
td input.inp:focus { outline: 1px solid #003087; }
/* Statement box */
.stmt { background: #f0f4ff; border: 1pt solid #003087; padding: 5px 8px; font-size: 8.5pt; line-height: 1.5; margin-bottom: 6px; }
/* Print */
@media print {
    html, body { background: #fff; margin: 0; padding: 0; }
    .no-print { display: none !important; }
    .page { margin: 0; padding: 6mm 8mm; max-width: none; box-shadow: none; }
}
.save-btn { position: fixed; top: 10px; right: 100px; background: #16a34a; color: #fff; border: none; padding: 7px 18px; font-size: 11pt; border-radius: 4px; cursor: pointer; z-index: 9999; }
.save-btn:disabled { background: #6b7280; cursor: default; }
</style>
</head>
<meta name="csrf-token" content="{{ csrf_token() }}">
<body>

<a href="{{ $source === 'egos' ? route('egos.index') : route('patients.show', $patient) }}" class="back-btn no-print">&larr; {{ $source === 'egos' ? 'Back to eGOS' : 'Back to Patient' }}</a>
<button class="print-btn no-print" onclick="window.print()">&#128438; Print</button>
<button id="save-btn" class="save-btn no-print" onclick="saveEgosForm()">&#128190; {{ $existingSubmission ? 'Update Form' : 'Save Form' }}</button>
<div id="save-msg" class="no-print" style="display:none; position:fixed; top:48px; right:100px; background:#dcfce7; color:#166534; border:1px solid #86efac; padding:5px 12px; border-radius:4px; font-size:9pt; z-index:9999;">
    Form saved to eGOS submissions
</div>

<script>
function initSignaturePad(canvasId, hiddenInputId) {
    const canvas = document.getElementById(canvasId);
    const ctx = canvas.getContext('2d');
    const input = document.getElementById(hiddenInputId);
    let drawing = false;
    ctx.strokeStyle = '#000'; ctx.lineWidth = 1.5; ctx.lineCap = 'round';
    function getPos(e) {
        const rect = canvas.getBoundingClientRect();
        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;
        return { x: (clientX - rect.left) * (canvas.width / rect.width), y: (clientY - rect.top) * (canvas.height / rect.height) };
    }
    canvas.addEventListener('mousedown', e => { drawing = true; ctx.beginPath(); const p = getPos(e); ctx.moveTo(p.x, p.y); });
    canvas.addEventListener('mousemove', e => { if (!drawing) return; const p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); });
    canvas.addEventListener('mouseup', () => { drawing = false; input.value = canvas.toDataURL(); });
    canvas.addEventListener('mouseleave', () => { drawing = false; });
    canvas.addEventListener('touchstart', e => { e.preventDefault(); drawing = true; ctx.beginPath(); const p = getPos(e); ctx.moveTo(p.x, p.y); }, { passive: false });
    canvas.addEventListener('touchmove', e => { e.preventDefault(); if (!drawing) return; const p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); }, { passive: false });
    canvas.addEventListener('touchend', () => { drawing = false; input.value = canvas.toDataURL(); });
}
function clearSig(canvasId) {
    const canvas = document.getElementById(canvasId);
    canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
    const input = document.getElementById(canvasId + '-data');
    if (input) input.value = '';
}
function saveEgosForm() {
    const fields = [];
    document.querySelectorAll('input:not([type=hidden]), select, textarea').forEach(function(el, i) {
        var entry = { index: i, type: el.type || 'text', name: el.name || null, value: el.value };
        if (el.type === 'checkbox' || el.type === 'radio') { entry.checked = el.checked; }
        fields.push(entry);
    });
    document.querySelectorAll('input[type=hidden][id^="sig-"]').forEach(function(el) {
        if (el.value) fields.push({ sigId: el.id, name: el.name, value: el.value, type: 'sig' });
    });
    fetch('{{ route('egos.store') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ patient_id: {{ $patient->id }}, form_type: 'GOS18', form_data: fields, voucher_value: {{ $existingSubmission?->voucher_value ?? 'null' }} })
    }).then(function(r) {
        if (r.ok) {
            document.getElementById('save-btn').disabled = true;
            document.getElementById('save-btn').textContent = '✓ Saved';
            document.getElementById('save-msg').style.display = 'block';
        }
    }).catch(function() { alert('Save failed. Please try again.'); });
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initSignaturePad('sig-gos18-optometrist', 'sig-gos18-optometrist-data');

    const saved = @json($savedFormData ?? []);
    if (!saved.length) return;
    const inputs = document.querySelectorAll('input:not([type=hidden]), select, textarea');
    saved.forEach(function(entry) {
        if (entry.type === 'sig' && entry.sigId) {
            var hi = document.getElementById(entry.sigId);
            if (hi && entry.value) {
                hi.value = entry.value;
                var cv = document.getElementById(entry.sigId.replace('-data', ''));
                if (cv) { var img = new Image(); img.onload = function() { cv.getContext('2d').drawImage(img, 0, 0); }; img.src = entry.value; }
            }
            return;
        }
        const el = inputs[entry.index];
        if (!el) return;
        if (entry.type === 'checkbox' || entry.type === 'radio') {
            el.checked = entry.checked ?? false;
        } else {
            el.value = entry.value ?? '';
        }
    });
});
</script>

<div class="page">

{{-- ── Header ──────────────────────────────────────────────────────────────── --}}
<div class="form-hdr">
    <div class="form-hdr-title">
        <h1>GOS 18</h1>
        <p style="font-size:9.5pt; font-weight:bold; margin-top:2px;">OPHTHALMIC REFERRAL / INFORMATION FOR GP</p>
        <p>Primary Ophthalmic Services</p>
    </div>
    <svg xmlns="http://www.w3.org/2000/svg" width="72" height="38" viewBox="0 0 72 38" style="display:block;flex-shrink:0;">
        <rect width="72" height="38" fill="#003087" rx="2"/>
        <text x="36" y="28" text-anchor="middle" fill="#fff" font-family="Arial Black,Arial,sans-serif" font-weight="900" font-size="24" letter-spacing="-1">NHS</text>
    </svg>
</div>

{{-- ── Optometrist / Dates ──────────────────────────────────────────────────── --}}
<div class="section">
    <div class="g2">
        <div>
            <div class="fl">
                <span class="lbl">Date of sight test</span>
                <input type="date" class="inp" style="width:130px;" value="{{ date('Y-m-d') }}">
            </div>
            <div class="fl">
                <span class="lbl">Date of referral</span>
                <input type="date" class="inp" style="width:130px;" value="{{ date('Y-m-d') }}">
            </div>
        </div>
        <div>
            <div class="stmt">
                I am writing to advise you about the above patient whom I have examined today and wish to bring to your attention the following findings.
            </div>
        </div>
    </div>

    <div style="margin-top:6px;">
        <div class="fl">
            <span class="lbl">Optometrist / OMP name</span>
            <input type="text" class="inp" style="flex:1; min-width:160px;">
        </div>
        <div class="fl">
            <span class="lbl">Practice name &amp; address</span>
            <input type="text" class="inp" style="flex:1; min-width:160px;" value="{{ $patient->practice?->name }}">
        </div>
        @if($patient->practice?->address)
        <div class="fl">
            <span class="lbl" style="min-width:130px;">&nbsp;</span>
            <input type="text" class="inp" style="flex:1; min-width:160px;" value="{{ $patient->practice->address }}">
        </div>
        @else
        <div class="fl">
            <span class="lbl" style="min-width:130px;">&nbsp;</span>
            <input type="text" class="inp" style="flex:1; min-width:160px;">
        </div>
        @endif
    </div>

    <div class="g2" style="margin-top:6px;">
        <div>
            <div class="fl">
                <span class="lbl">GOC / GMC No.</span>
                <input type="text" class="inp" style="width:110px;">
            </div>
            <div class="fl">
                <span class="lbl">NHSBSA number</span>
                <input type="text" class="inp" style="width:110px;">
            </div>
        </div>
        <div>
            <div class="fl">
                <span class="lbl">Post Code</span>
                <input type="text" class="inp" style="width:80px;">
            </div>
            <div class="fl">
                <span class="lbl">Tel</span>
                <input type="text" class="inp" style="width:110px;">
            </div>
            <div class="fl">
                <span class="lbl">NHS mail</span>
                <input type="text" class="inp" style="width:150px;">
            </div>
        </div>
    </div>
</div>

{{-- ── Patient Details ──────────────────────────────────────────────────────── --}}
<div class="part-hdr">PATIENT DETAILS</div>
<div class="section">
    <div class="g2">
        <div>
            <div class="fl">
                <span class="lbl">Title</span>
                <input type="text" class="inp" style="width:55px;" value="{{ $patient->title }}">
                <span class="lbl" style="margin-left:10px;">Gender</span>
                <input type="text" class="inp" style="width:90px;" value="{{ $patient->sex_gender?->label() }}">
            </div>
            <div class="fl">
                <span class="lbl">Surname</span>
                <input type="text" class="inp" style="flex:1; min-width:100px;" value="{{ $patient->surname }}">
            </div>
            <div class="fl">
                <span class="lbl">Forenames</span>
                <input type="text" class="inp" style="flex:1; min-width:100px;" value="{{ $patient->first_name }}">
            </div>
            <div class="fl">
                <span class="lbl">Address</span>
                <input type="text" class="inp" style="flex:1; min-width:100px;" value="{{ $patient->address_line_1 }}">
            </div>
            @if($patient->town_city)
            <div class="fl">
                <span class="lbl" style="min-width:50px;">&nbsp;</span>
                <input type="text" class="inp" style="flex:1; min-width:100px;" value="{{ $patient->town_city }}">
            </div>
            @endif
            @if($patient->county)
            <div class="fl">
                <span class="lbl" style="min-width:50px;">&nbsp;</span>
                <input type="text" class="inp" style="flex:1; min-width:100px;" value="{{ $patient->county }}">
            </div>
            @endif
        </div>
        <div>
            <div class="fl">
                <span class="lbl">Postcode</span>
                <input type="text" class="inp" style="width:80px;" value="{{ $patient->post_code }}">
            </div>
            <div class="fl">
                <span class="lbl">Telephone</span>
                <input type="text" class="inp" style="width:120px;" value="{{ $patient->telephone_mobile }}">
            </div>
            <div class="fl">
                <span class="lbl">Date of Birth</span>
                <input type="date" class="inp" style="width:130px;" value="{{ $patient->date_of_birth->format('Y-m-d') }}">
            </div>
            <div class="fl">
                <span class="lbl">NHS Number</span>
                <input type="text" class="inp" style="width:110px;">
            </div>
        </div>
    </div>
</div>

{{-- ── GP Details ───────────────────────────────────────────────────────────── --}}
<div class="part-hdr">GP DETAILS</div>
<div class="section">
    <div class="fl">
        <span class="lbl">GP Name &amp; Practice Address</span>
        <input type="text" class="inp" style="flex:1; min-width:160px;" value="{{ $patient->doctor?->name }}">
    </div>
    <div class="fl">
        <span class="lbl" style="min-width:156px;">&nbsp;</span>
        <input type="text" class="inp" style="flex:1; min-width:160px;">
    </div>
    <div class="fl">
        <span class="lbl" style="min-width:156px;">&nbsp;</span>
        <input type="text" class="inp" style="flex:1; min-width:160px;">
    </div>
</div>

{{-- ── GP Action Required ───────────────────────────────────────────────────── --}}
<div class="part-hdr">GP ACTION REQUIRED</div>
<div class="section">
    <div class="g2">
        <div>
            <div class="chk"><input type="checkbox"><span>This letter is for <strong>INFORMATION ONLY</strong></span></div>
            <div class="chk"><input type="checkbox"><span>Patient asked to telephone / visit GP</span></div>
            <div class="chk"><input type="checkbox"><span>Patient sent to Eye Casualty</span></div>
        </div>
        <div>
            <div class="chk"><input type="checkbox"><span>Advise Referral to Eye Dept <strong>(URGENT)</strong></span></div>
            <div class="chk"><input type="checkbox"><span>Advise Referral to Eye Dept (Routine)</span></div>
        </div>
    </div>

    <div style="margin-top:6px;">
        <div class="sub-hdr">Clinic type — Children</div>
        <div style="display:flex; gap:20px; flex-wrap:wrap;">
            <label class="chk"><input type="checkbox"><span>Strabismus and Amblyopia</span></label>
            <label class="chk"><input type="checkbox"><span>Paediatric non-strabismus</span></label>
            <label class="chk"><input type="checkbox"><span>Orthoptic (only)</span></label>
        </div>
    </div>

    <div style="margin-top:6px;">
        <div class="sub-hdr">Clinic type — Adults</div>
        <div class="g3">
            <label class="chk"><input type="checkbox"><span>Cataract</span></label>
            <label class="chk"><input type="checkbox"><span>Cornea</span></label>
            <label class="chk"><input type="checkbox"><span>Diabetic Medical Retina</span></label>
            <label class="chk"><input type="checkbox"><span>External Eye Disease</span></label>
            <label class="chk"><input type="checkbox"><span>Glaucoma</span></label>
            <label class="chk"><input type="checkbox"><span>Laser (YAG capsulotomy)</span></label>
            <label class="chk"><input type="checkbox"><span>Low Vision</span></label>
            <label class="chk"><input type="checkbox"><span>Oculoplastics / Orbits / Lacrimal</span></label>
            <label class="chk"><input type="checkbox"><span>Other Medical Retina (incl ARMD)</span></label>
            <label class="chk"><input type="checkbox"><span>Squint / Ocular motility</span></label>
            <label class="chk"><input type="checkbox"><span>Vitreoretinal</span></label>
            <label class="chk"><input type="checkbox"><span>Not Otherwise Specified</span></label>
        </div>
    </div>

    <div class="fl" style="margin-top:6px;">
        <span class="lbl">Clinical Terms</span>
        <input type="text" class="inp" style="flex:1; min-width:200px;">
    </div>
</div>

{{-- ── Refraction ───────────────────────────────────────────────────────────── --}}
<div class="part-hdr">REFRACTION</div>
<div class="section">
    <table>
        <thead>
            <tr>
                <th style="width:22px;">&nbsp;</th>
                <th>Sph</th>
                <th>Cyl</th>
                <th>Axis</th>
                <th>Prism</th>
                <th>Base</th>
                <th>VA</th>
                <th>Pinhole</th>
                <th>Add</th>
                <th>Near Vision</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align:center; font-weight:bold; background:#f5f5f5;">R</td>
                <td><input type="text" class="inp"></td>
                <td><input type="text" class="inp"></td>
                <td><input type="text" class="inp"></td>
                <td><input type="text" class="inp"></td>
                <td><input type="text" class="inp"></td>
                <td><input type="text" class="inp"></td>
                <td><input type="text" class="inp"></td>
                <td><input type="text" class="inp"></td>
                <td><input type="text" class="inp"></td>
            </tr>
            <tr>
                <td style="text-align:center; font-weight:bold; background:#f5f5f5;">L</td>
                <td><input type="text" class="inp"></td>
                <td><input type="text" class="inp"></td>
                <td><input type="text" class="inp"></td>
                <td><input type="text" class="inp"></td>
                <td><input type="text" class="inp"></td>
                <td><input type="text" class="inp"></td>
                <td><input type="text" class="inp"></td>
                <td><input type="text" class="inp"></td>
                <td><input type="text" class="inp"></td>
            </tr>
        </tbody>
    </table>

    <div class="fl" style="margin-top:6px;">
        <span class="lbl">Previous corrected VA — date</span>
        <input type="date" class="inp" style="width:130px;">
        <span class="lbl" style="margin-left:10px;">R</span>
        <input type="text" class="inp" style="width:65px;">
        <span class="lbl" style="margin-left:10px;">L</span>
        <input type="text" class="inp" style="width:65px;">
    </div>
</div>

{{-- ── Clinical Findings ────────────────────────────────────────────────────── --}}
<div class="part-hdr">CLINICAL FINDINGS</div>
<div class="section">

    {{-- Visual Fields --}}
    <div class="sub-hdr">Visual Fields</div>
    <div class="g2" style="margin-bottom:6px;">
        <div>
            <span class="lbl" style="font-size:8pt;">Right</span>
            <div style="display:flex; gap:10px; align-items:center; margin-top:2px;">
                <label class="chk"><input type="radio" name="vf_r"><span>Normal / enclosed</span></label>
                <input type="text" class="inp" style="flex:1; min-width:80px;" placeholder="Details">
            </div>
        </div>
        <div>
            <span class="lbl" style="font-size:8pt;">Left</span>
            <div style="display:flex; gap:10px; align-items:center; margin-top:2px;">
                <label class="chk"><input type="radio" name="vf_l"><span>Normal / enclosed</span></label>
                <input type="text" class="inp" style="flex:1; min-width:80px;" placeholder="Details">
            </div>
        </div>
    </div>

    {{-- Optic nerve heads --}}
    <div class="sub-hdr">Optic Nerve Heads — C:D Ratio</div>
    <div class="fl" style="margin-bottom:6px;">
        <span class="lbl">R</span>
        <input type="text" class="inp" style="width:60px;" placeholder="0.0">
        <span class="lbl" style="margin-left:20px;">L</span>
        <input type="text" class="inp" style="width:60px;" placeholder="0.0">
    </div>

    {{-- IOP --}}
    <div class="sub-hdr">Intraocular Pressure (IOP)</div>
    <div class="fl">
        <span class="lbl">Time</span>
        <input type="text" class="inp" style="width:70px;" placeholder="HH:MM">
        <span class="lbl" style="margin-left:10px;">mmHg R</span>
        <input type="text" class="inp" style="width:50px;">
        <span class="lbl" style="margin-left:6px;">L</span>
        <input type="text" class="inp" style="width:50px;">
        <span class="lbl" style="margin-left:10px;">Method</span>
        <select class="inp" style="width:150px;">
            <option value=""></option>
            <option value="applanation">Applanation</option>
            <option value="non-contact">Non-contact</option>
            <option value="other">Other</option>
        </select>
    </div>
</div>

{{-- ── Additional Information ───────────────────────────────────────────────── --}}
<div class="part-hdr">ADDITIONAL INFORMATION</div>
<div class="section">
    <div style="display:flex; gap:20px; flex-wrap:wrap; margin-bottom:6px;">
        <label class="chk"><input type="checkbox"><span>Cycloplegic refraction performed</span></label>
        <label class="chk"><input type="checkbox"><span>Dilated fundus examination performed</span></label>
    </div>
    <textarea class="inp" style="width:100%; height:80px; border:1pt solid #777; padding:4px; font-size:9pt; font-family:inherit; resize:vertical; background:#fffff0;" placeholder="Additional clinical information, observations, or notes..."></textarea>
</div>

{{-- ── Optometrist Signature ────────────────────────────────────────────────── --}}
<div class="part-hdr">OPTOMETRIST SIGNATURE</div>
<div class="section">
    <div class="g2">
        <div>
            <p style="font-size:8pt; font-weight:bold; margin-bottom:2px;">Signature</p>
            <canvas id="sig-gos18-optometrist" width="400" height="80" style="display:block; width:100%; max-width:400px; height:80px; border:1px solid #000; background:#fff; cursor:crosshair; margin-top:4px;"></canvas>
            <input type="hidden" id="sig-gos18-optometrist-data" name="sig-gos18-optometrist">
            <button type="button" class="no-print" onclick="clearSig('sig-gos18-optometrist')" style="font-size:7pt; color:#555; background:none; border:1px solid #bbb; border-radius:2px; padding:1px 6px; cursor:pointer; margin-top:2px;">Clear</button>
            <p class="no-print" style="font-size:7pt; color:#888; margin-top:1px; font-style:italic;">Sign here (use mouse or touch)</p>
            <p style="font-size:8.5pt; margin-top:4px;">Date&nbsp;<input type="date" class="inp" style="width:130px;" value="{{ date('Y-m-d') }}"></p>
        </div>
        <div>
            <p style="font-size:8pt; font-weight:bold; margin-bottom:2px;">Name</p>
            <input type="text" class="inp" style="width:100%;">
            <p style="font-size:8pt; font-weight:bold; margin-top:6px; margin-bottom:2px;">GOC / GMC No.</p>
            <input type="text" class="inp" style="width:110px;">
        </div>
    </div>
</div>

</div>{{-- /page --}}
</body>
</html>
