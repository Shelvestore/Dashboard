<?php

namespace App\Http\Controllers\API\Admin;

use App\Contract\Admin\OrderInterface;
use App\Http\Controllers\Controller as Controller;
use App\Http\Requests\OrderUpdateRequest;
use App\Models\Web\Order;
use App\Repository\Admin\OrderRepository;

class OrderController extends Controller
{
    private $orderRepository;

    public function __construct(OrderInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function index()
    {
        return $this->orderRepository->all();
    }

    public function show(Order $Order)
    {
        return $this->orderRepository->show($Order);
    }

    public function update(OrderUpdateRequest $request, Order $Order)
    {
        $parms = $request->all();
        return $this->orderRepository->update($parms, $Order);
    }

}
