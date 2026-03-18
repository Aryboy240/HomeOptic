# HomeOptic — Final Agreed Database Schema

> Agreed in design session before any migrations were written.
> All decisions recorded here supersede the earlier discussion in chat.

---

## Conventions

- Tables: `snake_case`, plural
- Primary keys: auto-increment `bigint id`
- Foreign keys: `{singular}_id`
- All tables carry `created_at` and `updated_at` (Laravel timestamps)
- No `deleted_at` — patient lifecycle is managed via a `status` enum column instead
- PHP-backed enums (string) are used in place of FK lookup tables for fields that are static lists the user will never manage through the UI (e.g. dropped reason, domiciliary reason)

---

## PHP Enums

These are defined in code, not as database tables. Stored as `varchar` columns.

```
PatientStatus:       active | deceased | deleted
PatientType:         A (Private) | B (Over 60) | C (NHS) | D (FHG) | E (Child) | F (Glaucoma)
SexGender:           male | female | non_binary | transgender | prefer_not_to_say
DroppedReason:       change_of_mind | hospital_appointment | illness | new_patient_awaiting | not_in_at_time
HowHeard:            facebook | google | in_your_area | instagram | recall_letter | recommended | walking_past | other
DomiciliaryReason:   agoraphobic | alzheimers | amputee | angina | arthritis | bells_palsy | ...  (full list to be finalised)
GosEligibility:      not_eligible | complex_rx | diabetes | fhg_and_40_over | glaucoma | glaucoma_risk | over_60 | ... (full list to be finalised)
ExamType:            spectacle | red_eye | post_op | contact_lens | contact_lens_aftercare | external_rx | external_cl_rx
ExamOutcome:         no_change_specs_needed | no_change_specs_ok | new_rx | no_rx | screening_only | refer_to_gp
AppointmentStatus:   booked | confirmed | completed | cancelled | did_not_attend
AppointmentType:     (to be finalised with client — values not specified in reference doc)
PrismDirection:      in | out | up | down  (used across all Rx direction dropdowns)
```

---

## Pillar 1 — Patient Database

### `practices`

| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `name` | varchar | |
| `address` | text | nullable |

### `doctors`

| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `name` | varchar | |
| `practice_id` | bigint FK → practices | nullable |

### `pcts`

Primary Care Trusts / NHS hospitals. Seeded from the reference list.

| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `name` | varchar | e.g. "Aintree University Hospital" |

### `patients`

| Column | Type | Nullable | Notes |
|---|---|---|---|
| `id` | bigint PK | — | |
| `title` | varchar | no | Mr, Mrs, Ms, Miss, Dr, Prof, Rev, etc. |
| `first_name` | varchar | no | |
| `surname` | varchar | no | |
| `address_line_1` | varchar | no | |
| `post_code` | varchar | no | |
| `town_city` | varchar | no | |
| `county` | varchar | yes | |
| `country` | varchar | yes | |
| `telephone_mobile` | varchar | yes | |
| `telephone_other` | varchar | yes | |
| `alt_contact_name` | varchar | yes | Name and/or relationship |
| `alt_tel_number` | varchar | yes | |
| `email` | varchar | yes | |
| `sex_gender` | varchar | no | SexGender enum |
| `date_of_birth` | date | no | |
| `status` | varchar | no | PatientStatus enum — default `active` |
| `practice_id` | bigint FK → practices | yes | |
| `doctor_id` | bigint FK → doctors | yes | |
| `doctor_other` | varchar | yes | Free text when doctor is "Other" |
| `has_glaucoma` | boolean | no | default false |
| `is_diabetic` | boolean | no | default false |
| `is_nhs` | boolean | no | default false |
| `patient_type` | char(1) | no | PatientType enum |
| `dropped_reason` | varchar | yes | DroppedReason enum |
| `how_heard` | varchar | yes | HowHeard enum |
| `how_heard_other` | varchar | yes | Free text when how_heard = other |
| `pct_id` | bigint FK → pcts | yes | |
| `domiciliary_reason` | varchar | yes | DomiciliaryReason enum |
| `notes` | text | yes | |

**Indexes:** `surname`, `post_code`, `date_of_birth`, `patient_type`, `status`
(driven by the search/sort options in the Find Patient screen)

