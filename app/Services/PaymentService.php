<?php

namespace App\Services;

use App\Models\CompanyService;
use App\Models\Order;
use App\Repositories\OrderRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentService {
    /**
     * function contructer
     *
     * @param OrderRepository $repository
     */
    public function __construct(
        protected readonly OrderRepository $repository
    ) {
    }

    public function pay($order) {
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = "http://localhost/vnpay_php/vnpay_return.php";
        $vnp_apiUrl = "http://sandbox.vnpayment.vn/merchant_webapi/merchant.html";
        $apiUrl = "https://sandbox.vnpayment.vn/merchant_webapi/api/transaction";
        //Config input format
        //Expire
        $startTime = date("YmdHis");
        $expire = date('YmdHis',strtotime('+15 minutes',strtotime($startTime)));


        if ($order['company_id'] != Auth::guard('api-user')->user()->company[0]['id']) {
            return [false, 'Đơn hàng không phù hợp. Vui lòng kiểm tra lại', ''];
        }

        if ($order['payment_status'] != config('custom.status-payment.unpaid')) {
            return [false, 'Đơn hàng đã thanh toán', ''];
        }

        $vnp_TmnCode = env('vnp_TmnCode'); //Mã website tại VNPAY
        $vnp_HashSecret = env('vnp_HashSecret'); //Chuỗi bí mật
        $vnp_Returnurl = route('payment-return');

        $vnp_TxnRef = $order->id;
        $vnp_OrderInfo = 'Thanh toán đơn hàng ' . $order->id . '. Số tiền ' . number_format((int)$order->total * 100) . ' VND';
        $vnp_Amount = (int)$order->total * 100;
        $vnp_Locale = 'vn';
        $vnp_BankCode = 'NCB';
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => "other",
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
            // "vnp_ExpireDate"=>$expire
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
        
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        
        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret);//  
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        return [true, '', $vnp_Url];
    }

    public function paymentReturn($request) {
        $requestData = $request->all();

        $vnp_SecureHash = $requestData['vnp_SecureHash'];
        $inputData = array();
        foreach ($requestData as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
        
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, env('vnp_HashSecret'));

        if ($secureHash == $vnp_SecureHash) {
            if ($requestData['vnp_ResponseCode'] == '00') {
                return [true, 'Giao dịch thành công'];
            } else {
                return [true, 'Giao dịch không thành công'];
            }
        } else {
            return [true, 'Giao dịch không hợp lệ'];
        }
    }

    public function callback() {
        $inputData = array();
        $returnData = array();
        foreach ($_GET as $key => $value) {
                    if (substr($key, 0, 4) == "vnp_") {
                        $inputData[$key] = $value;
                    }
                }
        
        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }
        
        $secureHash = hash_hmac('sha512', $hashData, env('vnp_HashSecret'));
        $vnpTranId = $inputData['vnp_TransactionNo']; //Mã giao dịch tại VNPAY
        $vnp_BankCode = $inputData['vnp_BankCode']; //Ngân hàng thanh toán
        $vnp_Amount = $inputData['vnp_Amount']/100; // Số tiền thanh toán VNPAY phản hồi
        
        $statusPayment = config('custom.status-payment.unpaid'); // Là trạng thái thanh toán của giao dịch chưa có IPN lưu tại hệ thống của merchant chiều khởi tạo URL thanh toán.
        $orderId = $inputData['vnp_TxnRef'];
        
        try {
            //Kiểm tra checksum của dữ liệu
            if ($secureHash == $vnp_SecureHash || true) {
                $order = Order::where('id', $orderId)->first();

                if (!empty($order)) {
                    if($order["total"] == $vnp_Amount) //Kiểm tra số tiền thanh toán của giao dịch: giả sử số tiền kiểm tra là đúng.
                    {
                        if ($order["payment_status"] != NULL && $order["payment_status"] == config('custom.status-payment.unpaid')) {
                            if ($inputData['vnp_ResponseCode'] == '00' && $inputData['vnp_TransactionStatus'] == '00') {
                                $statusPayment = config('custom.status-payment.paid'); // Trạng thái thanh toán thành công
                            } else {
                                $statusPayment = config('custom.status-payment.pay_failed'); // Trạng thái thanh toán thất bại / lỗi
                            }

                            DB::beginTransaction();
                            /**
                             * Danh sách dịch vụ trước đó của công ty
                             * Nếu dịch vụ trước đó vẫn còn thời gian sử dụng mà đăng ký mua thêm thì sẽ cộng thêm thời gian vào dịch vụ hiện tại 
                             */
                            $companyId = Auth::guard('api-user')->user()->company[0]['id'];
                            $serviceExit = CompanyService::where('company_id', $companyId)->where('expiration_date', '>=', Carbon::today())->orderBy('id', 'DESC')->get();
                            $serviceExitDistinct = [];
                            foreach($serviceExit as $item) {
                                if (!array_key_exists($item->service_id, $serviceExitDistinct)) {
                                    $serviceExitDistinct[$item->service_id] = $item;
                                }
                            }

                            $order->payment_status = $statusPayment;
                            $order->payment_date = Date('Y/m/d');
                            $order->payment_transaction = $vnpTranId;
                            $order->payment_response_code = '00';
                            $order->save();

                            $orderDetail = $order->orderDetail;
                            $companyServiceData = [];
                            foreach ($orderDetail as $item) {
                                $day = $item->pivot->used_time * $item->pivot->count;
                                if (array_key_exists($item->pivot->service_id, $serviceExitDistinct)) {
                                    $date = $serviceExitDistinct[$item->pivot->service_id]['expiration_date'];
                                    $expirationDate = Date('Y/m/d', strtotime($date . '+' . $day . ' days'));
                                } else {
                                    $expirationDate = Date('Y/m/d', strtotime('+' . $day . ' days'));
                                }

                                $companyServiceData[] = [
                                    'company_id' => Auth::guard('api-user')->user()->company[0]['id'],
                                    'service_id' => $item->pivot->service_id,
                                    'expiration_date' => $expirationDate,
                                ];
                            }

                            CompanyService::insert($companyServiceData);

                            DB::commit();
                                   
                            $returnData['RspCode'] = '00';
                            $returnData['Message'] = 'Confirm Success';
                        } else {
                            $returnData['RspCode'] = '02';
                            $returnData['Message'] = 'Order already confirmed';
                        }
                    }
                    else {
                        $returnData['RspCode'] = '04';
                        $returnData['Message'] = 'invalid amount';
                    }
                } else {
                    $returnData['RspCode'] = '01';
                    $returnData['Message'] = 'Order not found';
                }
            } else {
                $returnData['RspCode'] = '97';
                $returnData['Message'] = 'Invalid signature';
            }
        } catch (\Exception $e) {
            $returnData['RspCode1'] = $e->getMessage();
            $returnData['RspCode'] = '99';
            $returnData['Message'] = 'Unknow error';
        }

        //Trả lại VNPAY theo định dạng JSON
        return json_encode($returnData);
    }

}