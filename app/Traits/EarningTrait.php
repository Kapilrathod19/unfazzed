<?php

namespace App\Traits;

use App\Models\User;
use App\Models\ProviderPayout;
use App\Models\CommissionEarning;
use App\Models\Wallet;
use App\Models\WalletHistory;


trait EarningTrait {

    public function get_provider_commission($bookings,$provider){
      

        $total_amount=$bookings->sum('total_amount');

        $provider_earning=$this->getProviderCommission($bookings);

        $provider_paid_earning = ProviderPayout::where('provider_id',$provider->id)->sum('amount') ?? 0;

        $handyman_earnings=$this->getHandymanCommission($bookings);

        $data=[
             'total_earning'=>$total_amount,
             'provider_total_earning'=> $provider_earning,
             'provider_paid_earning'=>  $provider_paid_earning,
             'provider_due_earning'=>$provider_earning - $provider_paid_earning,
             'admin_earning'=>$total_amount-$handyman_earnings-$provider_earning,
             
        ];

        return $data;
    }

    public function getProviderCommission($bookings)
    {
        $providerEarning = 0;
    
        foreach ($bookings as $booking) {
            $commissionData = json_decode($booking->commission_data, false);
    
            if ($commissionData) {
                if ($commissionData->type == 'percent') {
                    $providerEarning += ($booking->total_amount * $commissionData->commission) / 100;
                } else {
                    $providerEarning += $commissionData->commission;
                }
            }
        }
    
        return $providerEarning;
    }


    public function getHandymanCommission($bookings)
    {
        $handymanEarning = 0;
    
        foreach ($bookings as $booking) {

            $providerId = $booking->provider_id;

            $handyman = $booking->handymanAdded()->where('handyman_id', '!=', $providerId)->get();
  
                foreach ($handyman as $handyman) {

                    $commissionData = json_decode($handyman->commission_data, false);

                    if( $commissionData){

                        if ($commissionData->type == 'percent') {

                            $handymanEarning += ($booking->total_amount * $commissionData->commission) / 100;

                        }else{

                            $handymanEarning += $commissionData->commission;

                        }  

                    }
                }
           }

        return $handymanEarning;
    }


    public function getProviderBookingCommission($booking, $payment,$handyman_earning)
    {
        $provider_commission_data = [];
        $providerEarning = 0;
    
        $provider = User::where('id', $booking['provider_id'])->with('providertype')->first();
        $provider_commission = $provider->providertype ? json_encode($provider->providertype) : null;
        $commissionData = json_decode($provider_commission, false);
    
        if ($commissionData) {
            if ($commissionData->type === 'percent') {
                $providerEarning += ($booking->final_sub_total * $commissionData->commission) / 100;
            } else {
                $providerEarning += $commissionData->commission;
            }
        }
        if($handyman_earning > 0){
            $providerEarning = $providerEarning - $handyman_earning;
        }
        $payment_status = 'pending';
        if($payment && $payment->payment_status === 'paid'){ 
            $payment_status = 'unpaid';
        }
    
        $provider_commission_data = [
            'employee_id'       => $booking->provider_id,
            'booking_id'        => $booking->id,
            'user_type'         => 'provider',
            'commission_amount' => $providerEarning,
            'commission_status' => $payment_status,
            'commissions'       => $provider_commission
        ];
    
        return $provider_commission_data;
    }
    

    public function getHandymanBookingCommission($booking, $payment,$provider_earning)
    {
        $handyman_commission_data = [];
        $handymanEarning = 0;
    
        $providerId = $booking->provider_id;
        $handymen = $booking->handymanAdded()->where('handyman_id', '!=', $providerId)->get();

    
        foreach ($handymen as $handyman) {
            $handymanData = User::where('id', $handyman->handyman_id)->with('handymantype')->first();

            $handyman_commission = $handymanData->handymantype ? json_encode($handymanData->handymantype) : null;
            $commissionData = json_decode($handyman_commission, false);
    
            if ($commissionData) {
                if ($commissionData->type === 'percent') {
                    $handymanEarning = 0;
                    if($booking->total_amount > 0){
                        $handymanEarning += ($booking->total_amount * $commissionData->commission) / 100;
                    }
                    
                } else {
                    $handymanEarning += $commissionData->commission;
                }
            }

            $payment_status = 'pending';
            if($payment && $payment->payment_status === 'paid'){ 
                $payment_status = 'unpaid';
            }
    
            $handyman_commission_data[] = [
                'employee_id'       => $handyman->handyman_id,
                'booking_id'        => $booking->id,
                'user_type'         => 'handyman',
                'commission_amount' => $handymanEarning,
                'commission_status' => $payment_status,
                'commissions'       => $handyman_commission
            ];

    
        }
    
        return $handyman_commission_data;
    }
    