---

## Pillar 2 — Examination Records

One parent row ties all tab data together. Only the Spectacle Exam is required for the prototype; the structure supports the other types when needed.

### `examinations`

| Column | Type | Nullable | Notes |
|---|---|---|---|
| `id` | bigint PK | — | |
| `patient_id` | bigint FK → patients | no | |
| `exam_type` | varchar | no | ExamType enum |
| `examined_at` | date | no | |
| `staff_id` | bigint FK → users | no | Examining optometrist |
| `signed_by` | bigint FK → users | yes | |
| `signed_at` | timestamp | yes | |
| `notes` | text | yes | Shown in the patient examination history table |

**Indexes:** `patient_id`, `examined_at`

---

### `exam_history_symptoms` (Tab 1)

1:1 with `examinations`.

| Column | Type | Nullable | Notes |
|---|---|---|---|
| `id` | bigint PK | — | |
| `examination_id` | bigint FK → examinations | no | unique |
| `gos_eligibility` | varchar | no | GosEligibility enum |
| `gos_establishment_name` | varchar | yes | |
| `gos_establishment_town` | varchar | yes | |
| `last_exam_first` | boolean | no | "First Examination" checkbox — default false |
| `last_exam_not_known` | boolean | no | default false |
| `last_exam_date` | date | yes | Null when first or not known |
| `reason_for_visit` | text | yes | |
| `poh` | text | yes | Past Ocular History |
| `gh` | text | yes | General Health |
| `medication_notes` | text | yes | Free-text medication field |
| `fh` | text | yes | Family History |
| `foh` | text | yes | Family Ocular History |
| `medications` | json | yes | Array of medication name strings. **Prototype only — normalise to a pivot table before production.** |
| `other_notes` | text | yes | |
| `has_glaucoma` | boolean | no | Snapshot at time of exam — default false |
| `has_fhg` | boolean | no | Family History of Glaucoma — default false |
| `is_diabetic` | boolean | no | Snapshot at time of exam — default false |

**Medication list (seeded as the valid values for the JSON array):**
ADCAL, ALENDRONATE, ALLOPURINOL, AMITRIPTYLINE, AMLODIPINE, ASPIRIN, ATENOLOL, ATIVAN,
ATROVENT, BENDROFLUAZIDE, BENDROFLUMETHIAZIDE, BETOPTIC, BISOPROLOL FUMARATE, BRUFEN,
CANDESARTAN, CARBAMAZEPINE, CLOBAZAM, CO-CODAMOL, CO-DYDRAMOL, COMBIVENT, DIAZEPAM,
DIGOXIN, DOXAZOSIN, EPANUTUN, EPILIM, FUROSEMIDE, GABAPENTIN, GLICLAZIDE,
HYDROCHLOROQUINE, INSULIN, IRBESARTAN, LANSOPRAZOLE, LISINOPRIL, LOSARTAN,
METFORMIN HYDROCHLORIDE, OMEPRAZOLE, PARACETAMOL, PRIMIDONE, PROCYCLIDINE, RAMIPRIL,
RANITIDINE, RISPERIDONE, SALBUTAMOL, STATIN, TAMSULOSIN, TEGRETOL, THYROXINE,
TIMOLOL, TIMOPTOL, TRAMADOL, VENTOLIN, WARFARIN, XAL-ATAN

---

### `exam_ophthalmoscopy` (Tab 2)

1:1 with `examinations`. All per-eye fields stored as flat prefixed columns (fixed field set).

