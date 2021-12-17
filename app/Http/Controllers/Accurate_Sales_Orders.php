<?php

namespace App\Http\Controllers;

use App\Models\Sales_Order;
use App\Models\Sales_Order_Details;
use Illuminate\Http\Request;

class Accurate_Sales_Orders extends Controller
{
    public function request_list_with_details($access_token, $session){
        $total_page = $this->request_list($access_token, $session, 1)->sp->pageCount;
        $information = $this->request_list($access_token, $session, 1);
        error_log($total_page);
        Sales_Order::truncate();
        for ($page = 0; $page < $total_page; $page++) {
            error_log("Page: " . $page);
            $list = $this->request_list($access_token, $session, $page);

            for ($row = 0; $row < count($list->d); $row++) {
                error_log("Row: " . $row);
                $data = $this->request_details($access_token, $session, $list->d[$row]->id);
                $total_quantity = 0;
                $detailItem=array();
                for ($items = 0; $items < count($data->d->detailItem); $items++) {
                    $total_quantity = $total_quantity + $data->d->detailItem[$items]->quantityDefault;
                    // array_push($detailItem,
                    //     array(
                    //         'name' => $data->d->detailItem[$items]->item->name,
                    //         'no' => $data->d->detailItem[$items]->item->no,
                    //         'quantityDefault' => $data->d->detailItem[$items]->quantityDefault,
                    //         'availableUnitPrice' => $data->d->detailItem[$items]->availableUnitPrice,
                    //         'totalPrice' => $data->d->detailItem[$items]->totalPrice,
                    //     )
                    // );

                    $uniqueCode = 
                    rand(1,10) + rand(1,10)
                    + rand(1,10) + rand(1,100)
                    + rand(1,10) + rand(1,1000)
                    + rand(1,10) + rand(1,10000)
                    + rand(1,10) + rand(1,100000)
                    + rand(1,10) + rand(1,1000000)
                    + rand(1,10) + rand(1,10000000)
                    + rand(1,10) + rand(1,100000000)
                    + rand(1,10) + rand(1,1000000000)
                    + rand(1,10) + rand(1,10000000000)
                    + rand(1,10) + rand(1,100000000000)
                    + rand(1,10) + rand(1,1000000000000)
                    + rand(1,10) + rand(1,10000000000000)
                    ;

                    Sales_Order_Details::create([
                        'so_number' => $data->d->number,
                        'name' => $data->d->detailItem[$items]->item->name,
                        'product_code' => $data->d->detailItem[$items]->item->no,
                        'quantity_bought' => $data->d->detailItem[$items]->quantityDefault,
                        'price_per_unit' => $data->d->detailItem[$items]->availableUnitPrice,
                        'total_price' => $data->d->detailItem[$items]->totalPrice,
                        'oid' => $uniqueCode
                    ]);
                }

                $transDate = explode('/', $data->d->transDate);
                $netDays = explode('/', $data->d->paymentTerm->netDays);
                if(count($transDate) > 1){
                    $transDate = $transDate[2] . '/' . $transDate[1] . '/' . $transDate[0];
                }
                if(count($netDays) > 1){
                    $netDays = $netDays[2] . '/' . $netDays[1] . '/' . $netDays[0];
                }else{
                    $netDays = $transDate;
                }

                Sales_Order::create([
                    'so_number' => $data->d->number,
                    'order_date' => $transDate,
                    'period_date' => $netDays,
                    'payment_method' => $data->d->paymentTerm->name,
                    'customer_name' => $data->d->customer->contactInfo->name,
                    'customer_code' => $data->d->customer->customerNo,
                    'salesman' => $data->d->detailItem[0]->salesmanName,
                    'delivery_address' => $data->d->toAddress,
                    'total_quantities' => $total_quantity,
                    'total_amount' => $data->d->totalAmount,
                    'status' => '232314',
                    'deleted' => 'DEV',
                    'contact_number' => $data->d->customer->contactInfo->workPhone
                ]);

                // $data = array(
                //     'number' => $data->d->number, 
                //     'id' => $data->d->id, 
                //     'totalAmount' => $data->d->totalAmount, 
                //     'total_quantity' => $total_quantity,
                //     'transDate' => $data->d->transDate,
                //     'netDays' => $data->d->paymentTerm->netDays,
                //     'paymentTerm' => $data->d->paymentTerm->name, 
                //     'description' => $data->d->description,
                //     'approvalStatus' => $data->d->approvalStatus,
                //     'salesmanName' => $data->d->detailItem[0]->salesmanName,
                //     'customer' => $data->d->customer->contactInfo->name,
                //     'customerContact' => $data->d->customer->contactInfo->workPhone,
                //     'customerNo' => $data->d->customer->customerNo,
                //     'customerId' => $data->d->customerId,
                //     'toAddress' => $data->d->toAddress,
                //     'detailItem' => $detailItem,
                // );
            }
        }
        return $information;
    }

    public function request_list($access_token, $session, $page){
        $client = new \GuzzleHttp\Client(['base_uri' => 'https://public.accurate.id/']);

        try {
            $response_body = $client->request('GET', 'accurate/api/sales-order/list.do?sp.page=' . $page, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $access_token,
                    'X-Session-ID' => $session,
                ],
                'verify' => false,
                'connect_timeout' => 0,
                'timeout' => 0,
                'read_timeout' => 0,
            ]);
            $response_status = json_decode($response_body->getStatusCode());
            if($response_status != 200){
                error_log($response_status);
                error_log(json_decode($response_body->getBody()));
                return $this->request_list($access_token, $session, $page);
            }
            $response = json_decode($response_body->getBody());
            return $response;
        } catch (Exception $e) {
            error_log($e);
            return $this->request_list($access_token, $session, $page);
        }
    }

    public function request_details($access_token, $session, $id){
        $client = new \GuzzleHttp\Client(['base_uri' => 'https://public.accurate.id/']);
        try {
            $response_body = 
            $client->request('GET', 'accurate/api/sales-order/detail.do?id=' . $id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $access_token,
                    'X-Session-ID' => $session,
                ],
                'verify' => false,
                'connect_timeout' => 0,
                'timeout' => 0,
                'read_timeout' => 0,
            ]);
            $response_status = json_decode($response_body->getStatusCode());
            if($response_status != 200){
                error_log($response_status);
                error_log(json_decode($response_body->getBody()));
                return $this->request_details($access_token, $session, $id);
            }
            error_log($response_body->getBody());
            $response = json_decode($response_body->getBody());
            return $response;
        } catch (Exception $e) {
            error_log("catch (Exception e)");
            error_log($e);
            return $this->request_details($access_token, $session, $id);
        }
    }
}
