<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Examination Report — {{ $examination->patient->first_name }} {{ $examination->patient->surname }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1f2937; line-height: 1.4; }

        /* Page layout */
        .page { padding: 24px 28px; }

        /* Header / logo bar */
        .header { border-bottom: 2px solid #4f46e5; padding-bottom: 10px; margin-bottom: 14px; }
        .header-title { font-size: 16px; font-weight: bold; color: #4f46e5; }
        .header-sub { font-size: 9px; color: #6b7280; margin-top: 2px; }
        .header-meta { font-size: 9px; color: #6b7280; text-align: right; }

        /* Section blocks */
        .section { margin-bottom: 12px; }
        .section-title {
            font-size: 9px; font-weight: bold; text-transform: uppercase;
            letter-spacing: 0.05em; color: #4f46e5;
            border-bottom: 1px solid #e5e7eb; padding-bottom: 3px; margin-bottom: 6px;
        }

        /* Two-column grid */
        .grid-2 { width: 100%; border-collapse: collapse; }
        .grid-2 td { width: 50%; vertical-align: top; padding: 0 8px 0 0; }
        .grid-2 td:last-child { padding-right: 0; }

        /* Data rows */
        .dl { width: 100%; border-collapse: collapse; }
        .dl tr td { padding: 1.5px 0; vertical-align: top; }
        .dl .label { color: #6b7280; width: 38%; white-space: nowrap; }
        .dl .value { color: #111827; font-weight: 500; }

        /* Rx table */
        .rx-table { width: 100%; border-collapse: collapse; font-size: 9px; }
        .rx-table th { background: #f3f4f6; text-align: center; padding: 3px 4px; border: 1px solid #e5e7eb; font-weight: bold; color: #374151; }
        .rx-table td { text-align: center; padding: 2px 4px; border: 1px solid #e5e7eb; color: #111827; }
        .rx-table .eye-label { text-align: left; font-weight: bold; background: #f9fafb; }

        /* Ophthalmoscopy table */
        .oph-table { width: 100%; border-collapse: collapse; font-size: 9px; }
        .oph-table th { background: #f3f4f6; text-align: center; padding: 3px 4px; border: 1px solid #e5e7eb; font-weight: bold; color: #374151; width: 50%; }
        .oph-table td { padding: 2px 4px; border: 1px solid #e5e7eb; vertical-align: top; }
        .oph-table .field-label { color: #6b7280; font-size: 8.5px; }

        /* Recommendation chips */
        .recs { margin-top: 4px; }
        .rec-chip { display: inline-block; background: #ede9fe; color: #5b21b6; border-radius: 3px; padding: 1px 6px; margin: 2px 2px 0 0; font-size: 8.5px; }

        /* Tags */
        .badge { display: inline-block; border-radius: 3px; padding: 1px 5px; font-size: 8.5px; font-weight: bold; }
        .badge-green { background: #d1fae5; color: #065f46; }
        .badge-yellow { background: #fef3c7; color: #92400e; }
        .badge-orange { background: #ffedd5; color: #9a3412; }
        .badge-blue { background: #dbeafe; color: #1e40af; }

        /* Sign-off box */
        .signoff { border: 1px solid #e5e7eb; border-radius: 4px; padding: 8px 10px; background: #f9fafb; margin-top: 10px; }
        .signoff-title { font-weight: bold; font-size: 9px; color: #374151; margin-bottom: 4px; }

        /* Footer */
        .footer { border-top: 1px solid #e5e7eb; padding-top: 6px; margin-top: 14px; font-size: 8px; color: #9ca3af; text-align: center; }

        /* Utility */
        .text-muted { color: #6b7280; }
        .font-bold { font-weight: bold; }
        .mt-4 { margin-top: 4px; }
        .mt-8 { margin-top: 8px; }
        .nowrap { white-space: nowrap; }
        .dash { color: #d1d5db; }
    </style>
</head>
<body>
<div class="page">

    {{-- ── Header ─────────────────────────────────────────────────────── --}}
    <table style="width:100%; border-collapse:collapse;" class="header">
        <tr>
            <td>
                <div class="header-title">HomeOptic — Examination Report</div>
                <div class="header-sub">Psk Locum Cover Ltd · Domiciliary Optometry</div>
            </td>
            <td style="text-align:right; vertical-align:top;">
                <div class="header-meta">Report generated: {{ now()->format('d/m/Y H:i') }}</div>
                <div class="header-meta">Examination #{{ $examination->id }}</div>
            </td>
        </tr>
    </table>

    {{-- ── Patient details ───────────────────────────────────────────── --}}
    @php $patient = $examination->patient; @endphp
    <div class="section">
        <div class="section-title">Patient Details</div>
        <table class="grid-2">
            <tr>
                <td>
                    <table class="dl">
                        <tr>
                            <td class="label">Name</td>
                            <td class="value font-bold">{{ $patient->title }} {{ $patient->first_name }} {{ $patient->surname }}</td>
                        </tr>
                        <tr>
                            <td class="label">Date of Birth</td>
                            <td class="value">{{ $patient->date_of_birth->format('d/m/Y') }} (age {{ $patient->date_of_birth->age }})</td>
                        </tr>
                        <tr>
                            <td class="label">Sex / Gender</td>
                            <td class="value">{{ $patient->sex_gender?->label() ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Patient ID</td>
                            <td class="value">#{{ $patient->id }}</td>
                        </tr>
                        <tr>
                            <td class="label">Patient Type</td>
                            <td class="value">{{ $patient->patient_type?->label() ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="label">NHS Patient</td>
                            <td class="value">
                                @if($patient->is_nhs)
                                    <span class="badge badge-green">Yes</span>
                                @else
                                    No
                                @endif
                            </td>
                        </tr>
                        @if($patient->has_glaucoma)
                        <tr>
                            <td class="label">Flags</td>
                            <td class="value">
                                @if($patient->has_glaucoma)<span class="badge badge-yellow">Glaucoma</span> @endif
                                @if($patient->is_diabetic)<span class="badge badge-orange">Diabetic</span> @endif
                            </td>
                        </tr>
                        @endif
                    </table>
                </td>
                <td>
                    <table class="dl">
                        <tr>
                            <td class="label">Address</td>
                            <td class="value">
                                {{ $patient->address_line_1 }}<br>
                                {{ $patient->town_city }}@if($patient->county), {{ $patient->county }}@endif<br>
                                {{ $patient->post_code }}
                                @if($patient->country)<br>{{ $patient->country }}@endif
                            </td>
                        </tr>
                        @if($patient->telephone_mobile)
                        <tr>
                            <td class="label">Mobile</td>
                            <td class="value">{{ $patient->telephone_mobile }}</td>
                        </tr>
                        @endif
                        @if($patient->telephone_other)
                        <tr>
                            <td class="label">Other Tel.</td>
                            <td class="value">{{ $patient->telephone_other }}</td>
                        </tr>
                        @endif
                        @if($patient->practice)
                        <tr>
                            <td class="label">Practice</td>
                            <td class="value">{{ $patient->practice->name }}</td>
                        </tr>
                        @endif
                        @if($patient->doctor || $patient->doctor_other)
                        <tr>
                            <td class="label">GP</td>
                            <td class="value">{{ $patient->doctor?->name ?? $patient->doctor_other }}</td>
                        </tr>
                        @endif
                    </table>
                </td>
            </tr>
        </table>
    </div>

    {{-- ── Examination details ────────────────────────────────────────── --}}
    <div class="section">
        <div class="section-title">Examination Details</div>
        <table class="dl">
            <tr>
                <td class="label">Examination Date</td>
                <td class="value">{{ $examination->examined_at->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td class="label">Examination Type</td>
                <td class="value">{{ $examination->exam_type->label() }}</td>
            </tr>
            <tr>
                <td class="label">Examining Optometrist</td>
                <td class="value">{{ $examination->staff?->name ?? '—' }}</td>
            </tr>
        </table>
    </div>

    {{-- ── Tab 1: History & Symptoms ──────────────────────────────────── --}}
    @php $h = $examination->historySymptoms; @endphp
    @if($h)
    <div class="section">
        <div class="section-title">History &amp; Symptoms</div>
        <table class="dl">
            @if($h->gos_eligibility)
            <tr>
                <td class="label">GOS Eligibility</td>
                <td class="value">{{ $h->gos_eligibility->label() }}</td>
            </tr>
            @endif
            @if($h->gos_establishment_name || $h->gos_establishment_town)
            <tr>
                <td class="label">GOS Establishment</td>
                <td class="value">{{ implode(', ', array_filter([$h->gos_establishment_name, $h->gos_establishment_town])) }}</td>
            </tr>
            @endif
            <tr>
                <td class="label">Last Examination</td>
                <td class="value">
                    @if($h->last_exam_first) First examination
                    @elseif($h->last_exam_not_known) Not known
                    @elseif($h->last_exam_date) {{ $h->last_exam_date->format('d/m/Y') }}
                    @else <span class="dash">—</span>
                    @endif
                </td>
            </tr>
            @if($h->reason_for_visit)
            <tr>
                <td class="label">Reason for Visit</td>
                <td class="value">{{ $h->reason_for_visit }}</td>
            </tr>
            @endif
            @if($h->poh)
            <tr>
                <td class="label">Personal Ocular History</td>
                <td class="value">{{ $h->poh }}</td>
            </tr>
            @endif
            @if($h->gh)
            <tr>
                <td class="label">General Health</td>
                <td class="value">{{ $h->gh }}</td>
            </tr>
            @endif
            @if($h->medications && count($h->medications))
            <tr>
                <td class="label">Medications</td>
                <td class="value">{{ implode(', ', $h->medications) }}</td>
            </tr>
            @endif
            @if($h->medication_notes)
            <tr>
                <td class="label">Medication Notes</td>
                <td class="value">{{ $h->medication_notes }}</td>
            </tr>
            @endif
            @if($h->fh)
            <tr>
                <td class="label">Family History</td>
                <td class="value">{{ $h->fh }}</td>
            </tr>
            @endif
            @if($h->foh)
            <tr>
                <td class="label">Family Ocular History</td>
                <td class="value">{{ $h->foh }}</td>
            </tr>
            @endif
            @if($h->has_glaucoma || $h->has_fhg || $h->is_diabetic)
            <tr>
                <td class="label">Clinical Flags</td>
                <td class="value">
                    @if($h->has_glaucoma)<span class="badge badge-yellow">Glaucoma</span> @endif
                    @if($h->has_fhg)<span class="badge badge-yellow">FHG</span> @endif
                    @if($h->is_diabetic)<span class="badge badge-orange">Diabetic</span> @endif
                </td>
            </tr>
            @endif
            @if($h->other_notes)
            <tr>
                <td class="label">Other Notes</td>
                <td class="value">{{ $h->other_notes }}</td>
            </tr>
            @endif
        </table>
    </div>
    @endif

    {{-- ── Tab 2: Ophthalmoscopy ──────────────────────────────────────── --}}
    @php $o = $examination->ophthalmoscopy; @endphp
    @if($o)
    @php
        $ophFields = [
            'pupils'           => 'Pupils',
            'lids_lashes'      => 'Lids / Lashes',
            'conjunc'          => 'Conjunctiva',
            'cornea'           => 'Cornea',
            'sclera'           => 'Sclera',
            'ant_ch'           => 'Anterior Chamber',
            'media'            => 'Media',
            'cd'               => 'Cup / Disc',
            'av'               => 'A/V Vessels',
            'fundus_periphery' => 'Fundus Periphery',
            'macular'          => 'Macular',
            'ret_grading'      => 'Retinal Grading',
        ];
        $hasOphData = false;
        foreach ($ophFields as $key => $_) {
            if ($o->{"right_$key"} || $o->{"left_$key"}) { $hasOphData = true; break; }
        }
    @endphp
    @if($hasOphData || $o->ophthalmoscopy_notes)
    <div class="section">
        <div class="section-title">Ophthalmoscopy &amp; External Examination</div>
        @if($hasOphData)
        <table class="oph-table">
            <thead>
                <tr>
                    <th style="width:30%; text-align:left; padding-left:4px;">Finding</th>
                    <th style="width:35%;">Right Eye (R)</th>
                    <th style="width:35%;">Left Eye (L)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ophFields as $key => $label)
                @if($o->{"right_$key"} || $o->{"left_$key"})
                <tr>
                    <td class="field-label">{{ $label }}</td>
                    <td>{{ $o->{"right_$key"} ?? '—' }}</td>
                    <td>{{ $o->{"left_$key"} ?? '—' }}</td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
        @endif
        @if($o->ophthalmoscopy_notes)
        <div class="mt-4 text-muted">Notes: <span style="color:#111827;">{{ $o->ophthalmoscopy_notes }}</span></div>
        @endif
    </div>
    @endif
    @endif

    {{-- ── Tab 3: Investigative Techniques ───────────────────────────── --}}
    @php $inv = $examination->investigative; @endphp
    @if($inv)
    @php
        $invRows = [
            ['IOP (pre)', $inv->pre_iop_r, $inv->pre_iop_l],
            ['IOP (post)', $inv->post_iop_r, $inv->post_iop_l],
            ['Visual Fields', $inv->visual_fields_r, $inv->visual_fields_l],
            ['Amsler', $inv->amsler_r, $inv->amsler_l],
            ['Keratometry', $inv->keratometry_r, $inv->keratometry_l],
        ];
        $invSingle = array_filter([
            'Cover Test (with Rx)' => $inv->ct_with_rx,
            'Cover Test (without Rx)' => $inv->ct_without_rx,
            'Motility' => $inv->motility,
            'NPC' => $inv->npc,
            'Stereopsis' => $inv->stereopsis,
            'Colour Vision' => $inv->colour_vision,
            'Amplitude of Accommodation' => $inv->amplitude_of_accommodation,
            'OMB (H)' => $inv->omb_h,
            'OMB (V)' => $inv->omb_v,
        ]);
        $hasInvData = $invSingle || array_filter($invRows, fn($r) => $r[1] || $r[2]);
        $hasDrops = $inv->drops_used;
    @endphp
    @if($hasInvData || $hasDrops)
    <div class="section">
        <div class="section-title">Investigative Techniques</div>
        @if($hasDrops)
        <div class="mt-4">
            <span class="badge badge-blue">Drops Used</span>
            @if($inv->drops_detail_batch) Batch: {{ $inv->drops_detail_batch }} @endif
            @if($inv->drops_expiry) · Expiry: {{ $inv->drops_expiry->format('m/Y') }} @endif
        </div>
        @endif
        @if(array_filter($invRows, fn($r) => $r[1] || $r[2]))
        <table class="oph-table mt-4">
            <thead>
                <tr>
                    <th style="width:30%; text-align:left; padding-left:4px;">Test</th>
                    <th>Right (R)</th>
                    <th>Left (L)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invRows as [$label, $r, $l])
                @if($r || $l)
                <tr>
                    <td class="field-label">{{ $label }}</td>
                    <td>{{ $r ?? '—' }}</td>
                    <td>{{ $l ?? '—' }}</td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
        @endif
        @if($invSingle)
        <table class="dl mt-4">
            @foreach($invSingle as $label => $val)
            <tr>
                <td class="label">{{ $label }}</td>
                <td class="value">{{ $val }}</td>
            </tr>
            @endforeach
        </table>
        @endif
    </div>
    @endif
    @endif

    {{-- ── Tab 4: Refraction ──────────────────────────────────────────── --}}
    @php $rx = $examination->refraction; @endphp
    @if($rx)
    @php
        $rxSections = [
            'Current Rx' => [
                'r' => ['sph'=>$rx->current_r_sph,'cyl'=>$rx->current_r_cyl,'axis'=>$rx->current_r_axis,'add'=>$rx->current_r_add,'va'=>$rx->current_r_va,'prism'=>$rx->current_r_prism],
                'l' => ['sph'=>$rx->current_l_sph,'cyl'=>$rx->current_l_cyl,'axis'=>$rx->current_l_axis,'add'=>$rx->current_l_add,'va'=>$rx->current_l_va,'prism'=>$rx->current_l_prism],
            ],
            'Subjective Rx' => [
                'r' => ['sph'=>$rx->subj_r_sph,'cyl'=>$rx->subj_r_cyl,'axis'=>$rx->subj_r_axis,'add'=>$rx->subj_r_near_add,'va'=>$rx->subj_r_va,'prism'=>$rx->subj_r_prism],
                'l' => ['sph'=>$rx->subj_l_sph,'cyl'=>$rx->subj_l_cyl,'axis'=>$rx->subj_l_axis,'add'=>$rx->subj_l_near_add,'va'=>$rx->subj_l_va,'prism'=>$rx->subj_l_prism],
            ],
        ];
        $hasSomeRx = false;
        foreach ($rxSections as $s) {
            foreach ($s as $eye) {
                foreach ($eye as $v) { if (!is_null($v)) { $hasSomeRx = true; break 3; } }
            }
        }
    @endphp
    @if($hasSomeRx)
    <div class="section">
        <div class="section-title">Refraction</div>
        @foreach($rxSections as $title => $section)
        @php
            $hasSection = false;
            foreach ($section as $eye) { foreach ($eye as $v) { if (!is_null($v)) { $hasSection = true; break 2; } } }
        @endphp
        @if($hasSection)
        <div style="font-size:9px; font-weight:bold; color:#374151; margin:5px 0 3px;">{{ $title }}</div>
        <table class="rx-table">
            <thead>
                <tr>
                    <th style="text-align:left; width:8%;">Eye</th>
                    <th>Sph</th><th>Cyl</th><th>Axis</th><th>Prism</th><th>Add</th><th>VA</th>
                </tr>
            </thead>
            <tbody>
                @foreach(['r' => 'Right', 'l' => 'Left'] as $side => $label)
                @php $e = $section[$side]; @endphp
                <tr>
                    <td class="eye-label">{{ $label }}</td>
                    <td>{{ is_null($e['sph']) ? '—' : sprintf('%+.2f', $e['sph']) }}</td>
                    <td>{{ is_null($e['cyl']) ? '—' : sprintf('%+.2f', $e['cyl']) }}</td>
                    <td>{{ $e['axis'] ?? '—' }}{{ $e['axis'] ? '°' : '' }}</td>
                    <td>{{ $e['prism'] ?? '—' }}</td>
                    <td>{{ is_null($e['add']) ? '—' : sprintf('%+.2f', $e['add']) }}</td>
                    <td>{{ $e['va'] ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
        @endforeach
        @if($rx->subj_pd_r || $rx->subj_pd_l || $rx->subj_bin_bcva)
        <table class="dl mt-4">
            @if($rx->subj_pd_r || $rx->subj_pd_l)
            <tr>
                <td class="label">PD (R / L)</td>
                <td class="value">{{ $rx->subj_pd_r ?? '—' }} / {{ $rx->subj_pd_l ?? '—' }}</td>
            </tr>
            @endif
            @if($rx->subj_bin_bcva)
            <tr>
                <td class="label">Bin. BCVA</td>
                <td class="value">{{ $rx->subj_bin_bcva }}</td>
            </tr>
            @endif
        </table>
        @endif
    </div>
    @endif

    {{-- Outcome & recommendations --}}
    @if($rx->outcome || $rx->rec_distance || $rx->rec_near || $rx->rec_intermediate || $rx->rec_high_index || $rx->rec_bifocals || $rx->rec_varifocals || $rx->rec_occupational || $rx->rec_min_sub || $rx->rec_photochromic || $rx->rec_hardcoat || $rx->rec_tint || $rx->rec_mar)
    <div class="section">
        <div class="section-title">Outcome &amp; Recommendations</div>
        @if($rx->outcome)
        <table class="dl">
            <tr>
                <td class="label">Outcome</td>
                <td class="value font-bold">{{ $rx->outcome->label() }}</td>
            </tr>
        </table>
        @endif
        @php
            $recs = array_filter([
                'Distance' => $rx->rec_distance, 'Near' => $rx->rec_near, 'Intermediate' => $rx->rec_intermediate,
                'High Index' => $rx->rec_high_index, 'Bifocals' => $rx->rec_bifocals, 'Varifocals' => $rx->rec_varifocals,
                'Occupational' => $rx->rec_occupational, 'Min Sub' => $rx->rec_min_sub, 'Photochromic' => $rx->rec_photochromic,
                'Hardcoat' => $rx->rec_hardcoat, 'Tint' => $rx->rec_tint, 'MAR' => $rx->rec_mar,
            ]);
        @endphp
        @if($recs)
        <div class="recs mt-4">
            <span class="text-muted">Recommendations: </span>
            @foreach($recs as $label => $_)
                <span class="rec-chip">{{ $label }}</span>
            @endforeach
        </div>
        @endif
        @if($rx->nhs_voucher_dist || $rx->nhs_voucher_near)
        <table class="dl mt-4">
            @if($rx->nhs_voucher_dist)
            <tr><td class="label">NHS Voucher (dist)</td><td class="value">{{ $rx->nhs_voucher_dist }}</td></tr>
            @endif
            @if($rx->nhs_voucher_near)
            <tr><td class="label">NHS Voucher (near)</td><td class="value">{{ $rx->nhs_voucher_near }}</td></tr>
            @endif
        </table>
        @endif
        @if($rx->retest_after)
        <table class="dl mt-4">
            <tr>
                <td class="label">Retest After</td>
                <td class="value">{{ $rx->retest_after }}
                    @if($rx->retest_patient_type) ({{ $rx->retest_patient_type->label() }}) @endif
                </td>
            </tr>
        </table>
        @endif
        @if($rx->examination_comment)
        <table class="dl mt-4">
            <tr>
                <td class="label">Comments</td>
                <td class="value">{{ $rx->examination_comment }}</td>
            </tr>
        </table>
        @endif
    </div>
    @endif
    @endif

    {{-- ── Sign-off ────────────────────────────────────────────────────── --}}
    @if($examination->signed_at)
    <div class="signoff">
        <div class="signoff-title">Examination Sign-Off</div>
        <table class="dl">
            <tr>
                <td class="label">Signed by</td>
                <td class="value">{{ $examination->signedBy?->name ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">Signed at</td>
                <td class="value">{{ $examination->signed_at->format('d/m/Y H:i') }}</td>
            </tr>
        </table>
    </div>
    @endif

    {{-- ── Footer ──────────────────────────────────────────────────────── --}}
    <div class="footer">
        HomeOptic · Psk Locum Cover Ltd · Examination #{{ $examination->id }} · {{ $examination->patient->first_name }} {{ $examination->patient->surname }} · Generated {{ now()->format('d/m/Y H:i') }}
    </div>

</div>
</body>
</html>