| Column | Type | Nullable | Notes |
|---|---|---|---|
| `id` | bigint PK | — | |
| `examination_id` | bigint FK → examinations | no | unique |
| `ophthalmoscopy_notes` | text | yes | |
| `right_pupils` | varchar | yes | |
| `right_lids_lashes` | varchar | yes | |
| `right_lashes` | varchar | yes | |
| `right_conjunc` | varchar | yes | |
| `right_cornea` | varchar | yes | |
| `right_sclera` | varchar | yes | |
| `right_ant_ch` | varchar | yes | Anterior Chamber |
| `right_media` | varchar | yes | |
| `right_cd` | varchar | yes | Cup-to-Disc ratio |
| `right_av` | varchar | yes | Arteriovenous ratio |
| `right_fundus_periphery` | varchar | yes | |
| `right_macular` | varchar | yes | |
| `right_ret_grading` | varchar | yes | Retinal Grading |
| `left_pupils` | varchar | yes | |
| `left_lids_lashes` | varchar | yes | |
| `left_lashes` | varchar | yes | |
| `left_conjunc` | varchar | yes | |
| `left_cornea` | varchar | yes | |
| `left_sclera` | varchar | yes | |
| `left_ant_ch` | varchar | yes | |
| `left_media` | varchar | yes | |
| `left_cd` | varchar | yes | |
| `left_av` | varchar | yes | |
| `left_fundus_periphery` | varchar | yes | |
| `left_macular` | varchar | yes | |
| `left_ret_grading` | varchar | yes | |

---

### `exam_investigative` (Tab 3)

1:1 with `examinations`.

| Column | Type | Nullable | Notes |
|---|---|---|---|
| `id` | bigint PK | — | |
| `examination_id` | bigint FK → examinations | no | unique |
| `drops_used` | boolean | no | Tropicamide 1% — default false |
| `drops_detail_batch` | varchar | yes | |
| `drops_expiry` | date | yes | |
| `drops_more_info` | text | yes | |
| `pre_iop_r` | varchar | yes | Pre-dilation IOP, right |
| `pre_iop_l` | varchar | yes | Pre-dilation IOP, left |
| `post_iop_r` | varchar | yes | |
| `post_iop_l` | varchar | yes | |
| `ct_with_rx` | varchar | yes | Cover Test with Rx |
| `ct_with_rx_near` | varchar | yes | |
| `ct_with_rx_near_notes` | text | yes | |
| `ct_without_rx` | varchar | yes | |
| `ct_without_rx_near` | varchar | yes | |
| `ct_without_rx_near_notes` | text | yes | |
| `omb_near_h` | varchar | yes | Ocular Motility Balance — near, horizontal |
| `omb_near_v` | varchar | yes | Ocular Motility Balance — near, vertical |
| `visual_fields_r` | varchar | yes | |
| `visual_fields_l` | varchar | yes | |
| `motility` | varchar | yes | e.g. "Full & Smooth" |
| `amsler_r` | varchar | yes | No distortion or scotomas / Distortion / Scotoma |
| `amsler_r_notes` | text | yes | |
| `amsler_l` | varchar | yes | |
| `amsler_l_notes` | text | yes | |
| `omb_h` | varchar | yes | |
| `omb_v` | varchar | yes | |
| `keratometry_r` | varchar | yes | |
| `keratometry_l` | varchar | yes | |
| `npc` | varchar | yes | Near Point of Convergence |
| `stereopsis` | varchar | yes | |
| `colour_vision` | varchar | yes | |
| `amplitude_of_accommodation` | varchar | yes | |

---

### `exam_refraction` (Tab 4)

1:1 with `examinations`. All Rx sections stored as flat columns — the form renders all sections together and saves them in one operation, making flat columns the cleanest approach (see design notes).

