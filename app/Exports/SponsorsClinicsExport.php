<?php

namespace App\Exports;

use App\Models\TrialClinicAppointment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Log;
use JWTAuth;
use DB;
use \Config;

class SponsorsClinicsExport implements FromCollection,WithHeadings,WithColumnWidths,WithStyles
{
	protected $clinic_name;
	protected $specialities;
	protected $conditions;
	protected $keywords;
	function __construct($exportData)
	{
		$this->clinic_name = $exportData['clinic_name'];
		$this->specialities = $exportData['specialities'];
		$this->conditions = $exportData['conditions'];
		$this->keywords = $exportData['keywords'];
	}

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //return Users::all();
		$this->clinic_name = $this->clinic_name;
		$this->specialities = $this->specialities;
		$this->conditions = $this->conditions;
		$this->keywords = $this->keywords;

		$data = TrialClinicAppointment::where('sponsor_user_id', JWTAuth::user()->id);
		if(!empty($this->clinic_name)){
            $clinicNameFilter = $this->clinic_name;
            $data = $data->whereHas('trialClinicUserInfo', function ($query) use ($clinicNameFilter) {
                $query->where('clinic_name', 'LIKE', "%{$clinicNameFilter}%");
            });
        }

        if(!empty($this->specialities)){
            $specialityFilter = $this->specialities;
            $data = $data->whereHas('clinicTrialInfo', function ($query) use ($specialityFilter) {
                $query->whereHas('specialities', function ($query1) use ($specialityFilter) {
                    $query1->whereIn('trial_category_speciality_id', $specialityFilter);
                });
            });
        }

        if(!empty($this->conditions)){
            $conditionFilter = $this->conditions;
            $data = $data->whereHas('clinicTrialInfo', function ($query) use ($conditionFilter) {
                $query->whereHas('conditions', function ($query1) use ($conditionFilter) {
                    $query1->whereIn('trial_speciality_condition_id', $conditionFilter);
                });
            });
        }

        if(!empty($this->keywords)){
            $keywordFilter = $this->keywords;
            $data = $data->whereHas('clinicTrialInfo', function ($query) use ($keywordFilter) {
                $query->where('trial_name', 'LIKE', "%{$keywordFilter}%");
            });
        }

		$data = $data->with(['trialClinicUserInfo' => function ($query) {
            $query->select('id', 'clinic_name', 'phone_number', 'address', 'state_id', 'zip_code')
	        ->with(['userMetaInfo' => function ($query1) {
	            $query1->select('id', 'user_id', 'brief_intro');
	        }]);
        }])
        ->with(['clinicTrialInfo' => function ($query) {
            $query->select('id', 'trial_name');
        }])
        ->select('id', 'trial_clinic_user_id', 'clinic_trial_id', 'status')
        ->whereIn('status', [1,2,3])
        ->orderBy('id','DESC')
        ->get();

		$row=[];
		$i = 0;
		foreach ($data as $list)
		{
            // Status can be - 1-Approved, 2-Rejected, 3-Completed
            $status = ($list->status == 1?"Approved":($list->status == 2?"Rejected":($list->status == 3?"Completed":"-")));

			$row[$i]['clinic_name']  = $list->trialClinicUserInfo->clinic_name;
			$row[$i]['trial_name']    = $list->clinicTrialInfo->trial_name;
			$row[$i]['status']    = $status;
			$row[$i]['phone_number']  = $list->trialClinicUserInfo->phone_number;
			$row[$i]['address']    = $list->trialClinicUserInfo->address;
			$row[$i]['additional_information']    = $list->trialClinicUserInfo->userMetaInfo->brief_intro;
			$i++;
		}
		//var_dump($row); die;
		return collect($row);
    }

    public function headings(): array
	{
		return [
			'Clinic Name',
			'Trial Name',
			'Status',
			'Phone Number',
			'Address',
			'Additional Information',
		];
	}

	public function columnWidths(): array
	{
		return [
			'A' => 20,
			'B' => 30,
			'C' => 15,
			'D' => 15,
			'E' => 20,
			'F' => 30,
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
