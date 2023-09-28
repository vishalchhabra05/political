<?php

namespace App\Exports;

use App\Models\PatientAppointment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Log;
use JWTAuth;
use DB;
use \Config;

class SponsorsPatientsExport implements FromCollection,WithHeadings,WithColumnWidths,WithStyles
{
	protected $from_age_filter;
	protected $to_age_filter;
	protected $gender_filter;
	protected $trial_name_filter;
	protected $status_filter;
	function __construct($exportData)
	{
		$this->from_age_filter = $exportData['from_age_filter'];
		$this->to_age_filter = $exportData['to_age_filter'];
		$this->gender_filter = $exportData['gender_filter'];
		$this->trial_name_filter = $exportData['trial_name_filter'];
		$this->status_filter = $exportData['status_filter'];
	}

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //return Users::all();
		$this->from_age_filter = $this->from_age_filter;
		$this->to_age_filter = $this->to_age_filter;
		$this->gender_filter = $this->gender_filter;
		$this->trial_name_filter = $this->trial_name_filter;
		$this->status_filter = $this->status_filter;

		$data = PatientAppointment::where('status' , '>', 0);
		if(!empty($this->from_age_filter)){
            $fromAgeFilter = $this->from_age_filter;
            $data = $data->whereHas('patientUserInfo', function ($query) use ($fromAgeFilter) {
                // $query->where(DB::raw('floor(DATEDIFF(CURDATE(),dob) /365)'), '>=', $fromAgeFilter);
                $query->whereEncrypted('age', '>=', $fromAgeFilter);
            });
        }
        if(!empty($this->to_age_filter)){
            $toAgeFilter = $this->to_age_filter;
            $data = $data->whereHas('patientUserInfo', function ($query) use ($toAgeFilter) {
                // $query->where(DB::raw('floor(DATEDIFF(CURDATE(),dob) /365)'), '<=', $toAgeFilter);
                $query->whereEncrypted('age', '<=', $toAgeFilter);
            });
        }
        if(!empty($this->gender_filter)){
            $genderFilter = $this->gender_filter;
            $data = $data->whereHas('patientUserInfo', function ($query) use ($genderFilter) {
                $query->where('gender', $genderFilter);
            });
        }
        if(!empty($this->trial_name_filter)){
            $trialNameFilter = $this->trial_name_filter;
            $data = $data->whereHas('clinicTrialInfo', function ($query) use ($trialNameFilter) {
                $query->where('trial_name', 'LIKE', "%{$trialNameFilter}%");
            });
        }
        if(!empty($this->status_filter)){
            $statusFilter = $this->status_filter;
            $data = $data->where('status', $statusFilter);
        }
		$data = $data->whereHas('clinicTrialInfo', function ($query) {
            $query->where('user_id', JWTAuth::user()->id);
        })
        ->with(['patientUserInfo' => function ($query) {
            $query->select('id', 'first_name', 'last_name', 'profile_image', 'phone_number', 'gender', 'state_id', 'zip_code', 'dob')
            ->with(['stateInfo' => function ($query1) {
	            $query1->select('id', 'name');
	        }])
	        ->with(['userMetaInfo' => function ($query1) {
	            $query1->select('id', 'user_id', 'trials_for', 'race');
	        }])
	        ->with(['userSpeciality' => function ($query1) {
                $query1->select('id', 'user_id', 'trial_category_speciality_id')
                ->with(['specialityInfo' => function ($query2) {
                    $query2->select('trial_category_specialities.id', 'trial_category_specialities.speciality_title');
                }]);
            }])
            ->with(['userCondition' => function ($query1) {
                $query1->select('id', 'user_id', 'trial_speciality_condition_id')
                ->with(['conditionInfo' => function ($query2) {
                    $query2->select('trial_speciality_conditions.id', 'trial_speciality_conditions.condition_title');
                }]);
            }]);
        }])
        ->with(['clinicTrialInfo' => function ($query) {
            $query->select('id', 'trial_name');
        }])
        ->select('id', 'visit_number', 'clinic_trial_id', 'patient_user_id', 'status')
        ->orderBy('id','DESC')
        ->get();

		$row=[];
		$i = 0;
		foreach ($data as $list)
		{
			$name = (!empty($list->patientUserInfo->first_name)?$list->patientUserInfo->first_name:'-');
			if($name != '-'){
				$name = $name.(!empty($list->patientUserInfo->last_name)?' '.$list->patientUserInfo->last_name:'');
			}

			$specialities = [];
            if(!empty($list['patientUserInfo']['userSpeciality'])){
                foreach($list['patientUserInfo']['userSpeciality'] as $userSpecialityRow){
                    if($userSpecialityRow['specialityInfo']){
                        $specialities[] = $userSpecialityRow['specialityInfo']->speciality_title;
                    }
                }
            }

            $conditions = [];
            if(!empty($list['patientUserInfo']['userCondition'])){
                foreach($list['patientUserInfo']['userCondition'] as $userConditionRow){
                    if($userConditionRow['conditionInfo']){
                        $conditions[] = $userConditionRow['conditionInfo']->condition_title;
                    }
                }
            }

            // Status can be - 0-Pending, 1-Screening, 2-Rejected , 3-Cancelled by patient, 4-Screen-Not eligible, 5-Screen-Pending approval, 6-Screen-Approval, 7-Complete, 8-Incomplete, 9-End of study, 10-Early Termination
            $status = ($list->status == 0?"Pending":($list->status == 1?"Screening":($list->status == 2?"Rejected":($list->status == 3?"Cancelled by patient":($list->status == 4?"Screen - Not eligible":($list->status == 5?"Screen - Pending approval":($list->status == 6?"Screen - Approval":($list->status == 7?"Complete":($list->status == 8?"Incomplete":($list->status == 9?"End of study":"Early termination"))))))))));

			$row[$i]['name']  = $name;
			$row[$i]['trial_name']    = $list->clinicTrialInfo->trial_name;
			$row[$i]['phone_number']    = $list->patientUserInfo->phone_number;
			$row[$i]['gender']    = ($list->patientUserInfo->gender == 'M'?'Male':($list->patientUserInfo->gender == 'F'?'Female':'Nonbinary'));
			$row[$i]['state']    = (!empty($list->patientUserInfo->stateInfo->name)?$list->patientUserInfo->stateInfo->name:'-');
			$row[$i]['zipcode']    = $list->patientUserInfo->zip_code;
			$row[$i]['dob']    = $list->patientUserInfo->dob;
			$row[$i]['trials_for']    = $list->patientUserInfo->userMetaInfo->trials_for;
			$row[$i]['seeking_trials_for']    = (count($specialities) == 0?"-":implode(',', $specialities));
			$row[$i]['conditions']    = (count($conditions) == 0?"":implode(',', $conditions));
			$row[$i]['status'] =  $status;
			$i++;
		}
		//var_dump($row); die;
		return collect($row);
    }

    public function headings(): array
	{
		return [
			'Name',
			'Trial Name',
			'Phone Number',
			'Gender',
			'State',
			'Zipcode',
			'DOB',
			'Trials For',
			'Seeking Trials For',
			'Conditions',
			'Status',
		];
	}

	public function columnWidths(): array
	{
		return [
			'A' => 30,
			'B' => 30,
			'C' => 15,
			'D' => 15,
			'E' => 15,
			'F' => 15,
			'G' => 15,
			'H' => 15,
			'I' => 30,
			'J' => 30,
			'K' => 25,
		];
	}

	public function styles(Worksheet $sheet)
	{
		return [
			// Style the first row as bold text.
			1    => ['font' => ['bold' => true]],
		];
	}
}