| Column | Type | Nullable | Notes |
|---|---|---|---|
| `id` | bigint PK | — | |
| `examination_id` | bigint FK → examinations | no | unique |
| **Current / Previous Rx 1 — Right Eye** | | | |
| `current_r_sph` | decimal(5,2) | yes | |
| `current_r_cyl` | decimal(5,2) | yes | |
| `current_r_axis` | smallint | yes | 0–180 |
| `current_r_prism` | decimal(4,2) | yes | |
| `current_r_prism_dir` | varchar | yes | PrismDirection enum |
| `current_r_add` | decimal(4,2) | yes | |
| `current_r_va` | varchar | yes | Visual Acuity |
| **Current / Previous Rx 1 — Left Eye** | | | |
| `current_l_sph` | decimal(5,2) | yes | |
| `current_l_cyl` | decimal(5,2) | yes | |
| `current_l_axis` | smallint | yes | |
| `current_l_prism` | decimal(4,2) | yes | |
| `current_l_prism_dir` | varchar | yes | |
| `current_l_add` | decimal(4,2) | yes | |
| `current_l_va` | varchar | yes | |
| **Current Rx — Additional Fields** | | | |
| `current_pd_r` | decimal(4,1) | yes | Pupillary distance, right |
| `current_pd_l` | decimal(4,1) | yes | |
| `current_bvd` | decimal(4,1) | yes | Back Vertex Distance |
| `current_bin_bcva` | varchar | yes | Binocular Best Corrected VA |
| `current_comments` | text | yes | Date/location of previous test |
| **Previous Rx Other (incl. Autorefractor) — Right Eye** | | | |
| `prev_other_r_sph` | decimal(5,2) | yes | |
| `prev_other_r_cyl` | decimal(5,2) | yes | |
| `prev_other_r_axis` | smallint | yes | |
| `prev_other_r_prism` | decimal(4,2) | yes | |
| `prev_other_r_prism_dir` | varchar | yes | |
| `prev_other_r_add` | decimal(4,2) | yes | |
| `prev_other_r_va` | varchar | yes | |
| **Previous Rx Other — Left Eye** | | | |
| `prev_other_l_sph` | decimal(5,2) | yes | |
| `prev_other_l_cyl` | decimal(5,2) | yes | |
| `prev_other_l_axis` | smallint | yes | |
| `prev_other_l_prism` | decimal(4,2) | yes | |
| `prev_other_l_prism_dir` | varchar | yes | |
| `prev_other_l_add` | decimal(4,2) | yes | |
| `prev_other_l_va` | varchar | yes | |
| **Retinoscopy** | | | |
| `retino_r_value` | text | yes | Double-click sets default |
| `retino_l_value` | text | yes | |
| **Rx Subjective — Right Eye (Distance)** | | | |
| `subj_r_uav` | varchar | yes | Unaided Visual Acuity |
| `subj_r_sph` | decimal(5,2) | yes | |
| `subj_r_cyl` | decimal(5,2) | yes | |
| `subj_r_axis` | smallint | yes | |
| `subj_r_prism` | decimal(4,2) | yes | |
| `subj_r_prism_dir` | varchar | yes | |
| `subj_r_va` | varchar | yes | |
| **Rx Subjective — Right Eye (Near Add)** | | | |
| `subj_r_near_add` | decimal(4,2) | yes | |
| `subj_r_near_prism` | decimal(4,2) | yes | |
| `subj_r_near_prism_dir` | varchar | yes | |
| `subj_r_near_acuity` | varchar | yes | |
| **Rx Subjective — Right Eye (Intermediate Add)** | | | |
| `subj_r_int_add` | decimal(4,2) | yes | |
| `subj_r_int_prism` | decimal(4,2) | yes | |
| `subj_r_int_prism_dir` | varchar | yes | |
| `subj_r_int_acuity` | varchar | yes | |
| **Rx Subjective — Left Eye (Distance)** | | | |
| `subj_l_uav` | varchar | yes | |
| `subj_l_sph` | decimal(5,2) | yes | |
| `subj_l_cyl` | decimal(5,2) | yes | |
| `subj_l_axis` | smallint | yes | |
| `subj_l_prism` | decimal(4,2) | yes | |
| `subj_l_prism_dir` | varchar | yes | |
| `subj_l_va` | varchar | yes | |
| **Rx Subjective — Left Eye (Near Add)** | | | |
| `subj_l_near_add` | decimal(4,2) | yes | |
| `subj_l_near_prism` | decimal(4,2) | yes | |
| `subj_l_near_prism_dir` | varchar | yes | |
| `subj_l_near_acuity` | varchar | yes | |
| **Rx Subjective — Left Eye (Intermediate Add)** | | | |
| `subj_l_int_add` | decimal(4,2) | yes | |
| `subj_l_int_prism` | decimal(4,2) | yes | |
| `subj_l_int_prism_dir` | varchar | yes | |
| `subj_l_int_acuity` | varchar | yes | |
| **Subjective PD & Additional Measurements** | | | |
| `subj_pd_r` | decimal(4,1) | yes | |
| `subj_pd_l` | decimal(4,1) | yes | |
| `subj_pd_combined` | decimal(4,1) | yes | |
| `subj_bvd` | decimal(4,1) | yes | |
| `subj_bin_bcva` | varchar | yes | |
| `subj_notes` | text | yes | |
| **Outcome** | | | |
| `outcome` | varchar | yes | ExamOutcome enum |
| **Recommendations** | | | |
| `rec_distance` | boolean | no | default false |
| `rec_near` | boolean | no | default false |
| `rec_intermediate` | boolean | no | default false |
| `rec_high_index` | boolean | no | default false |
| `rec_bifocals` | boolean | no | default false |
| `rec_varifocals` | boolean | no | default false |
| `rec_occupational` | boolean | no | default false |
| `rec_min_sub` | boolean | no | default false |
| `rec_photochromic` | boolean | no | default false |
| `rec_hardcoat` | boolean | no | default false |
| `rec_tint` | boolean | no | default false |
| `rec_mar` | boolean | no | default false |
| **NHS Voucher** | | | |
| `nhs_voucher_dist` | varchar | yes | |
| `nhs_voucher_near` | varchar | yes | |
| **Examination Comment & Retest** | | | |
| `examination_comment` | text | yes | |
| `retest_after` | varchar | yes | e.g. "1 Year", "2 Years" |
| `retest_patient_type` | char(1) | yes | PatientType enum — updated at exam close |

