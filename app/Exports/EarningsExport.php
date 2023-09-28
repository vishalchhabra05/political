<?php

namespace App\Exports;

use App\Models\User;
use App\Models\TrialPayment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Log;
use \Config;

class EarningsExport implements FromCollection,WithHeadings,WithColumnWidths,WithStyles
{
	protected $payment_type;
	protected $paid_by;
	function __construct($exportData)
	{
		$this->payment_type = $exportData[0];
		$this->paid_by = $exportData[1];
	}

	/**
	* @return \Illuminate\Support\Collection
	*/
	public function collection()
	{
		//return Users::all();
		$this->payment_type = $this->payment_type;
		$this->paid_by = $this->paid_by;
		\Log::info("startt");
		\Log::info($this->payment_type);
		\Log::info($this->paid_by);

		$data = TrialPayment::with(['clinicTrialInfo' => function ($query) {
        	$query->select('id', 'trial_name');
	    }])
	    ->with(['fromUserInfo' => function ($query) {
	        $query->select('id', 'first_name', 'last_name', 'sponsor_name', 'clinic_name', 'email', 'role');
	    }])
	    ->with(['toUserInfo' => function ($query) {
	        $query->select('id', 'first_name', 'last_name', 'sponsor_name', 'clinic_name', 'email', 'role');
	    }])->latest();
		if(!empty($this->payment_type)){
			$data = $data->where('payment_type','=',$this->payment_type);
		}
		if(!empty($this->paid_by)){
			$data = $data->where('paid_by','=',$this->paid_by);
		}

		$data = $data->select('trial_payments.id', 'trial_payments.from_user_id', 'trial_payments.to_user_id', 'trial_payments.clinic_trial_id', 'trial_payments.amount', 'trial_payments.payment_type', 'trial_payments.paid_by', 'trial_payments.transaction_id', 'trial_payments.payment_status', 'trial_payments.qb_invoice_id', 'trial_payments.qb_bill_id', 'trial_payments.created_at')->get();

		$row=[];
		$i = 0;
		foreach ($data as $list)
		{
			$patientRoleId = Config('params.role_ids.patient');
			$trialClinicRoleId = Config('params.role_ids.trialclinic');
			$sponsorRoleId = Config('params.role_ids.sponsor');
			$physicianRoleId = Config('params.role_ids.physician');

			$paymentBy = '-';
            if($list->fromUserInfo->role == $patientRoleId || $list->fromUserInfo->role == $physicianRoleId){
                $paymentBy = (!empty($list->fromUserInfo->first_name)?$list->fromUserInfo->first_name:'-');
                if($paymentBy != '-'){
                    $paymentBy = $paymentBy.(!empty($list->fromUserInfo->last_name)?' '.$list->fromUserInfo->last_name:'');
                }
            }elseif($list->fromUserInfo->role == $trialClinicRoleId){
                $paymentBy = (!empty($list->fromUserInfo->clinic_name)?$list->fromUserInfo->clinic_name:'-');
            }elseif($list->fromUserInfo->role == $sponsorRoleId){
                $paymentBy = (!empty($list->fromUserInfo->sponsor_name)?$list->fromUserInfo->sponsor_name:'-');
            }

            $paymentTo = '-';
            if($list->toUserInfo->role == $patientRoleId || $list->toUserInfo->role == $physicianRoleId){
                $paymentTo = (!empty($list->toUserInfo->first_name)?$list->toUserInfo->first_name:'-');
                if($paymentTo != '-'){
                    $paymentTo = $paymentTo.(!empty($list->toUserInfo->last_name)?' '.$list->toUserInfo->last_name:'');
                }
            }elseif($list->toUserInfo->role == $trialClinicRoleId){
                $paymentTo = (!empty($list->toUserInfo->clinic_name)?$list->toUserInfo->clinic_name:'-');
            }elseif($list->toUserInfo->role == $sponsorRoleId){
                $paymentTo = (!empty($list->toUserInfo->sponsor_name)?$list->toUserInfo->sponsor_name:'-');
            }

            // Payment Types - 1-Sponsor to clinic payment for trial, 2-Trial clinic to patient payment for trial visit, 3-Admin to physician payment for referal, 4-Admin to patient payment for first time screening
            $paymentType = ($list->payment_type == 1?"SponsorToClinic payment for trial":($list->payment_type == 2?"ClinicToPatient payment for trial visit":($list->payment_type == 1?"AdminToPhysician payment for referal":"AdminToPatient payment for 1st screening")));

            //  Status - 0-Pending, 1-Success, 2-Failed
            $paymentStatus = ($list->payment_status == 0?"Pending":($list->payment_status == 1?"Success":"Failed"));

			$row[$i]['transaction_id']  = $list->transaction_id;
			$row[$i]['payment_by']    = $paymentBy;
			$row[$i]['payment_to']    = $paymentTo;
			$row[$i]['trial']    = (!empty($list->clinicTrialInfo->trial_name)?$list->clinicTrialInfo->trial_name:'-');
			$row[$i]['amount']    = $list->amount;
			$row[$i]['payment_type']    = $paymentType;
			$row[$i]['paid_by']    = $list->paid_by;
			$row[$i]['payment_status']    = $paymentStatus;
			$row[$i]['qb_invoice_id']    = (!empty($list->qb_invoice_id)?$list->qb_invoice_id:'-');
			$row[$i]['qb_bill_id']    = (!empty($list->qb_bill_id)?$list->qb_bill_id:'-');
			$row[$i]['created_at']    = (!empty($row->created_at) && $row->created_at != '0000-00-00 00:00:00'?DMYDateFromat($row->created_at):'-');
			$i++;
		}
		//var_dump($row); die;
		return collect($row);
	}

	public function headings(): array
	{
		return [
			'Transaction Id',
			'Payment By',
			'Payment To',
			'Trial',
			'Amount',
			'Payment Type',
			'Paid By',
			'Payment Status',
			'QB Invoice Id',
			'QB Bill Id',
			'Status',
		];
	}

	public function columnWidths(): array
	{
		return [
			'A' => 25,
			'B' => 30,
			'C' => 30,
			'D' => 25,
			'E' => 15,
			'F' => 25,
			'G' => 15,
			'H' => 15,
			'I' => 20,
			'J' => 20,
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
