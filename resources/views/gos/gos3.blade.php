@php
use App\Enums\PatientType;

$age      = $patient->date_of_birth->age;
$benefits = $patient->benefits ?? [];
$ptype    = $patient->patient_type;

// GOS3 eligibility (no age 60+ or glaucoma risk criteria)
$isUnder16    = $age < 16;
$isStudent    = $patient->in_full_time_education && $age >= 16 && $age <= 18;
$isChildType  = $ptype === PatientType::Child;
$isDiabGlauc  = $patient->is_diabetic || $patient->has_glaucoma;
$isFHG        = $ptype === PatientType::FamilyHistory;
$isNHSType    = in_array($ptype, [PatientType::NHS, PatientType::Over60, PatientType::Glaucoma, PatientType::FamilyHistory], strict: true);

$hasIS        = in_array('income_support',           $benefits);
$hasUC        = in_array('universal_credit',         $benefits);
$hasPC        = in_array('pension_credit',           $benefits);
$hasJSA       = in_array('jobseekers_allowance',     $benefits);
$hasESA       = in_array('esa',                      $benefits);
$hasTaxCredit = in_array('nhs_tax_credit_exemption', $benefits);
$hasHC2       = in_array('hc2_certificate',          $benefits);
$hasHC3       = in_array('hc3_certificate',          $benefits);

$cb = function(?string $val, int $n): string {
    $s   = mb_strtoupper($val ?? '');
    $out = '';
    $chars = mb_str_split($s);
    for ($i = 0; $i < $n; $i++) {
        $c    = isset($chars[$i]) ? htmlspecialchars($chars[$i]) : '&nbsp;';
        $out .= '<span class="cb">' . $c . '</span>';
    }
    return $out;
};

