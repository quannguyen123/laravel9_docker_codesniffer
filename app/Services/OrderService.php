<?php

namespace App\Services;

use App\Models\Order;
use App\Models\TagSuggest;
use App\Repositories\OrderDetailRepository;
use App\Repositories\OrderRepository;
use App\Repositories\TagRepository;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderService {
    /**
     * function contructer
     *
     * @param TagRepository $repository
     */
    public function __construct(
        protected readonly OrderRepository $orderRepository,
        protected readonly OrderDetailRepository $orderDetailRepository
    ) {
    }

    public function index() {
        $order = $this->orderRepository
                    ->with(['orderDetail'])
                    ->findWhere(['company_id' => Auth::guard('api-user')->user()->company[0]['id']]);

        return [true, $order];
    }

    public function store() {
        $countService = Cart::count();
        if ($countService == 0) {
            return [false, [], 'Giỏ hàng không tồn tại'];
        }
        
        $order = [
            'total' => Cart::total(0, 0, ''),
            'user_id' => Auth::guard('api-user')->user()->id,
            'company_id' => Auth::guard('api-user')->user()->company[0]['id'],
            'payment_status' => config('custom.status-payment.unpaid'),
            'created_by' => Auth::guard('api-user')->user()->id
        ];
        
        DB::beginTransaction();
        $orderTemp = $this->orderRepository->create($order);
        
        $cart = Cart::content();
        foreach ($cart as $item) {
            $order_detail = [
                'order_id' => $orderTemp->id,
                'service_id' => $item->id,
                'price' => $item->price,
                'count' => $item->qty,
                'total' => $item->subtotal,
                'used_time' => $item->options->used_time,
            ];

            $this->orderDetailRepository->create($order_detail);
        }
        DB::commit();
        
        Cart::destroy();
       
        $order = $this->orderRepository->with(['orderDetail'])->find($orderTemp->id);

        return [true, $order, 'Tạo đơn hàng thành công'];
    }

    public function orderInfo($id) {
        $order = $this->orderRepository
        ->with(['orderDetail'])
        ->findWhere([
            'id' => $id,
            'company_id' => Auth::guard('api-user')->user()->company[0]['id'],
        ]);

        return [true, $order];
    }

    public function adminIndex($request) {
        $orders = $this->orderRepository->getOrder($request);

        return [true, $orders, 'Success'];
    }

    public function adminDetail($order) {
        $order->orderDetail;
        $order->company;
        
        return [true, $order, 'Success'];
    }
}