    public function addBookingCommission($bookingdata)
    {
        // ------------------------------------------------------------------
        // RE-SYNC SNAPSHOTS IF MISSING (To fix old bookings or sync issues)
        // ------------------------------------------------------------------
        if ($bookingdata->final_sub_total <= 0 || $bookingdata->total_amount <= 0) {
            $bookingdata->final_total_service_price = $bookingdata->getServiceTotalPrice();
            $bookingdata->final_discount_amount = $bookingdata->getDiscountValue();
            $bookingdata->final_coupon_discount_amount = $bookingdata->getCouponDiscountValue();
            
            $subtotal = $bookingdata->getSubTotalValue() + $bookingdata->getServiceAddonValue() + $bookingdata->getServiceOptionValue() + $bookingdata->getExtraChargeValue();
            $bookingdata->final_sub_total = $subtotal;
            
            $tax = $bookingdata->getTaxesValue();
            $bookingdata->final_total_tax = $tax;
            
            $bookingdata->total_amount = $subtotal + $tax > 0 ? $subtotal + $tax : $bookingdata->getTotalValue();
            $bookingdata->save();
        }

        $payment = $bookingdata->payment;
        $provider_earning = 0;
        $handyman_earning = 0;

        if ($bookingdata->provider_id) {
            $provider_commission_data = $this->getProviderBookingCommission($bookingdata, $payment,$handyman_earning);
            $provider_earning = $this->saveCommission($provider_commission_data);
        }

        if ($bookingdata->handymanAdded) {
            $handyman_commission_data = $this->getHandymanBookingCommission($bookingdata, $payment, $provider_earning);
            foreach ($handyman_commission_data as $commission_data) {
                $handyman_earning += $this->saveCommission($commission_data);
            }
            if ($handyman_earning > 0) {
                $provider_commission_data = $this->getProviderBookingCommission($bookingdata, $payment, $handyman_earning);
                $provider_earning = $this->saveCommission($provider_commission_data);
            }
        }

        // If provider earning is still 0 (e.g. no handyman but provider exists), ensure it's saved
        if ($provider_earning == 0 && $bookingdata->provider_id) {
            $provider_commission_data = $this->getProviderBookingCommission($bookingdata, $payment, 0);
            $provider_earning = $this->saveCommission($provider_commission_data);
        }

        $payment_status = $payment && $payment->payment_status == 'paid' ? 'unpaid' : 'pending';

        $admin_earning = $bookingdata->total_amount - $provider_earning - $handyman_earning;
        $admin_commission_data = [
            'employee_id'       => User::where('user_type', 'admin')->value('id'),
            'booking_id'        => $bookingdata->id,
            'user_type'         => 'admin',
            'commission_amount' => $admin_earning,
            'commission_status' => $payment_status,
            'commissions'       => null
        ];
        $this->saveCommission($admin_commission_data);

        // ------------------------------------------------------------------
        // WALLET UPDATE LOGIC (Add earning to Provider/Handyman Wallet)
        // ------------------------------------------------------------------
        if ($payment_status === 'unpaid') {
            // Update Provider Wallet
            if ($provider_earning > 0) {
                $this->updateUserWallet($bookingdata->provider_id, $provider_earning, $bookingdata->id, 'provider');
            }
            // Update Handyman Wallet
            if ($handyman_earning > 0) {
                $bookingdata->load('handymanAdded'); // Reload to ensure fresh data
                foreach ($bookingdata->handymanAdded as $handyman) {
                    $specific_commission = CommissionEarning::where('booking_id', $bookingdata->id)
                        ->where('employee_id', $handyman->handyman_id)
                        ->where('user_type', 'handyman')
                        ->value('commission_amount');
                    
                    if ($specific_commission > 0) {
                        $this->updateUserWallet($handyman->handyman_id, $specific_commission, $bookingdata->id, 'handyman');
                    }
                }
            }
        }
    }

    /**
     * Helper to update user wallet and record history
     */
    protected function updateUserWallet($userId, $amount, $bookingId, $userType)
    {
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $userId],
            ['title' => User::find($userId)->display_name . ' Wallet', 'amount' => 0, 'status' => 1]
        );

        // Prevent double adding by checking history
        $exists = WalletHistory::where('user_id', $userId)
            ->where('activity_message', 'LIKE', '%#' . $bookingId . '%')
            ->where('activity_type', 'booking_earning')
            ->exists();

        if (!$exists) {
            $wallet->amount += $amount;
            $wallet->save();

            $historyData = [
                'user_id' => $userId,
                'activity_type' => 'booking_earning',
                'activity_message' => __('messages.booking_earning_message', ['id' => $bookingId, 'amount' => getPriceFormat($amount)]),
                'datetime' => date('Y-m-d H:i:s'),
                'activity_data' => json_encode([
                    'booking_id' => $bookingId,
                    'amount' => $amount,
                    'transaction_type' => 'Credit'
                ])
            ];
            WalletHistory::create($historyData);
        }
    }

    protected function saveCommission($commission_data)
    {
        $res = CommissionEarning::updateOrCreate(
            [
                'booking_id' => $commission_data['booking_id'],
                'user_type' => $commission_data['user_type'],
                'employee_id' => $commission_data['employee_id']
            ],
            $commission_data
        );
        return $res->commission_amount;
    }


}



    

?>