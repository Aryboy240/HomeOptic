@php
use App\Enums\PatientType;

$age      = $patient->date_of_birth->age;
$benefits = $patient->benefits ?? [];
$ptype    = $patient->patient_type;

// ── Eligibility flags ─────────────────────────────────────────────────────
$is60orOver    = $age >= 60;
$isUnder16     = $age < 16;
$isFHG         = $ptype === PatientType::FamilyHistory;
$isStudent     = $patient->in_full_time_education && $age >= 16 && $age <= 18;
$isDiabGlauc   = $patient->is_diabetic || $patient->has_glaucoma;
$isGlaucRisk   = $ptype === PatientType::Glaucoma;
$isBlind       = $patient->is_blind_partially_sighted;

// ── Benefits ─────────────────────────────────────────────────────────────
$hasIS         = in_array('income_support',           $benefits);
$hasUC         = in_array('universal_credit',         $benefits);
$hasPC         = in_array('pension_credit',           $benefits);
$hasJSA        = in_array('jobseekers_allowance',     $benefits);
$hasESA        = in_array('esa',                      $benefits);
$hasTaxCredit  = in_array('nhs_tax_credit_exemption', $benefits);
$hasHC2        = in_array('hc2_certificate',          $benefits);

// ── Character-box helpers ─────────────────────────────────────────────────
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
<title>GOS 1 — {{ $patient->first_name }} {{ $patient->surname }}</title>
<style>
@page { size: A4; margin: 10mm; }
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { background: #e8e8e8; }
body { font-family: Arial, Helvetica, sans-serif; font-size: 9pt; color: #000; background: #e8e8e8; }
.page { max-width: 190mm; margin: 8mm auto; background: #fff; padding: 8mm 10mm; box-shadow: 0 2px 10px rgba(0,0,0,.25); }
.print-btn { position: fixed; top: 10px; right: 10px; background: #003087; color: #fff; border: none; padding: 7px 18px; font-size: 11pt; border-radius: 4px; cursor: pointer; z-index: 9999; }
/* Header */
.form-hdr { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3pt solid #003087; padding-bottom: 4px; margin-bottom: 8px; }
.form-hdr-title h1 { font-size: 12pt; font-weight: 900; color: #003087; letter-spacing: 0.5pt; }
.form-hdr-title p  { font-size: 8pt; color: #444; }
.nhs-logo { font-size: 22pt; font-weight: 900; color: #003087; font-family: Arial Black, sans-serif; letter-spacing: -1pt; line-height: 1; }
/* Part headers */
.part-hdr { background: #003087; color: #fff; font-weight: bold; font-size: 9.5pt; padding: 3px 8px; margin: 8px 0 4px; }
/* Sections */
.section { border: 1pt solid #bbb; padding: 5px 7px; margin-bottom: 5px; }
.sub-hdr { font-weight: bold; font-size: 8.5pt; margin: 5px 0 3px; border-bottom: 0.5pt solid #ccc; padding-bottom: 2px; }
/* Character boxes */
.cb { display: inline-block; width: 18px; height: 22px; border: 1px solid #555; text-align: center; line-height: 22px; font-size: 8pt; font-family: 'Courier New', monospace; vertical-align: middle; margin-right: 1px; }
.dob-sep { display: inline-block; width: 8px; text-align: center; vertical-align: middle; font-size: 9pt; line-height: 22px; }
.cbf { display: inline-flex; align-items: center; gap: 3px; flex-wrap: nowrap; white-space: nowrap; }
/* Layout helpers */
.fl   { display: flex; align-items: baseline; gap: 5px; margin-bottom: 4px; flex-wrap: wrap; }
.lbl  { font-size: 8.5pt; font-weight: bold; white-space: nowrap; }
.g2   { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.g3   { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 6px; }
/* Checkboxes */
.chk { display: flex; align-items: flex-start; gap: 5px; margin-bottom: 3px; font-size: 8.5pt; line-height: 1.3; }
.chk input[type=checkbox], .chk input[type=radio] { flex-shrink: 0; width: 11px; height: 11px; margin-top: 1pt; cursor: pointer; }
/* Editable inputs */
.inp { border: none; border-bottom: 1pt solid #777; background: #fffff0; font-size: 9pt; padding: 1pt 2pt; vertical-align: baseline; font-family: inherit; }
.inp:focus { outline: 1px solid #003087; background: #fffde7; }
/* Signature boxes */
.sig { border: 1px solid #000; min-height: 50px; height: 60px; background: #fff; display: block; width: 100%; margin-top: 4px; }
/* Optician box */
.opt-box { border: 2pt solid #003087; background: #f0f4ff; padding: 4px 7px; margin-top: 5px; font-size: 8.5pt; }
/* Tables */
table { width: 100%; border-collapse: collapse; font-size: 8.5pt; }
th, td { border: 1pt solid #555; padding: 2px 4px; vertical-align: middle; }
th { background: #e0e0e0; text-align: center; font-weight: bold; }
/* Page break */
.pb { page-break-before: always; break-before: page; }
.pb-visual { border-top: 2px dashed #bbb; margin-top: 14px; padding-top: 14px; }
@media print {
    html, body { background: #fff; margin: 0; padding: 0; }
    .no-print { display: none !important; }
    .page { margin: 0; padding: 6mm 8mm; max-width: none; box-shadow: none; }
    .pb-visual { border-top: none; margin-top: 0; padding-top: 0; }
}
</style>
</head>
<body>

<button class="print-btn no-print" onclick="window.print()">&#128438; Print</button>

<div class="page">

{{-- ── Header ──────────────────────────────────────────────────────────────── --}}
<div class="form-hdr">
    <div class="form-hdr-title">
        <h1>GOS 1</h1>
        <p style="font-size:9.5pt; font-weight:bold; margin-top:2px;">APPLICATION FOR AN NHS FUNDED SIGHT TEST</p>
        <p>06/20</p>
    </div>
    <svg xmlns="http://www.w3.org/2000/svg" width="72" height="38" viewBox="0 0 72 38" style="display:block;flex-shrink:0;">
        <rect width="72" height="38" fill="#003087" rx="2"/>
        <text x="36" y="28" text-anchor="middle" fill="#fff" font-family="Arial Black,Arial,sans-serif" font-weight="900" font-size="24" letter-spacing="-1">NHS</text>
    </svg>
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
        <span class="lbl">Date of birth</span>{!! $dobBoxes($patient->date_of_birth) !!}
        <span class="lbl" style="margin-left:14px;">NHS No</span>
        <input type="text" class="inp" style="width:90px;">
        <span class="lbl" style="margin-left:10px;">N.I. No</span>
        <input type="text" class="inp" style="width:90px;">
    </div>
    <div class="fl">
        <span class="lbl">Date of last sight test</span>
        <input type="text" class="inp" style="width:80px;" placeholder="DD/MM/YYYY">
        <label class="chk" style="margin-left:10px;"><input type="checkbox">&nbsp;First test</label>
        <label class="chk" style="margin-left:6px;"><input type="checkbox">&nbsp;Not known</label>
    </div>

    {{-- Eligibility --}}
    <div class="sub-hdr">Tick the box(es) that apply to you</div>
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
    <div class="sub-hdr" style="margin-top:6px;">I receive one of the following (tick if applicable)</div>
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
        DOB <input type="text" class="inp" style="width:75px;" placeholder="DD/MM/YYYY">
    </div>

    {{-- HC2 / Complex --}}
    <div class="fl" style="margin-top:5px;">
        <div class="chk">
            <input type="checkbox" {{ $hasHC2 ? 'checked' : '' }}>
            <span>I hold a valid HC2 certificate &nbsp; No.&nbsp;<input type="text" class="inp" style="width:90px;"></span>
        </div>
        <div class="chk" style="margin-left:20px;">
            <input type="checkbox"><span>I need complex lenses</span>
        </div>
    </div>

    {{-- Optician use only --}}
    <div class="opt-box">
        <strong>For optician / contractor use only</strong> &nbsp;&mdash;&nbsp;
        Evidence of eligibility:
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
        I am applying for a free NHS sight test and am entitled to receive one because of the reason(s) ticked in Part 1.
        I understand that I am only entitled to one free NHS sight test within the relevant period.
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
            <div class="sig"></div>
            <p style="font-size:8.5pt; margin-top:4px;">Date&nbsp;<input type="text" class="inp" style="width:90px;" placeholder="DD/MM/YYYY"></p>
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
                'G' => 'Mixed — Any other Mixed',   'H' => 'Asian or Asian British — Indian',
                'J' => 'Asian or Asian British — Pakistani', 'K' => 'Asian — Bangladeshi',
                'L' => 'Asian — Any other Asian',   'M' => 'Black — Caribbean',
                'N' => 'Black — African',            'P' => 'Black — Any other Black',
                'R' => 'Chinese',                    'S' => 'Any other ethnic group',
                'Z' => 'Not stated',
            ] as $code => $label)
                <label class="chk"><input type="checkbox"><span><strong>{{ $code }}</strong>&nbsp;{{ $label }}</span></label>
            @endforeach
        </div>
    </div>

    {{-- PART 3 --}}
    <div class="part-hdr">PART 3 — PERFORMER'S DECLARATION</div>
    <div class="section">
        <div class="fl">
            <span class="lbl">Date of sight test</span>
            <input type="text" class="inp" style="width:90px;" placeholder="DD/MM/YYYY">
            <span class="lbl" style="margin-left:16px;">Re-test code</span>
            <input type="text" class="inp" style="width:55px;">
        </div>

        <div style="margin:6px 0;">
            <p class="sub-hdr">Following the sight test (tick all that apply):</p>
            <div class="g2">
                <div>
                    <div class="chk"><input type="checkbox"><span>Patient referred to a doctor or hospital</span></div>
                    <div class="chk"><input type="checkbox"><span>New or changed prescription issued</span></div>
                    <div class="chk"><input type="checkbox"><span>Statement in lieu of prescription issued</span></div>
                </div>
                <div>
                    <div class="chk"><input type="checkbox"><span>Unchanged prescription</span></div>
                    <div class="chk"><input type="checkbox"><span>Optical voucher issued</span></div>
                </div>
            </div>
        </div>

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

        <div class="chk" style="margin-bottom:8px;">
            <input type="checkbox"><span>Contractor only — unable to provide details of performer</span>
        </div>

        <div class="fl">
            <span class="lbl">Performer's name</span>
            <input type="text" class="inp" style="width:170px;">
            <span class="lbl" style="margin-left:10px;">Performers list number</span>
            <input type="text" class="inp" style="width:75px;">
            <span class="lbl" style="margin-left:10px;">Date</span>
            <input type="text" class="inp" style="width:85px;" placeholder="DD/MM/YYYY">
        </div>
        <div class="fl" style="margin-top:3px;">
            <span class="lbl">Performer's signature</span>
            <div class="sig" style="width:180px;"></div>
        </div>

        <p style="font-size:8pt; margin:7px 0; line-height:1.4;">
            I declare that I am included in a Performers List and am contracted to undertake NHS primary ophthalmic services.
            I have personally carried out the sight test. I certify that the patient is eligible for an NHS sight test for the reason(s) shown.
            I agree to maintain records of this sight test and make them available to NHS England on request.
        </p>

        <div style="border-top:1pt solid #bbb; padding-top:7px; margin-top:7px;">
            <p style="font-weight:bold; font-size:9pt; margin-bottom:5px;">Contractor</p>
            <div class="g2">
                <div>
                    <p style="font-size:8pt; font-weight:bold; margin-bottom:2px;">Signature</p>
                    <div class="sig"></div>
                    <p style="font-size:8.5pt; margin-top:4px;">Date&nbsp;<input type="text" class="inp" style="width:85px;" placeholder="DD/MM/YYYY"></p>
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