---

## Pillar 3 — Appointment Diary

### `diaries`

| Column | Type | Nullable | Notes |
|---|---|---|---|
| `id` | bigint PK | — | |
| `name` | varchar | no | The "Diary Name" dropdown in appointments |
| `description` | varchar | yes | |

### `appointments`

| Column | Type | Nullable | Notes |
|---|---|---|---|
| `id` | bigint PK | — | |
| `diary_id` | bigint FK → diaries | no | |
| `patient_id` | bigint FK → patients | no | |
| `appointment_type` | varchar | no | AppointmentType enum |
| `appointment_status` | varchar | no | AppointmentStatus enum |
| `date` | date | no | |
| `start_time` | time | no | |
| `length_minutes` | smallint | no | |
| `display_text` | text | yes | Multi-line text shown in calendar slot |
| `cancelled_at` | timestamp | yes | Null unless cancelled — drives "show cancelled" filter |
| `notified_at` | timestamp | yes | Set when "Update & Notify" is triggered |

**Indexes:** `diary_id` + `date` (composite — primary diary calendar query), `patient_id`

---

## Relationships Summary

```
practices         ──< doctors
practices         ──< patients
doctors           ──< patients
pcts              ──< patients
patients          ──< examinations
patients          ──< appointments
users             ──< examinations  (staff_id)
users             ──< examinations  (signed_by)
examinations      ──1 exam_history_symptoms
examinations      ──1 exam_ophthalmoscopy
examinations      ──1 exam_investigative
examinations      ──1 exam_refraction
diaries           ──< appointments
```

---

## Migration Order

Migrations must be created in dependency order:

1. `practices`
2. `doctors` (depends on practices)
3. `pcts`
4. `users` (Laravel default)
5. `patients` (depends on practices, doctors, pcts)
6. `diaries`
7. `appointments` (depends on diaries, patients)
8. `examinations` (depends on patients, users)
9. `exam_history_symptoms` (depends on examinations)
10. `exam_ophthalmoscopy` (depends on examinations)
11. `exam_investigative` (depends on examinations)
12. `exam_refraction` (depends on examinations)

---

## Design Notes

**Why flat columns on `exam_refraction` rather than normalised `exam_rx_entries` rows:**
The Refraction tab renders all Rx sections together in one form and saves them in a single POST. Normalising into rows would require 8 `updateOrCreate` calls on save, a `groupBy` + `->first()` dance on every load, and no benefit — there are no cross-exam Rx queries in scope. Flat columns make the Blade template and FormRequest validation straightforward.

**Why JSON for medications rather than a pivot table:**
Prototype scope only. "Which patients are on Drug X?" is a legitimate production query that SQL handles poorly against a JSON column. Before going to production, `exam_history_symptoms.medications` should be replaced with a `medications` seed table and an `examination_medications` pivot.

**Why `status` enum rather than `deleted_at` on patients:**
Deceased and deleted are semantically different states. Conflating them under a single soft-delete timestamp would make the "Include Deceased & Deleted" filter misleading and make it impossible to distinguish a patient who died from one who was removed in error.
