<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Log;
use \Config;

class UsersExport implements FromCollection,WithHeadings,WithColumnWidths,WithStyles
{
	protected $status;
	protected $user_type;
	function __construct($exportData)
	{
		$this->status = $exportData[0];
		$this->user_type = $exportData[1];
	}

	/**
	* @return \Illuminate\Support\Collection
	*/
	public function collection()
	{
		//return Users::all();
		$this->status = $this->status;
		$this->user_type = $this->user_type;

		$allUserRoleIds = Config::get('params.user_role_ids');
		$data = User::whereIn('role', $allUserRoleIds)->latest();
		if(!empty($this->status)){
			$data = $data->where('status','=',$this->status);
		}
		if(!empty($this->user_type)){
			$data = $data->where('role','=',$this->user_type);
		}
		$data = $data->select('id', 'first_name', 'last_name', 'clinic_name', 'sponsor_name', 'email', 'phone_number', 'role', 'status')->get();

		$row=[];
		$i = 0;
		foreach ($data as $list)
		{
			$patientRoleId = Config('params.role_ids.patient');
			$trialClinicRoleId = Config('params.role_ids.trialclinic');
			$sponsorRoleId = Config('params.role_ids.sponsor');
			$physicianRoleId = Config('params.role_ids.physician');

			$name = '-';
			if($list->role == $patientRoleId || $list->role == $physicianRoleId){
				$name = (!empty($list->first_name)?$list->first_name:'-');
				if($name != '-'){
					$name = $name.(!empty($list->last_name)?' '.$list->last_name:'');
				}
			}elseif($list->role == $trialClinicRoleId){
				$name = (!empty($list->clinic_name)?$list->clinic_name:'-');
			}elseif($list->role == $sponsorRoleId){
				$name = (!empty($list->sponsor_name)?$list->sponsor_name:'-');
			}

			$row[$i]['first_name']  = $name;
			$row[$i]['email']    = $list->email;
			$row[$i]['phone_number']    = $list->phone_number;
			$row[$i]['role']  = ($list->role === 2)?'Patient':(($list->role  === 3)?'Trial Clinic':(($list->role  === 4)?'Physician':(($list->role  === 5)?'Sponsor':'Admin')));
			$row[$i]['status'] =  ($list->status === 0)?'Pending':(($list->status  === 1)?'Active':(($list->status  === 2)?'Inactive':(($list->status  === 3)?'Deleted':'-')));
			$i++;
		}
		//var_dump($row); die;
		return collect($row);
	}

	public function headings(): array
	{
		return [
			'Name',
			'Email',
			'Phone Number',
			'Role',
			'Status',
		];
	}

	public function columnWidths(): array
	{
		return [
			'A' => 30,
			'B' => 30,
			'C' => 15,
			'D' => 25,
			'E' => 25,
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
