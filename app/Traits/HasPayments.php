<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait HasPayments {


    /**
     * 
     * @return $number
     */
    public function payments() 
    {
        $payments = $this->receiptVoucher;
        $total =0;
        foreach ($payments as $payment) {
           $total+= $payment->value;
        }
        return $total;
    }
    /**
     *  
     * @return $number
     */
    public function totalFees() {
        
        $tuition_fees_total = $this->calculatePaymentPartitions('App\Models\TuitionFee',"tuitionFees");
        $general_fees_total = $this->calculatePaymentPartitions('App\Models\GeneralFee',"otherFees");
        // $transport_fees_total = $this->calculatePaymentPartitions('App\Models\TransportFee',"transportFees");
        return  $this->calculatePaymentPartitions();
    }
 
    /**
     * @param string $model
     * @param string $fee_type
     * @return $number
     */
    public function calculatePaymentPartitions():float {

        $fee_types = ["tuitionFees"];

        $total_sum=0;
        $total= $value_after_discount=$value_after_tax=[];
        foreach ($fee_types as $fee_type)
        {
            foreach ($this->{$fee_type} as $ind=>$fee)
            {
                    if(count($fee->payment_partition))
                    {
                        foreach ($fee->payment_partition as $i=> $partition)
                        {
                            $can_be_calculated  =false;
                            if($fee_type =='tuitionFees')
                            {
                                if($this->termination_date ==null || $this->termination_date > $partition['due_date'])
                                {
                                    $can_be_calculated = true;
                                }
                            }
                            else //transport fee
                            {
                                if($this->transport && ( $this->transport->termination_date == null || $this->transport->termination_date > $partition['due_date']))
                                {
                                    $can_be_calculated = true;
                                }
                            }
                            //calculate only if the requirement is ok
                            if($can_be_calculated)
                            {
                                switch ($fee_type) {
                                    case 'tuitionFees':
                                        $model = "App\Models\TuitionFee";
                                        break;
                                    case 'otherFees':
                                        $model = "App\Models\GeneralFee";
                                        break;
                                    
                                    default:
                                        $model ='App\Models\TransportFee';
                                        break;
                                }
                                //if has discounts
                                $value_after_discount[$i]=$value_after_tax[$i] = 0;
                                
                                $discounts = DB::table('student_fee')
                                            ->where('student_id', $this->id)
                                            ->where('feeable_id', $fee->id)
                                            ->where('feeable_type', $model)
                                            ->value('discounts');
                                $decodedDiscounts = json_decode($discounts, true);
                            
                                if(isset($decodedDiscounts[$i]) && array_key_exists('discount_value',$decodedDiscounts[$i]))
                                {
                                    if($decodedDiscounts[$i]['discount_type'] == 'percentage')
                                        {
                                            $value_after_discount[$i] =floatval($partition['value']) * (1 - ($decodedDiscounts[$i]['discount_value'] / 100));
                                        }
                                        else{
                                            $value_after_discount[$i] = floatval($partition['value']) - $decodedDiscounts[$i]['value'];
                                        }
                                }
                                if($this->nationality == "saudian" && $fee_type =='tuitionFees')
                                {
                                    // $vat = \App\Models\ValueAddedTax::first();
                                    $value_after_tax[$i] = $value_after_discount[$i] ?? floatval($partition['value']);
                                }else{
                                    // if($fee_type !='tuitionFees')
                                    // {
                                    
                                        $vat = \App\Models\ValueAddedTax::first();
                                        $value_after_tax[$i] = ($vat->percentage / 100) * ($value_after_discount[$i] ?? floatval($partition['value']));
                                    // }
                                    
                                }
                                
                                $total[$i] = ($value_after_discount[$i] ?? floatval($partition['value'])) + ($value_after_tax[$i] ?? 0);

                            }
                        }
                    }
                    $total_sum+= array_sum($total);
            }


        }
       return $total_sum;


    }
    /**
     * calculate fees discounts for (transport / tuition) fees reports
     * @return float
     */
    public function calculateFeesDiscounts($model,$fee_type) {

        $total_sum=0;
        foreach ($this->{$fee_type} as $fee)
        {
                 if(count($fee->payment_partition))
                 {
                    foreach ($fee->payment_partition as $i=> $partition)
                    {
                        $can_be_calculated  =false;
                        if($model =='App\Models\TuitionFee')
                        {
                            if($this->termination_date ==null || $this->termination_date > $partition['due_date'])
                            {
                                $can_be_calculated = true;
                            }
                        }
                        else //transport fee
                        {
                            if( $this->transport && ( $this->transport->termination_date == null || $this->transport->termination_date > $partition['due_date']))
                            {
                                $can_be_calculated = true;
                            }
                        }
                        //calculate only if the requirement is ok
                        if($can_be_calculated)
                        {
                            //if has discounts
                            $discount_value = 0;
                            
                            $discounts = DB::table('student_fee')
                                        ->where('student_id', $this->id)
                                        ->where('feeable_id', $fee->id)
                                        ->where('feeable_type', $model)
                                        ->value('discounts');
                            $decodedDiscounts = json_decode($discounts, true);
                            
                        
                            if(isset($decodedDiscounts[0]))
                            {
                                if($decodedDiscounts[0]['discount_type'] == 'percentage')
                                    {
                                        $value_after_discount =$partition['value'] * (1 - ($decodedDiscounts[0]['discount_value'] / 100));
                                        $discount_value = $partition['value'] - $value_after_discount;
                                    }
                                    else{
                                        $discount_value =  $decodedDiscounts[0]['discount_value'];
                                    }
                            }
                            $total[$i] = ($discount_value);

                        }
                    }
                }
                $total_sum+= array_sum($total);
        }
       return $total_sum;

    }

}