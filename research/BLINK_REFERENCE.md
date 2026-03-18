# Patient Data page:


## New Patient:

	Title* - dropdown
	First Name* - text
	Surname* - text
	Post Code* - text (find address button)
	Address (Line 1*) - text
	Town/City* - text
	County - text
	Country - text
	Telephone (Mobile) - number
	Telephone (Other) - number
	Alt Contact (name/relationship) - text
	Alt Tel Number - number
	Email - email
	Sex/Gender* - dropdown (Non-Binary, Prefer Not To Say, Female, Male, Transgender)
	Date Of Birth* - DOB
	Practice - dropdown (use fillers)
	Doctor - dropdown/other
	(If other doctor, then a text field to enter doctor information)
	Patient has Glaucoma - checkbox
	Patient is Diabetic- checkbox
	NHS Patient - checkbox
	Patient Type* - dropdown (A- Private Patient, B- Over 60, C- NHS, D- Family history of glaucoma (FHG), E- Child (under 16), F- Glaucoma)
	Dropped (Reason) - dropdown (Change of Mind, Hospital Appointment, Illness, New Patient Awaiting Eye Test, Not In at time of callChange of Mind, Hospital, Appointment, Illness, New Patient Awaiting Eye Test, Not In at time of call)
	How did you hear about us? - dropdown (Facebook, Google, In Your Area (online local news), Instagram, Other (please specify), Recall Letter, Recommended, Walking Past, Other)
	(If other for "How did you hear about us?" then there is a text box)
	PCT - dropdown (Aintree University Hospital, Airedale, Alder Hey Children's, Ashford and St Peters Hospitals, ....)
	Domiciliary Reason - (Agoraphobic, Alzheimers, Amputee, Angina, Arthritis, Bells Palsy, ...)
	Notes - textbox

	Creates patient ID on database most likley


## Find patient:

	### Search:
		
		Patient ID - number
		First Name - text
		Surname - text
		Date Of Birth - dd dropdown, mm dropdown, yy dropdown
		Post Code - text
		HMO - dropdown 
		Sex/Gender - dropdown (Non-Binary, Prefer Not To Say, Female, Male, Transgender)
		Patient Type - dropdown (A- Private Patient, B- Over 60, C- NHS, D- Family history of glaucoma (FHG), E- Child (under 16), F- Glaucoma)
		Glaucoma - checkbox
 		Diabetic- checkbox
		Include Deceased & Deleted - checkbox
		Sort By - dropdown (ID (Asc), ID (Desc), Surname (Asc), Surname (Desc), Forename (Asc), Forename (Desc), Date Of Birth (Asc), Date Of Birth (Desc), Postcode (Asc), Postcode (Desc) )
		


## Patient Information:

	### - Patient Summary:
		
		(Shows basic information about the patient - name, id, dob, address, age, mobile, tele, email - READ ONLY)


	### - Patient Details:
		
		(Allows editing of patient information)


	### - Patient examination history:
		
		(A table of past examinations for this patient ID - Headers: Date, Staff Member, Type, Subjective Rx, Notes)



## New Examination (creates a new record on "Patient examination history")

	## Spectacle Exam (has multiple tabs - History & Symptoms, Ophthalmoscopy & External Examination, Further Investigative Techniques, Refraction)
		
		### History & Symptoms
			
			GOS Eligibility* - dropdown (Please Select, NOT Eligible, Complex Rx, Diabetes, Family History of Glaucoma And 40 or Over, Glaucoma, Glaucoma Risk, Over 60, ...)
			GOS Form Establishment Name - text
			GOS Form Establishment Town - text
			
			Last Examination Date: (subheading)
			First Examination: - checkbox
			Not Known: - checkbox
			Date: - date selecter
			
			(all of these options have a "select default button that will fill the text box with pre-set "default" information - from reason for visit to FOH)
			Reason For Visit: - text
			POH: - text
			GH: - text
			Medication: - text
			FH: - text
			FOH: - text
			
			Medication - checkboxes for all of the following options:
			ADCAL
			ALENDRONATE
			ALLOPURINOL
			AMITRIPTYLINE
			AMLODIPINE
			ASPIRIN
			ATENOLOL
			ATIVAN
			ATROVENT
			BENDROFLUAZIDE
			BENDROFLUMETHIAZIDE
			BETOPTIC
			BISOPROLOL FUMARATE
			BRUFEN
			CANDESARTAN
			CARBAMAZEPINE
			CLOBAZAM
			CO-CODAMOL
			CO-DYDRAMOL
			COMBIVENT
			DIAZEPAM
			DIGOXIN
			DOXAZOSIN
			EPANUTUN
			EPILIM
			FUROSEMIDE
			GABAPENTIN
			GLICLAZIDE
			HYDROCHLOROQUINE
			INSULIN
			IRBESARTAN
			LANSOPRAZOLE
			LISINOPRIL
			LOSARTAN
			METFORMIN HYDROCHLORIDE
			OMEPRAZOLE
			PARACETAMOL
			PRIMIDONE
			PROCYCLIDINE
			RAMIPRIL
			RANITIDINE
			RISPERIDONE
			SALBUTAMOL
			STATIN
			TAMSULOSIN
			TEGRETOL
			THYROXINE
			TIMOLOL
			TIMOPTOL
			TRAMADOL
			VENTOLIN
			WARFARIN
			XAL-ATAN
			

			Other Notes: - text box
			
			PATIENT INFORMATION (subtitle)
			
			Patient has Glaucoma - checkbox
			Patient has Family History of Glaucoma - checkbox
			Patient is Diabetic - checkbox


		### Ophthalmoscopy & External Examination

			Ophthalmoscopy Notes: - text
			
			Right eye (subtitle)
			
			(all of these options have a "select default button that will fill the text box with pre-set "default" information - from Pupils to Ret. Grading)
			Pupils - dropdown ()
			Lids/Lashes: - dropdown ()
			Lashes: - dropdown ()
			Conjunc: - dropdown ()
			Cornea: - dropdown ()
			Sclera: - dropdown ()
			Ant Ch: - dropdown ()
			Media: - dropdown ()
			CD: - dropdown ()
			AV: - dropdown ()
			Fundus & Periphery - dropdown ()
			Macular - dropdown ()
			Ret. Grading: - dropdown ()
			( for all of the options above, just have 5 filler options to chose from for the prototype )

			Left Eye (subtitle)
			
			Pupils: - dropdown ()
			Lids/Lashes: - dropdown ()
			Lashes: - dropdown ()
			Conjunc: - dropdown ()
			Cornea: - dropdown ()
			Sclera: - dropdown ()
			Ant Ch: - dropdown ()
			Media: - dropdown ()
			CD: - dropdown ()
			AV: - dropdown ()
			Fundus & Periphery: - dropdown ()
			Macular: - dropdown ()
			Ret. GradinC - dropdown ()
			( for all of the options above, just have 5 filler options to chose from for the prototype )

			
		### Further Investigative Techniques

			Drops Used (default = Tropicamide 1%): - checkbox

			Detail & Batch - text
			Expiary - date

			more info - text

			Pre IOP - number
			R - text
			L - text

			Post IOP - text
			R - text
			L - text
			
			CT with Rx - dropdown (use filler information)
			Near CT with RX - dropdown (use filler information) and text box under it

			CT without Rx - dropdown (use filler information)
			Near CT without Rx - dropdown (use filler information) and text box under it

			OMB Near - text
			H - Dropdown (no slip or empty)
 			V - dropdown (no slip or empty)

			Visual Fields - text
			R - dropdown (use filler information)
 			L - dropdown (use filler information)

			Motility - dropdown ("Full & Smooth" or empty)
			
			Amsler Value (subtitle)
			R - dropdown (No distortion or scotomas, Distortion, Scotoma) and text box next to it 
			L - dropdown (No distortion or scotomas, Distortion, Scotoma) and text box next to it

			OMB - text
			H - Dropdown ("no slip" or empty)
			V - dropdown ("no slip" or empty)

			Keratometry (subtitle)
			R - text
			L - text

			NPC - text
			Stereopsis - text
			Colour Vision - dropdown (use filler information)
			Amplitude of Accommodation - text
			
	
		### Refraction

			Current / Previous Rx 1 - (subtitle)
				Right Eye (R) - TABLE

					SPH – number
					CYL – number
					Axis – number
					Prism – number
					Direction – dropdown
					Add – number
					VA – number

				Left Eye (L) - TABLE

					SPH – number
					CYL – number
					Axis – number
					Prism – number
					Direction – dropdown
					Add – number
					VA – number

				Additional Fields

					PD (R/C) – number
					PD (L) – number
					BVD – number
					BIN BCVA – number
					Add any comments including date and location of previous test - textbox


			Previous Rx Other (incl From Autorefractor) - (subtitle)

				Right Eye (R2)

					SPH – number
					CYL – number
					Axis – number
					Prism – number
					Direction – dropdown
					Add – number
					VA – number

				Left Eye (L2)

					SPH – number
					CYL – number
					Axis – number
					Prism – number
					Direction – dropdown
					Add – number
					VA – number


			Retinoscopy - (subtitle)
			
			Right Eye (R)

				Value – text (double click to set default)

			Left Eye (L)

				Value – text (double click to set default)



			Rx Subjective - (subtitle)

				Controls

					Populate From Current Rx – button
					Transpose Rx – button

			Distance Section - (subtitle)

				Right Eye (R)

					UAV – number
					SPH – number
					CYL – number
					Axis – number
					Prism – number
					Direction – dropdown
					VA – number
					Near Add – number
					Prism – number
					Direction – dropdown
					Acuity – dropdown
					Int Add – number
					Prism – number
					Direction – dropdown
					Acuity – dropdown

				Left Eye (L)

					UAV – number
					SPH – number
					CYL – number
					Axis – number
					Prism – number
					Direction – dropdown
					VA – number
					Near Add – number
					Prism – number
					Direction – dropdown
					Acuity – dropdown
					Int Add – number
					Prism – number
					Direction – dropdown
					Acuity – dropdown

				PD & Additional Measurements - (subtitle)

					PD Right – number
					PD Left – number
					PD Combined – number
					BVD – number
					BIN BCVA – number
					Notes / Comments – large text area

				Outcome - (subtitle)

					No Change – Specs needed – radio
					No Change – Specs OK – radio
					New Rx – radio
					No Rx – radio
					Screening Only – radio
					Refer To GP – radio

				Recommendations - (subtitle)

					Distance – checkbox
					Near – checkbox
					Intermediate – checkbox
					High Index – checkbox
					BiFocals – checkbox
					VariFocals – checkbox
					Occupational – checkbox
					Min. Sub – checkbox
					Photochromic – checkbox
					Hardcoat – checkbox
					Tint – checkbox
					Mar – checkbox

				NHS Voucher - (subtitle)

					NHS Voucher (Dist/Bi/Vari) – dropdown
					NHS Voucher (Near) – dropdown

				Examination Comment - (subtitle)

					Set default – button/text trigger
					Examination Comment – textarea

				
				Retest & Patient Info - (subtitle)

					Retest After – dropdown (e.g. 1 Year)
					Patient Type – dropdown (e.g. C-NHS)

				Signature - (subtitle)

					Signed – action/button (“Click To Sign”)


	## Red Eye Exam (not required for prototype)
	## Post op (not required for prototype)
	## Contact Lens Exam (not required for prototype)
	## Contact Lens Aftercare (not required for prototype)
	## Extemal Rx (not required for prototype)
	## Extemal CL Rx (not required for prototype)





# Diary page:

	// This is where all of the allocated timeslots for each examination should show up. It shows information by default as a week view (days across the top and time down the bottom for each day).
	// There are filters and buttons in the header to filter patients by ID , change from week to day view, show cancelled appointments etc...
	// There are forward + back buttons to change the days being shown, a date selecter pop up if the calendar is selected and a date reset that will put it back to todays date.
	// When selecting a timeslot (for a patient), it should show up with the following information in the pop-up box:

	
	## Edit Appointment
		
		Basic information

			Diary Name – dropdown
			Patient Name – text
			View Patient – button
			Appointment Info
			Appt Type – dropdown
			Appt Status – dropdown

		Scheduling

			Date – date
			Start Time – time
			Length – number (likely minutes)

		Additional Info

			Display Text – textarea (multi-line, scrollable)

		Actions

			Update – button
			Update & View – button
			Update & Notify – button
			Cancel – button