$dobBoxes = function (?\Illuminate\Support\Carbon $dob): string {
    $sep = '<span class="dob-sep">/</span>';
    if (! $dob) {
        $e = fn($n) => str_repeat('<span class="cb">&nbsp;</span>', $n);
        return $e(2) . $sep . $e(2) . $sep . $e(4);
    }
    $mk = fn($s) => implode('', array_map(fn($c) => '<span class="cb">' . $c . '</span>', str_split($s)));
    return $mk($dob->format('d')) . $sep . $mk($dob->format('m')) . $sep . $mk($dob->format('Y'));
};
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>GOS 3 — {{ $patient->first_name }} {{ $patient->surname }}</title>
<style>
@page { size: A4; margin: 10mm; }
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { background: #e8e8e8; }
body { font-family: Arial, Helvetica, sans-serif; font-size: 9pt; color: #000; background: #e8e8e8; }
.page { max-width: 190mm; margin: 8mm auto; background: #fff; padding: 8mm 10mm; box-shadow: 0 2px 10px rgba(0,0,0,.25); }
.print-btn { position: fixed; top: 10px; right: 10px; background: #003087; color: #fff; border: none; padding: 7px 18px; font-size: 11pt; border-radius: 4px; cursor: pointer; z-index: 9999; }
.back-btn  { position: fixed; top: 10px; left: 10px; background: #003087; color: #fff; text-decoration: none; padding: 7px 18px; font-size: 11pt; border-radius: 4px; z-index: 9999; }
/* GOS3 header — amber/gold */
.form-hdr { display: flex; justify-content: space-between; align-items: flex-start; background: #ffb81c; padding: 6px 10px; margin-bottom: 6px; }
.form-hdr-title h1 { font-size: 12pt; font-weight: 900; color: #000; }
.form-hdr-title p  { font-size: 8pt; color: #222; }
.nhs-logo { font-size: 22pt; font-weight: 900; color: #003087; font-family: Arial Black, sans-serif; letter-spacing: -1pt; }
.instruct { background: #fff9e6; border: 1pt solid #ffb81c; padding: 4px 8px; font-size: 8.5pt; margin-bottom: 6px; }
/* Part headers — blue for GOS3 parts */
.part-hdr { background: #003087; color: #fff; font-weight: bold; font-size: 9.5pt; padding: 3px 8px; margin: 8px 0 4px; }
.part-hdr.amber { background: #ffb81c; color: #000; }
.section { border: 1pt solid #bbb; padding: 5px 7px; margin-bottom: 5px; }
.sub-hdr { font-weight: bold; font-size: 8.5pt; margin: 5px 0 3px; border-bottom: 0.5pt solid #ccc; padding-bottom: 2px; }
.cb { display: inline-block; width: 18px; height: 22px; border: 1px solid #555; text-align: center; line-height: 22px; font-size: 8pt; font-family: 'Courier New', monospace; vertical-align: middle; margin-right: 1px; }
.dob-sep { display: inline-block; width: 8px; text-align: center; vertical-align: middle; font-size: 9pt; line-height: 22px; }
.cbf { display: inline-flex; align-items: center; gap: 3px; flex-wrap: nowrap; white-space: nowrap; }
.fl  { display: flex; align-items: baseline; gap: 5px; margin-bottom: 4px; flex-wrap: wrap; }
.lbl { font-size: 8.5pt; font-weight: bold; white-space: nowrap; }
.g2  { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.g3  { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 6px; }
.chk { display: flex; align-items: flex-start; gap: 5px; margin-bottom: 3px; font-size: 8.5pt; line-height: 1.3; }
.chk input[type=checkbox], .chk input[type=radio] { flex-shrink: 0; width: 11px; height: 11px; margin-top: 1pt; cursor: pointer; }
.inp { border: none; border-bottom: 1pt solid #777; background: #fffff0; font-size: 9pt; padding: 1pt 2pt; vertical-align: baseline; font-family: inherit; }
.sig { border: 1px solid #000; min-height: 50px; height: 60px; background: #fff; display: block; width: 100%; margin-top: 4px; }
.opt-box { border: 2pt solid #003087; background: #f0f4ff; padding: 4px 7px; margin-top: 5px; font-size: 8.5pt; }
table { width: 100%; border-collapse: collapse; font-size: 8.5pt; }
th, td { border: 1pt solid #555; padding: 2px 4px; vertical-align: middle; }
th { background: #e0e0e0; text-align: center; font-weight: bold; }
.num-inp { width: 60px; border: none; background: #fffff0; font-size: 9pt; padding: 1pt 2pt; text-align: right; font-family: inherit; }
.pb { page-break-before: always; break-before: page; }
.pb-visual { border-top: 2px dashed #bbb; margin-top: 14px; padding-top: 14px; }
@media print {
    html, body { background: #fff; margin: 0; padding: 0; }
    .no-print { display: none !important; }
    .page { margin: 0; padding: 6mm 8mm; max-width: none; box-shadow: none; }
    .pb-visual { border-top: none; margin-top: 0; padding-top: 0; }
}
.save-btn { position: fixed; top: 10px; right: 100px; background: #16a34a; color: #fff; border: none; padding: 7px 18px; font-size: 11pt; border-radius: 4px; cursor: pointer; z-index: 9999; }
.save-btn:disabled { background: #6b7280; cursor: default; }
</style>
</head>
<meta name="csrf-token" content="{{ csrf_token() }}">
<body>

<button onclick="history.back()" class="back-btn no-print">&larr; {{ $source === 'egos' ? 'Back to eGOS' : 'Back to Patient' }}</button>
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
        body: JSON.stringify({ patient_id: {{ $patient->id }}, form_type: 'GOS3', form_data: fields, voucher_value: {{ $existingSubmission?->voucher_value ?? 'null' }} })
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
    initSignaturePad('sig-gos3-patient-part2',      'sig-gos3-patient-part2-data');
    initSignaturePad('sig-gos3-performer-rx-part3',   'sig-gos3-performer-rx-part3-data');
    initSignaturePad('sig-gos3-performer-disp-part3', 'sig-gos3-performer-disp-part3-data');
    initSignaturePad('sig-gos3-supplier-part3',      'sig-gos3-supplier-part3-data');
    initSignaturePad('sig-gos3-patient-part4',       'sig-gos3-patient-part4-data');

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
        <h1>GOS 3</h1>
        <p style="font-size:9.5pt; font-weight:bold; margin-top:2px;">NHS OPTICAL VOUCHER AND PATIENT'S STATEMENT</p>
        <p>06/20</p>
    </div>
    <svg xmlns="http://www.w3.org/2000/svg" width="72" height="38" viewBox="0 0 72 38" style="display:block;flex-shrink:0;">
        <rect width="72" height="38" fill="#003087" rx="2"/>
        <text x="36" y="28" text-anchor="middle" fill="#fff" font-family="Arial Black,Arial,sans-serif" font-weight="900" font-size="24" letter-spacing="-1">NHS</text>
    </svg>
</div>

<div class="instruct">
    <strong>To get your glasses or contact lenses</strong>, fill in, sign and date Part 2 and take this form to the optician of your choice.
    The optician will complete the rest of the form. Keep the yellow copy for your records.
</div>

{{-- ── PART 1 ───────────────────────────────────────────────────────────────── --}}
<div class="part-hdr">PART 1 — PATIENT'S DETAILS</div>
<div class="section">

    <div class="fl">
        <span class="cbf"><span class="lbl">Title</span>{!! $cb($patient->title, 4) !!}</span>
        <span class="cbf"><span class="lbl">First names</span>{!! $cb($patient->first_name, 15) !!}</span>
        <span class="cbf"><span class="lbl">Surname</span>{!! $cb($patient->surname, 20) !!}</span>
    </div>
    <div class="fl">
        <span class="lbl">Previous surname</span>{!! $cb('', 20) !!}
    </div>
    <div class="fl">
        <span class="lbl">Address</span>{!! $cb($patient->address_line_1, 30) !!}
    </div>
    <div class="fl">
        <span class="lbl" style="min-width:46px;">&nbsp;</span>{!! $cb($patient->town_city, 30) !!}
    </div>
    <div class="fl">
        <span class="lbl">Postcode</span>{!! $cb($patient->post_code, 8) !!}
    </div>
    <div class="fl" style="margin-top:4px;">
        <span class="lbl">Date of birth</span><input type="date" class="inp" style="width:130px;" value="{{ $patient->date_of_birth->format('Y-m-d') }}">
        <span class="lbl" style="margin-left:14px;">NHS No</span>
        <input type="text" class="inp" style="width:90px;">
        <span class="lbl" style="margin-left:10px;">N.I. No</span>
        <input type="text" class="inp" style="width:90px;">
    </div>

    {{-- GOS3 eligibility (no 60+, no glaucoma risk) --}}
    <div class="sub-hdr">Tick the box(es) that apply to you</div>
    <div class="g2">
        <div>
            <div class="chk"><input type="checkbox" {{ $isUnder16 || $isChildType ? 'checked' : '' }}><span>I am under 16</span></div>
            <div class="chk"><input type="checkbox" {{ $isStudent ? 'checked' : '' }}><span>I am a full time student aged 16, 17 or 18</span></div>
            <div class="chk"><input type="checkbox"><span>I am a prisoner on leave from prison</span></div>
            <div style="margin-left:16px; margin-top:3px; font-size:8pt;">
                Details of establishment &mdash;
                Name <input type="text" class="inp" style="width:110px;">
                Town <input type="text" class="inp" style="width:80px;">
            </div>
        </div>
        <div>
            <div class="chk"><input type="checkbox" {{ $isDiabGlauc || $isNHSType ? 'checked' : '' }}><span>I suffer from diabetes or have been diagnosed with glaucoma</span></div>
            <div class="chk"><input type="checkbox" {{ $isFHG ? 'checked' : '' }}><span>I am 40 or over and the parent, sibling or child of a person diagnosed with glaucoma</span></div>
        </div>
    </div>

    {{-- Benefits --}}
    <div class="sub-hdr" style="margin-top:6px;">I receive one of the following</div>
    <div class="g2">
        <div>
            <div class="chk"><input type="checkbox" {{ $hasIS ? 'checked' : '' }}><span>Income Support</span></div>
            <div class="chk"><input type="checkbox" {{ $hasUC ? 'checked' : '' }}><span>Universal Credit</span></div>
            <div class="chk"><input type="checkbox" {{ $hasPC ? 'checked' : '' }}><span>Pension Credit Guarantee Credit</span></div>
        </div>
        <div>
            <div class="chk"><input type="checkbox" {{ $hasJSA ? 'checked' : '' }}><span>Income-based Jobseeker's Allowance</span></div>
            <div class="chk"><input type="checkbox" {{ $hasESA ? 'checked' : '' }}><span>Income-related Employment and Support Allowance</span></div>
            <div class="chk"><input type="checkbox" {{ $hasTaxCredit ? 'checked' : '' }}><span>Tax Credit / NHS Tax Credit Exemption Certificate</span></div>
        </div>
    </div>
    <div class="fl" style="margin-top:5px; font-size:8.5pt;">
        <strong>Person getting benefit —</strong>
        Name <input type="text" class="inp" style="width:130px;">
        NI No <input type="text" class="inp" style="width:90px;">
        DOB <input type="date" class="inp" style="width:100px;">
    </div>

    {{-- HC2 / HC3 / Complex --}}
    <div class="fl" style="margin-top:5px; flex-wrap:wrap; gap:8px;">
        <div class="chk">
            <input type="checkbox" {{ $hasHC2 ? 'checked' : '' }}>
            <span>HC2 certificate &nbsp; No.&nbsp;<input type="text" class="inp" style="width:85px;"></span>
        </div>
        <div class="chk">
            <input type="checkbox" {{ $hasHC3 ? 'checked' : '' }}>
            <span>HC3 certificate &nbsp; No.&nbsp;<input type="text" class="inp" style="width:85px;">
            &nbsp; Box B reduction: £&nbsp;<input type="text" class="inp" style="width:55px;"></span>
        </div>
        <div class="chk">
            <input type="checkbox"><span>I need complex lenses</span>
        </div>
    </div>

    <div class="opt-box">
        <strong>For optician / contractor use only</strong> &nbsp;&mdash;&nbsp; Evidence of eligibility:
        <label class="chk" style="display:inline-flex; margin-left:12px;"><input type="checkbox">&nbsp;Seen</label>
        <label class="chk" style="display:inline-flex; margin-left:8px;"><input type="checkbox">&nbsp;Not seen</label>
    </div>
</div>

{{-- ── PART 2 ───────────────────────────────────────────────────────────────── --}}
<div class="part-hdr">PART 2 — PATIENT'S DECLARATION</div>
<div class="section">
    <p style="font-size:8.5pt; margin-bottom:6px;">
        I declare that the information I have given on this form is correct and complete. I understand that if it is not, appropriate action may be taken.
        I am applying for an NHS optical voucher and am entitled to one because of the reason(s) ticked in Part 1.
        I understand the voucher may only be used to obtain the glasses or contact lenses described in the prescription.
        I understand that if I choose glasses costing more than the voucher value, I must pay the difference.
    </p>
    <p style="font-size:8.5pt; margin-bottom:4px;"><strong>I am the (tick one):</strong></p>
    <div style="display:flex; gap:14px; flex-wrap:wrap; margin-bottom:8px;">
        <label class="chk"><input type="radio" name="decl_role">&nbsp;Patient</label>
        <label class="chk"><input type="radio" name="decl_role">&nbsp;Patient's parent</label>
        <label class="chk"><input type="radio" name="decl_role">&nbsp;Patient's carer or guardian</label>
        <label class="chk"><input type="radio" name="decl_role">&nbsp;I live at the same address as the patient</label>
    </div>
    <div class="g2">
        <div>
            <p style="font-size:8pt; font-weight:bold; margin-bottom:2px;">Signature</p>
            <canvas id="sig-gos3-patient-part2" width="400" height="80" style="display:block; width:100%; max-width:400px; height:80px; border:1px solid #000; background:#fff; cursor:crosshair; margin-top:4px;"></canvas>
            <input type="hidden" id="sig-gos3-patient-part2-data" name="sig-gos3-patient-part2">
            <button type="button" class="no-print" onclick="clearSig('sig-gos3-patient-part2')" style="font-size:7pt; color:#555; background:none; border:1px solid #bbb; border-radius:2px; padding:1px 6px; cursor:pointer; margin-top:2px;">Clear</button>
            <p class="no-print" style="font-size:7pt; color:#888; margin-top:1px; font-style:italic;">Sign here (use mouse or touch)</p>
            <p style="font-size:8.5pt; margin-top:4px;">Date&nbsp;<input type="date" class="inp" style="width:130px;" value="{{ date('Y-m-d') }}"></p>
        </div>
        <div>
            <p style="font-size:8pt; font-weight:bold; margin-bottom:2px;">Name</p>
            <p style="font-size:9pt;">{{ $patient->first_name }} {{ $patient->surname }}</p>
            <p style="font-size:8pt; font-weight:bold; margin-top:5px; margin-bottom:2px;">Address</p>
            <p style="font-size:8.5pt; line-height:1.4;">
                {{ $patient->address_line_1 }}<br>
                {{ $patient->town_city }}<br>
                @if($patient->county){{ $patient->county }}<br>@endif
                {{ $patient->post_code }}
            </p>
        </div>
    </div>
    <div class="fl" style="margin-top:7px; padding-top:6px; border-top:1pt solid #ccc;">
        <span class="lbl">Voucher code</span>
        <input type="text" class="inp" style="width:80px;">
        <span class="lbl" style="margin-left:20px;">Authorisation code</span>
        <input type="text" class="inp" style="width:100px;">
    </div>
</div>

{{-- ── PAGE 2 ───────────────────────────────────────────────────────────────── --}}
<div class="pb pb-visual">

    {{-- PART 3 --}}
    <div class="part-hdr">PART 3 — SUPPLIER'S DECLARATION</div>
    <div class="section">

        {{-- Prescription table --}}
        <div class="sub-hdr">Prescription</div>
        <table style="margin-bottom:6px;">
            <thead>
                <tr>
                    <th style="width:60px;"></th>
                    <th>+/&minus; Sph</th>
                    <th>+/&minus; Cyl</th>
                    <th>Axis</th>
                    <th>Prism</th>
                    <th>Base</th>
                    <th>Add</th>
                    <th>VA</th>
                </tr>
            </thead>
            <tbody>
                @foreach(['RIGHT' => 'R', 'LEFT' => 'L'] as $label => $abbr)
                <tr>
                    <td style="font-weight:bold; text-align:center;">{{ $label }}</td>
                    @foreach(['sph','cyl','axis','prism','base','add','va'] as $col)
                    <td><input type="text" class="inp" style="width:100%;"></td>
                    @endforeach
                </tr>
                @endforeach
                <tr>
                    <td style="font-weight:bold; text-align:center;">ADD</td>
                    @foreach(['sph','cyl','axis','prism','base','add','va'] as $col)
                    <td><input type="text" class="inp" style="width:100%;"></td>
                    @endforeach
                </tr>
            </tbody>
        </table>

        {{-- Voucher types --}}
        <div class="g2" style="margin-bottom:6px;">
            <div>
                <span class="lbl">Distance / Bifocal voucher type</span>
                <input type="text" class="inp" style="width:50px; margin-left:4px;">
                <div style="margin-top:4px; display:flex; gap:10px; flex-wrap:wrap;">
                    <label class="chk"><input type="checkbox">&nbsp;Complex</label>
                    <label class="chk"><input type="checkbox">&nbsp;Prism supplement</label>
                    <label class="chk"><input type="checkbox">&nbsp;Tint supplement</label>
                </div>
            </div>
            <div>
                <span class="lbl">Reading voucher type</span>
                <input type="text" class="inp" style="width:50px; margin-left:4px;">
                <div style="margin-top:4px; display:flex; gap:10px; flex-wrap:wrap;">
                    <label class="chk"><input type="checkbox">&nbsp;Complex</label>
                    <label class="chk"><input type="checkbox">&nbsp;Prism supplement</label>
                    <label class="chk"><input type="checkbox">&nbsp;Tint supplement</label>
                </div>
            </div>
        </div>

        {{-- Performer details --}}
        <div class="fl">
            <span class="lbl">Performer's name</span>
            <input type="text" class="inp" style="width:160px;">
            <span class="lbl" style="margin-left:8px;">List number</span>
            <input type="text" class="inp" style="width:75px;">
        </div>
        <div class="g2" style="margin-top:4px;">
            <div>
                <p style="font-size:8pt; margin-bottom:2px;">Signature (prescription)</p>
                <canvas id="sig-gos3-performer-rx-part3" width="400" height="80" style="display:block; width:100%; max-width:400px; height:80px; border:1px solid #000; background:#fff; cursor:crosshair; margin-top:4px;"></canvas>
                <input type="hidden" id="sig-gos3-performer-rx-part3-data" name="sig-gos3-performer-rx-part3">
                <button type="button" class="no-print" onclick="clearSig('sig-gos3-performer-rx-part3')" style="font-size:7pt; color:#555; background:none; border:1px solid #bbb; border-radius:2px; padding:1px 6px; cursor:pointer; margin-top:2px;">Clear</button>
                <p class="no-print" style="font-size:7pt; color:#888; margin-top:1px; font-style:italic;">Sign here (use mouse or touch)</p>
                <p style="font-size:8.5pt; margin-top:3px;">Date of prescription&nbsp;<input type="date" class="inp" style="width:130px;"></p>
            </div>
            <div>
                <p style="font-size:8pt; margin-bottom:2px;">Signature (dispensing)</p>
                <canvas id="sig-gos3-performer-disp-part3" width="400" height="80" style="display:block; width:100%; max-width:400px; height:80px; border:1px solid #000; background:#fff; cursor:crosshair; margin-top:4px;"></canvas>
                <input type="hidden" id="sig-gos3-performer-disp-part3-data" name="sig-gos3-performer-disp-part3">
                <button type="button" class="no-print" onclick="clearSig('sig-gos3-performer-disp-part3')" style="font-size:7pt; color:#555; background:none; border:1px solid #bbb; border-radius:2px; padding:1px 6px; cursor:pointer; margin-top:2px;">Clear</button>
                <p class="no-print" style="font-size:7pt; color:#888; margin-top:1px; font-style:italic;">Sign here (use mouse or touch)</p>
                <p style="font-size:8.5pt; margin-top:3px;">Date&nbsp;<input type="date" class="inp" style="width:130px;"></p>
            </div>
        </div>

        {{-- Supplier declaration checkboxes --}}
        <div class="sub-hdr" style="margin-top:6px;">I am supplying (tick all that apply)</div>
        <div class="g2">
            <div>
                <div class="chk"><input type="checkbox"><span>Contact lenses</span></div>
                <div class="chk"><input type="checkbox"><span>Glasses</span></div>
                <div class="chk"><input type="checkbox"><span>Distance / bifocal</span></div>
                <div class="chk"><input type="checkbox"><span>Near (reading)</span></div>
            </div>
            <div>
                <div class="chk"><input type="checkbox"><span>New prescription</span></div>
                <div class="chk"><input type="checkbox"><span>Fair wear and tear replacement</span></div>
            </div>
        </div>

        {{-- CLAIM supplements --}}
        <div class="sub-hdr" style="margin-top:6px;">CLAIM — Supplements</div>
        <div class="g2">
            @foreach(['1st pair', '2nd pair'] as $pair)
            <div>
                <p style="font-size:8pt; font-weight:bold; margin-bottom:3px;">{{ $pair }}</p>
                @foreach(['Prism', 'Tint', 'Small glasses (mm)', 'Special facial characteristics', 'Prism controlled bifocals'] as $supp)
                <div class="chk"><input type="checkbox"><span>{{ $supp }}</span></div>
                @endforeach
            </div>
            @endforeach
        </div>

        {{-- Financial table --}}
        <div class="sub-hdr" style="margin-top:6px;">Financial Summary</div>
        <table style="width:auto; min-width:300px;">
            <thead>
                <tr><th style="width:200px;">Item</th><th style="width:70px;">1st pair £</th><th style="width:70px;">2nd pair £</th><th style="width:70px;">Total £</th></tr>
            </thead>
            <tbody>
                <tr>
                    <td>Actual retail cost</td>
                    <td><input type="number" class="inp num-inp"></td>
                    <td><input type="number" class="inp num-inp"></td>
                    <td><input type="number" class="inp num-inp"></td>
                </tr>
                <tr>
                    <td>Total vouchers</td>
                    <td><input type="number" class="inp num-inp"></td>
                    <td><input type="number" class="inp num-inp"></td>
                    <td><input type="number" class="inp num-inp"></td>
                </tr>
                <tr>
                    <td>Patient HC3 contribution</td>
                    <td colspan="2"></td>
                    <td><input type="number" class="inp num-inp"></td>
                </tr>
                <tr style="font-weight:bold;">
                    <td>Total claim</td>
                    <td colspan="2"></td>
                    <td><input type="number" class="inp num-inp"></td>
                </tr>
            </tbody>
        </table>

        <p style="font-size:8pt; margin:7px 0; line-height:1.4;">
            I declare that the information given on this form is correct and complete. I confirm I have dispensed the glasses or contact lenses specified above in accordance with the prescription provided.
            I agree to make records available to NHS England on request.
        </p>

        <div class="g2" style="margin-top:5px;">
            <div>
                <div class="fl"><span class="lbl">Date 1st pair supplied</span><input type="date" class="inp" style="width:130px;"></div>
                <div class="fl"><span class="lbl">Date 2nd pair supplied</span><input type="date" class="inp" style="width:130px;"></div>
            </div>
            <div>
                <p style="font-size:8pt; font-weight:bold; margin-bottom:2px;">Supplier's signature</p>
                <canvas id="sig-gos3-supplier-part3" width="400" height="80" style="display:block; width:100%; max-width:400px; height:80px; border:1px solid #000; background:#fff; cursor:crosshair; margin-top:4px;"></canvas>
                <input type="hidden" id="sig-gos3-supplier-part3-data" name="sig-gos3-supplier-part3">
                <button type="button" class="no-print" onclick="clearSig('sig-gos3-supplier-part3')" style="font-size:7pt; color:#555; background:none; border:1px solid #bbb; border-radius:2px; padding:1px 6px; cursor:pointer; margin-top:2px;">Clear</button>
                <p class="no-print" style="font-size:7pt; color:#888; margin-top:1px; font-style:italic;">Sign here (use mouse or touch)</p>
                <p style="font-size:8.5pt; margin-top:3px;">Name&nbsp;<input type="text" class="inp" style="width:130px;"></p>
            </div>
        </div>

        <div class="fl" style="margin-top:5px; border-top:1pt solid #ccc; padding-top:5px;">
            <span class="lbl">Supplier's name</span>
            <input type="text" class="inp" style="width:180px;">
            <span class="lbl" style="margin-left:12px;">Organisation number</span>
            <input type="text" class="inp" style="width:90px;">
        </div>
    </div>

    {{-- PART 4 --}}
    <div class="part-hdr" style="page-break-before:always; break-before:page;">PART 4 — PATIENT'S DECLARATION ON COLLECTION</div>
    <div class="section" style="page-break-inside:avoid; break-inside:avoid;">
        <div class="fl" style="margin-bottom:5px;">
            <p style="font-size:8.5pt;"><strong>I confirm that I have received:</strong></p>
            <label class="chk" style="margin-left:10px;"><input type="checkbox">&nbsp;Distance glasses</label>
            <label class="chk" style="margin-left:6px;"><input type="checkbox">&nbsp;Near glasses</label>
            <label class="chk" style="margin-left:6px;"><input type="checkbox">&nbsp;Bifocal / varifocal glasses</label>
            <label class="chk" style="margin-left:6px;"><input type="checkbox">&nbsp;Contact lenses</label>
            &nbsp;&nbsp;No. of pairs:&nbsp;<input type="text" class="inp" style="width:35px;">
        </div>

        <p style="font-size:8.5pt; margin-bottom:6px;">
            I declare that I have received the glasses / contact lenses described above. I understand that if any information I have given is incorrect, appropriate action may be taken.
        </p>
        <p style="font-size:8.5pt; margin-bottom:4px;"><strong>I am the (tick one):</strong></p>
        <div style="display:flex; gap:14px; flex-wrap:wrap; margin-bottom:8px;">
            <label class="chk"><input type="radio" name="coll_role">&nbsp;Patient</label>
            <label class="chk"><input type="radio" name="coll_role">&nbsp;Patient's parent</label>
            <label class="chk"><input type="radio" name="coll_role">&nbsp;Patient's carer or guardian</label>
            <label class="chk"><input type="radio" name="coll_role">&nbsp;I live at the same address as the patient</label>
        </div>
        <div class="g2">
            <div>
                <p style="font-size:8pt; font-weight:bold; margin-bottom:2px;">Signature</p>
                <canvas id="sig-gos3-patient-part4" width="400" height="80" style="display:block; width:100%; max-width:400px; height:80px; border:1px solid #000; background:#fff; cursor:crosshair; margin-top:4px;"></canvas>
                <input type="hidden" id="sig-gos3-patient-part4-data" name="sig-gos3-patient-part4">
                <button type="button" class="no-print" onclick="clearSig('sig-gos3-patient-part4')" style="font-size:7pt; color:#555; background:none; border:1px solid #bbb; border-radius:2px; padding:1px 6px; cursor:pointer; margin-top:2px;">Clear</button>
                <p class="no-print" style="font-size:7pt; color:#888; margin-top:1px; font-style:italic;">Sign here (use mouse or touch)</p>
                <p style="font-size:8.5pt; margin-top:4px;">Date&nbsp;<input type="date" class="inp" style="width:130px;"></p>
            </div>
            <div>
                <p style="font-size:8pt; font-weight:bold; margin-bottom:2px;">Name</p>
                <p style="font-size:9pt;">{{ $patient->first_name }} {{ $patient->surname }}</p>
                <p style="font-size:8pt; font-weight:bold; margin-top:5px; margin-bottom:2px;">Address</p>
                <p style="font-size:8.5pt; line-height:1.4;">
                    {{ $patient->address_line_1 }}<br>
                    {{ $patient->town_city }}<br>
                @if($patient->county){{ $patient->county }}<br>@endif
                    {{ $patient->post_code }}
                </p>
            </div>
        </div>
    </div>

</div>{{-- /page break --}}
</div>{{-- /page --}}
</body>
</html>
