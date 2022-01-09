<?php

namespace App\Http\Controllers\API\Web;

use App\Contract\Web\OrderInterface;
use App\Http\Controllers\Controller as Controller;
use App\Http\Requests\OrderRequest;
use App\Models\Web\Order;
use App\Repository\Web\OrderRepository;
use Illuminate\Http\Request;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use Stripe\Error\Card;

class OrderController extends Controller
{
    private $OrderRepository;

    public function __construct(OrderInterface $OrderRepository)
    {
        $this->OrderRepository = $OrderRepository;
    }

    public function store(OrderRequest $request)
    {
        $parms = $request->all();
        return $this->OrderRepository->store($parms);
    }
}
