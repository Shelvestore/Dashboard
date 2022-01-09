<?php

namespace App\Repository\Web;

use App\Contract\Web\CompareInterface;
use App\Http\Resources\Web\Compare as CompareResource;
use App\Models\Web\Compare;
use App\Traits\ApiResponser;
use Auth;
use Illuminate\Support\Collection;

class CompareRepository implements CompareInterface
{
    use ApiResponser;
    public function all()
    {
        try {
            if (isset($_GET['limit']) && is_numeric($_GET['limit']) && $_GET['limit'] > 0) {
                $numOfResult = $_GET['limit'];
            } else {
                $numOfResult = 100;
            }
            
            return $this->successResponse(CompareResource::collection(Compare::customerId(Auth::id())->paginate($numOfResult)) , 'Data Get Successfully!');
        } catch (Exception $e) {
            return $this->errorResponse();
        }
    }

    public function show($compare)
    {
        try {
            return $this->successResponse(new CompareResource(Compare::CompareId($compare->id)->firstOrFail()) , 'Data Get Successfully!');
        } catch (Exception $e) {
            return $this->errorResponse();
        }
    }

    public function store(array $parms)
    {
        try {
            $sql = new Compare;
            $sql = $sql->create($parms);
        } catch (Exception $e) {
            return $this->errorResponse();
        }
        if ($sql) {
            return $this->successResponse(new CompareResource($sql), 'Compare Save Successfully!');
        } else {
            return $this->errorResponse();
        }
    }

    public function destroy($compare)
    {
        try {
            $sql = Compare::findOrFail($compare);
            $sql->delete();
        } catch (Exception $e) {
            return $this->errorResponse();
        }
        if ($sql) {
            return $this->successResponse('', 'Compare Delete Successfully!');
        } else {
            return $this->errorResponse();
        }
    }

}
