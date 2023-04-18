<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use transaction model
use App\Models\transaction;
class AdminFinanceController extends Controller
{
    //
    private function GetCurrentMonthMaxMinDays($LastMonth = false)
    {
        $MonthMaxDaysArray = array(
            '01' => '31',
            '02' => '28',
            '03' => '31',
            '04' => '30',
            '05' => '31',
            '06' => '30',
            '07' => '31',
            '08' => '31',
            '09' => '30',
            '10' => '31',
            '11' => '30',
            '12' => '31',
        );
        //get current date in month
        $currentDate = date('Y-m-d');
        $currentYear = date('Y', strtotime($currentDate));
        //get current month
        $currentMonth = date('m', strtotime($currentDate));
        //if last month is true
        if ($LastMonth) {
            $currentMonth = $currentMonth - 1;
            $currentMonth = str_pad($currentMonth, 2, '0', STR_PAD_LEFT);
            if ($currentMonth == '00') {
                $currentMonth = '12';
                $currentYear = $currentYear - 1;
            }
        }
        $minDays = '01';
        //if current month is february
        $maxDays = $MonthMaxDaysArray[$currentMonth];
        if ($currentMonth == '02') {
            if ($currentYear % 4 == 0) $maxDays = '29';
            else $maxDays = '28';
        } else $maxDays = $MonthMaxDaysArray[$currentMonth];
        //create date with minDays CurrentMonth and CurrentYear
        $minDate = $currentYear . '-' . $currentMonth . '-' . $minDays;
        //create date with maxDays CurrentMonth and CurrentYear
        $maxDate = $currentYear . '-' . $currentMonth . '-' . $maxDays;
        // $this->db->where('transaction_date >=', $minDate);
        // $this->db->where('transaction_date <=', $maxDate);
        return array($minDate, $maxDate);
    }

    private function moneyAmerican($amount)
{
    $regex = $amount / 100;
    $usd = number_format($regex, 2, '.', ',');
    return "$ ".$usd;
}

    public function raporlar(){
        $data = transaction::all();

        //get transactions where status is 0
        //-7 days from now
        $data_7 = transaction::where('transaction_status',0)->where('created_at','>=',date('Y-m-d H:i:s', strtotime('-7 days')));
        $data_7_sum=$this->moneyAmerican($data_7->sum('transaction_amount'));
        //group by date and show sum of transaction_amount and count of transaction_id
        $data_7 = $data_7->selectRaw('DATE(created_at) as date, sum(transaction_amount) as amount, count(*) as count')->groupBy('date')->get();

        $arr=$this->GetCurrentMonthMaxMinDays();
        $startDate = $arr[0];
        $endDate = $arr[1];

        
        //get transactions where status is 0 and transaction_date is between startDate and endDate
        $data_30 = transaction::where('transaction_status',0)->where('created_at','>=',$startDate)->where('created_at','<=',$endDate);
        $data_30_sum=$this->moneyAmerican($data_30->sum('transaction_amount'));
        //group by date and show sum of transaction_amount and count of transaction_id
        $data_30 = $data_30->selectRaw('DATE(created_at) as date, sum(transaction_amount) as amount, count(*) as count')->groupBy('date')->get();

        $arr=$this->GetCurrentMonthMaxMinDays(true);
        $startDate = $arr[0];
        $endDate = $arr[1];

        $data_60=transaction::where('transaction_status',0)->where('created_at','>=',$startDate)->where('created_at','<=',$endDate);
        $data_60_sum=$this->moneyAmerican($data_60->sum('transaction_amount'));

        $data_60 = $data_60->selectRaw('DATE(created_at) as date, sum(transaction_amount) as amount, count(*) as count')->groupBy('date')->get();
        

        
        return view('Admin/Finance/raporlar',['data_7' => $data_7, 'data_30' => $data_30, 'data_7_sum' => $data_7_sum, 'data_30_sum' => $data_30_sum, 'data_60' => $data_60, 'data_60_sum' => $data_60_sum]);
    }

    public function datebydateReports(Request $req){
        $startDate = $req->startDate;
        $endDate = $req->endDate;
        $Maindata = transaction::where('transaction_status',0)->where('created_at','>=',$startDate)->where('created_at','<=',$endDate);
        $data_sum=$this->moneyAmerican($Maindata->sum('transaction_amount'));
        $data_count = $Maindata->count();
        $data = $Maindata->selectRaw('DATE(created_at) as date, sum(transaction_amount) as amount, count(*) as count')->groupBy('date')->get();
        //return json response 
        return response()->json(["status"=>true,'data' => $data, 'data_sum' => $data_sum, 'data_count' => $data_count]);
        
    }
}
