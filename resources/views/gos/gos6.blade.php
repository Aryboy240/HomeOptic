@php
use App\Enums\PatientType;

$age      = $patient->date_of_birth->age;
$benefits = $patient->benefits ?? [];
$ptype    = $patient->patient_type;

$is60orOver   = $age >= 60;
$isUnder16    = $age < 16;
$isFHG        = $ptype === PatientType::FamilyHistory;
$isStudent    = $patient->in_full_time_education && $age >= 16 && $age <= 18;
$isDiabGlauc  = $patient->is_diabetic || $patient->has_glaucoma;
$isGlaucRisk  = $ptype === PatientType::Glaucoma;
$isBlind      = $patient->is_blind_partially_sighted;

$hasIS        = in_array('income_support',           $benefits);
$hasUC        = in_array('universal_credit',         $benefits);
$hasPC        = in_array('pension_credit',           $benefits);
$hasJSA       = in_array('jobseekers_allowance',     $benefits);
$hasESA       = in_array('esa',                      $benefits);
$hasTaxCredit = in_array('nhs_tax_credit_exemption', $benefits);
$hasHC2       = in_array('hc2_certificate',          $benefits);

// Domiciliary reason pre-fill
$domReason = $patient->domiciliary_reason?->label() ?? '';
$physDis   = $patient->physical_disabilities ?? '';
$mentalH   = $patient->mental_health_conditions ?? '';
$domLine2  = implode('; ', array_filter([$physDis, $mentalH]));

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

$dobBoxes = function (?\Illuminate\Support\Carbon $dob) use ($cb): string {
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
<title>GOS 6 — {{ $patient->first_name }} {{ $patient->surname }}</title>
<style>
@page { size: A4; margin: 10mm; }
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { background: #e8e8e8; }
body { font-family: Arial, Helvetica, sans-serif; font-size: 9pt; color: #000; background: #e8e8e8; }
.page { max-width: 190mm; margin: 8mm auto; background: #fff; padding: 8mm 10mm; box-shadow: 0 2px 10px rgba(0,0,0,.25); }
.print-btn { position: fixed; top: 10px; right: 10px; background: #003087; color: #fff; border: none; padding: 7px 18px; font-size: 11pt; border-radius: 4px; cursor: pointer; z-index: 9999; }
.back-btn  { position: fixed; top: 10px; left: 10px; background: #003087; color: #fff; text-decoration: none; padding: 7px 18px; font-size: 11pt; border-radius: 4px; z-index: 9999; }
.form-hdr { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3pt solid #003087; padding-bottom: 4px; margin-bottom: 8px; }
.form-hdr-title h1 { font-size: 12pt; font-weight: 900; color: #003087; }
.form-hdr-title p  { font-size: 8pt; color: #444; }
.nhs-logo { font-size: 22pt; font-weight: 900; color: #003087; font-family: Arial Black, sans-serif; letter-spacing: -1pt; }
.pvn-box { border: 1pt solid #003087; padding: 3px 6px; font-size: 8.5pt; text-align: right; }
.part-hdr { background: #003087; color: #fff; font-weight: bold; font-size: 9.5pt; padding: 3px 8px; margin: 8px 0 4px; }
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
        body: JSON.stringify({ patient_id: {{ $patient->id }}, form_type: 'GOS6', form_data: fields, voucher_value: {{ $existingSubmission?->voucher_value ?? 'null' }} })
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
    initSignaturePad('sig-gos6-patient-part2',    'sig-gos6-patient-part2-data');
    initSignaturePad('sig-gos6-performer-part3',  'sig-gos6-performer-part3-data');
    initSignaturePad('sig-gos6-contractor-part3', 'sig-gos6-contractor-part3-data');

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
        <h1>GOS 6</h1>
        <p style="font-size:9.5pt; font-weight:bold; margin-top:2px;">APPLICATION FOR A MOBILE NHS FUNDED SIGHT TEST</p>
        <p>06/20</p>
    </div>
    <div style="text-align:right;">
        <svg xmlns="http://www.w3.org/2000/svg" width="72" height="38" viewBox="0 0 72 38" style="display:block;flex-shrink:0;">
            <rect width="72" height="38" fill="#003087" rx="2"/>
            <text x="36" y="28" text-anchor="middle" fill="#fff" font-family="Arial Black,Arial,sans-serif" font-weight="900" font-size="24" letter-spacing="-1">NHS</text>
        </svg>
        <div class="pvn-box" style="margin-top:4px;">
            Pre-Visit Notification ref no: P&nbsp;&ndash;&nbsp;<input type="text" class="inp" style="width:90px;">
        </div>
    </div>
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
    <div class="fl">
        <span class="lbl">Date of last sight test</span>
        <input type="date" class="inp" style="width:130px;">
        <label class="chk" style="margin-left:10px;"><input type="checkbox">&nbsp;First test</label>
        <label class="chk" style="margin-left:6px;"><input type="checkbox">&nbsp;Not known</label>
    </div>

    {{-- Domiciliary reason --}}
    <div style="margin-top:7px; border:1pt solid #003087; padding:5px 7px; background:#f8f8ff;">
        <p style="font-size:8.5pt; font-weight:bold; margin-bottom:4px;">I cannot attend a practice unaccompanied for a sight test because:</p>
        <div class="fl"><input type="text" class="inp" style="width:100%;" value="{{ $domReason }}"></div>
        <div class="fl"><input type="text" class="inp" style="width:100%;" value="{{ $domLine2 }}"></div>
    </div>

    {{-- Eligibility --}}
    <div class="sub-hdr" style="margin-top:7px;">Tick the box(es) that apply to you</div>
    <div class="g2">
        <div>
            <div class="chk"><input type="checkbox" {{ $is60orOver ? 'checked' : '' }}><span>I am 60 or over</span></div>
            <div class="chk"><input type="checkbox" {{ $isUnder16 ? 'checked' : '' }}><span>I am under 16</span></div>
            <div class="chk"><input type="checkbox" {{ $isFHG ? 'checked' : '' }}><span>I am 40 or over and the parent, sibling or child of a person who has been diagnosed with glaucoma</span></div>
            <div class="chk"><input type="checkbox" {{ $isStudent ? 'checked' : '' }}><span>I am a full time student aged 16, 17 or 18</span></div>
            <div class="chk"><input type="checkbox"><span>I am a prisoner on leave from prison</span></div>
            <div style="margin-left:16px; margin-top:3px; font-size:8pt;">
                Details of establishment &mdash;
                Name <input type="text" class="inp" style="width:110px;">
                Town <input type="text" class="inp" style="width:80px;">
            </div>
        </div>
        <div>
            <div class="chk"><input type="checkbox" {{ $isDiabGlauc ? 'checked' : '' }}><span>I suffer from diabetes or have been diagnosed with glaucoma</span></div>
            <div class="chk"><input type="checkbox" {{ $isGlaucRisk ? 'checked' : '' }}><span>I am considered to be at risk of glaucoma by a consultant ophthalmologist</span></div>
            <div class="chk"><input type="checkbox" {{ $isBlind ? 'checked' : '' }}><span>I am registered as blind or partially sighted</span></div>
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
    <div class="fl" style="margin-top:5px;">
        <div class="chk">
            <input type="checkbox" {{ $hasHC2 ? 'checked' : '' }}>
            <span>I hold a valid HC2 certificate &nbsp; No.&nbsp;<input type="text" class="inp" style="width:90px;"></span>
        </div>
        <div class="chk" style="margin-left:20px;">
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
<div style="page-break-inside:avoid; break-inside:avoid;">
<div class="part-hdr">PART 2 — PATIENT'S DECLARATION</div>
<div class="section">
    <p style="font-size:8.5pt; margin-bottom:6px;">
        I declare that the information I have given on this form is correct and complete. I understand that if it is not, appropriate action may be taken.
        I am applying for a free mobile NHS sight test and am entitled to receive one because of the reason(s) ticked in Part 1.
        I confirm that I am unable to travel to an optical practice unaccompanied for the reason(s) stated above.
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
            <canvas id="sig-gos6-patient-part2" width="400" height="80" style="display:block; width:100%; max-width:400px; height:80px; border:1px solid #000; background:#fff; cursor:crosshair; margin-top:4px;"></canvas>
            <input type="hidden" id="sig-gos6-patient-part2-data" name="sig-gos6-patient-part2">
            <button type="button" class="no-print" onclick="clearSig('sig-gos6-patient-part2')" style="font-size:7pt; color:#555; background:none; border:1px solid #bbb; border-radius:2px; padding:1px 6px; cursor:pointer; margin-top:2px;">Clear</button>
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
</div>
</div>

{{-- ── PAGE 2 ───────────────────────────────────────────────────────────────── --}}
<div class="pb pb-visual">

    {{-- Ethnic group --}}
    <div class="part-hdr" style="background:#555;">ETHNIC GROUP (OPTIONAL — for monitoring purposes only)</div>
    <div class="section">
        <div class="g3">
            @foreach([
                'A' => 'White — British',           'B' => 'White — Irish',
                'C' => 'White — Any other White',   'D' => 'Mixed — White and Black Caribbean',
                'E' => 'Mixed — White and Black African', 'F' => 'Mixed — White and Asian',
                'G' => 'Mixed — Any other Mixed',   'H' => 'Asian — Indian',
                'J' => 'Asian — Pakistani',          'K' => 'Asian — Bangladeshi',
                'L' => 'Asian — Any other Asian',   'M' => 'Black — Caribbean',
                'N' => 'Black — African',            'P' => 'Black — Any other Black',
                'R' => 'Chinese',                    'S' => 'Any other ethnic group',
                'Z' => 'Not stated',
            ] as $code => $label)
                <label class="chk"><input type="checkbox"><span><strong>{{ $code }}</strong>&nbsp;{{ $label }}</span></label>
            @endforeach
        </div>
    </div>

    {{-- PART 3 — GOS6 specific --}}
    <div class="part-hdr">PART 3 — PERFORMER'S DECLARATION (DOMICILIARY)</div>
    <div class="section">
        <div class="fl">
            <span class="lbl">Date of sight test</span>
            <input type="date" class="inp" style="width:130px;">
            <span class="lbl" style="margin-left:16px;">Re-test code</span>
            <input type="text" class="inp" style="width:55px;">
        </div>

        {{-- Domiciliary visit type --}}
        <div class="sub-hdr">This was a domiciliary visit for:</div>
        <div style="display:flex; gap:20px; flex-wrap:wrap; margin-bottom:6px;">
            <label class="chk"><input type="radio" name="dom_visit">&nbsp;one patient at the address</label>
            <label class="chk"><input type="radio" name="dom_visit">&nbsp;several patients at the address</label>
        </div>
        <div class="fl" style="margin-bottom:6px;">
            <span class="lbl">This patient was the:</span>
            <label class="chk" style="margin-left:8px;"><input type="radio" name="pat_order">&nbsp;1st patient</label>
            <label class="chk" style="margin-left:8px;"><input type="radio" name="pat_order">&nbsp;2nd patient</label>
            <label class="chk" style="margin-left:8px;"><input type="radio" name="pat_order">&nbsp;3rd or subsequent patient</label>
        </div>

        {{-- Outcomes --}}
        <div class="sub-hdr">Following the sight test (tick all that apply):</div>
        <div class="g2">
            <div>
                <div class="chk"><input type="checkbox"><span>Patient referred to a doctor or hospital</span></div>
                <div class="chk"><input type="checkbox"><span>New or changed prescription issued</span></div>
                <div class="chk"><input type="checkbox"><span>Statement in lieu of prescription issued</span></div>
                <div class="chk"><input type="checkbox"><span>Patient was added / substituted on the day</span></div>
            </div>
            <div>
                <div class="chk"><input type="checkbox"><span>Unchanged prescription</span></div>
                <div class="chk"><input type="checkbox"><span>Optical voucher issued</span></div>
            </div>
        </div>

        {{-- Voucher table --}}
        <table style="margin:6px 0; width:auto;">
            <thead>
                <tr>
                    <th style="width:130px;">Voucher type</th>
                    <th style="width:60px;">Complex</th>
                    <th style="width:60px;">Prism</th>
                    <th style="width:60px;">Tint</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Distance / Bifocal &nbsp;<input type="text" class="inp" style="width:35px;"></td>
                    <td style="text-align:center;"><input type="checkbox"></td>
                    <td style="text-align:center;"><input type="checkbox"></td>
                    <td style="text-align:center;"><input type="checkbox"></td>
                </tr>
                <tr>
                    <td>Reading &nbsp;<input type="text" class="inp" style="width:35px;"></td>
                    <td style="text-align:center;"><input type="checkbox"></td>
                    <td style="text-align:center;"><input type="checkbox"></td>
                    <td style="text-align:center;"><input type="checkbox"></td>
                </tr>
            </tbody>
        </table>

        {{-- Performer --}}
        <div class="fl">
            <span class="lbl">Performer's name</span>
            <input type="text" class="inp" style="width:160px;">
            <span class="lbl" style="margin-left:10px;">Performers list number</span>
            <input type="text" class="inp" style="width:75px;">
            <span class="lbl" style="margin-left:10px;">Date</span>
            <input type="date" class="inp" style="width:130px;">
        </div>
        <div class="fl" style="margin-top:3px; align-items:flex-start;">
            <span class="lbl">Performer's signature</span>
            <div>
                <canvas id="sig-gos6-performer-part3" width="300" height="70" style="display:block; width:300px; height:70px; border:1px solid #000; background:#fff; cursor:crosshair;"></canvas>
                <input type="hidden" id="sig-gos6-performer-part3-data" name="sig-gos6-performer-part3">
                <button type="button" class="no-print" onclick="clearSig('sig-gos6-performer-part3')" style="font-size:7pt; color:#555; background:none; border:1px solid #bbb; border-radius:2px; padding:1px 6px; cursor:pointer; margin-top:2px;">Clear</button>
                <p class="no-print" style="font-size:7pt; color:#888; margin-top:1px; font-style:italic;">Sign here (use mouse or touch)</p>
            </div>
        </div>

        {{-- CLAIM section --}}
        <div style="border:2pt solid #003087; padding:5px 7px; margin-top:8px; background:#f0f4ff;">
            <p style="font-weight:bold; font-size:9pt; margin-bottom:5px;">CLAIM — tick fees claimed:</p>
            <div class="chk"><input type="checkbox"><span>Current NHS sight test fee</span></div>
            <div class="chk"><input type="checkbox"><span>Domiciliary fee for 1st or 2nd patient at the address</span></div>
            <div class="chk"><input type="checkbox"><span>Domiciliary fee for 3rd or subsequent patient at the address</span></div>
            <div class="fl" style="margin-top:5px;">
                <span class="lbl">Address where sight test took place</span>
                <input type="text" class="inp" style="width:200px;" value="{{ $patient->address_line_1 }}">
                <span class="lbl" style="margin-left:8px;">Postcode</span>
                <input type="text" class="inp" style="width:70px;" value="{{ $patient->post_code }}">
            </div>
        </div>

        <p style="font-size:8pt; margin:7px 0; line-height:1.4;">
            I declare that I am included in a Performers List and am contracted to undertake NHS primary ophthalmic services.
            I have personally carried out this domiciliary sight test. I certify that the patient is eligible for an NHS sight test and for a domiciliary visit for the reason(s) shown.
            I agree to maintain records of this sight test and make them available to NHS England on request.
        </p>

        <div style="border-top:1pt solid #bbb; padding-top:7px; margin-top:7px;">
            <p style="font-weight:bold; font-size:9pt; margin-bottom:5px;">Contractor</p>
            <div class="g2">
                <div>
                    <p style="font-size:8pt; font-weight:bold; margin-bottom:2px;">Signature</p>
                    <canvas id="sig-gos6-contractor-part3" width="400" height="80" style="display:block; width:100%; max-width:400px; height:80px; border:1px solid #000; background:#fff; cursor:crosshair; margin-top:4px;"></canvas>
                    <input type="hidden" id="sig-gos6-contractor-part3-data" name="sig-gos6-contractor-part3">
                    <button type="button" class="no-print" onclick="clearSig('sig-gos6-contractor-part3')" style="font-size:7pt; color:#555; background:none; border:1px solid #bbb; border-radius:2px; padding:1px 6px; cursor:pointer; margin-top:2px;">Clear</button>
                    <p class="no-print" style="font-size:7pt; color:#888; margin-top:1px; font-style:italic;">Sign here (use mouse or touch)</p>
                    <p style="font-size:8.5pt; margin-top:4px;">Date&nbsp;<input type="date" class="inp" style="width:130px;"></p>
                    <p style="font-size:8.5pt; margin-top:3px;">Name&nbsp;<input type="text" class="inp" style="width:150px;"></p>
                </div>
                <div>
                    <p style="font-size:8pt; font-weight:bold; margin-bottom:2px;">Contractor's name</p>
                    <input type="text" class="inp" style="width:100%;">
                    <p style="font-size:8pt; font-weight:bold; margin-top:8px; margin-bottom:2px;">Organisation number</p>
                    <input type="text" class="inp" style="width:100px;">
                </div>
            </div>
        </div>
    </div>

</div>{{-- /page break --}}
</div>{{-- /page --}}
</body>
</html>
