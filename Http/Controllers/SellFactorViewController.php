<?php

namespace Modules\SellFactor\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\SellFactor\Entities\SellFactor;
use function optional;
use function verta;
use function view;


class SellFactorViewController extends Controller
{
    protected $dataCondition;

    /**
     * WareHouseController constructor.
     */
    public function __construct ()
    {
        $this->middleware('can:sales_invoice')->except(['search']);
        $this->middleware(function ($request, $next) {
            $user_id = Auth::user()->id;
            $this->dataCondition = getActiveCompanyUserConditions($user_id);
            return $next($request);
        });
    }

    /**
     * @return Factory|View|Application
     */
    public function index (): Factory|View|Application
    {
        return view('sellfactor::index');
    }

    /**
     * @param $buy_factor
     * @return Factory|View|Application
     */
    public function show ($buy_factor): Factory|View|Application
    {
        $factors = SellFactor::MyCompany($this->dataCondition)->where('factor_id', $buy_factor)->with('personal')
            ->firstOrFail();
        return view('sellfactor::show', compact('factors'));
    }

    /**
     * @return Factory|View|Application
     */
    public function removeFactor (): Factory|View|Application
    {
        return view('sellfactor::remove');
    }

    /**
     * @param $buy_factor
     * @return Factory|View|Application
     */
    public function showFactor ($buy_factor): Factory|View|Application
    {
        $factor = SellFactor::MyCompany($this->dataCondition)->where('factor_id', $buy_factor)
            ->with('personal')->firstOrFail();
        return view('sellfactor::factor.show', compact('factor'));
    }

    /**
     * @param Request $request
     * @return array
     */
    public function search (Request $request): array
    {
        $data = [];
        $personals = SellFactor::MyCompany($this->dataCondition)
            ->where('factor_id', 'like', '%' . $request->name . '%')->get(['factor_id', 'created_at', 'personal_id']);
        foreach ($personals as $personal) {
            $data[] = [
                'id'   => $personal->factor_id,
                'text' => ' فاکتور فروش  ' . $personal->factor_id .
                    ' - ' . optional($personal->personal)->typeSearchRemittance() . ' - ' . verta($personal->created_at)->format('Y/m/d')
            ];
        }
        return $data;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getFactor (Request $request): JsonResponse
    {
        $draw = $request->get('draw');
        $searchValue = $request->get('search.value');
        // Total records
        $totalRecords = SellFactor::MyCompany($this->dataCondition)->where('status', '!=', 'draft')->count();

        $totalRecordswithFilter = SellFactor::MyCompany($this->dataCondition)
            ->where('factor_id', 'like', '%' . $searchValue . '%')->where('status', '!=', 'draft')->count();
        // Fetch records
        $records = SellFactor::orderBy('factor_id', 'desc')
            ->MyCompany($this->dataCondition)
            ->where('status', '!=', 'draft')
            ->where('factor_id', 'like', '%' . $searchValue . '%')
            ->select(['factor_id', 'personal_id', 'created_at', 'action_date', 'id', 'status'])
            ->with(['personal', 'factorRemittances'])
            ->skip($request->input('start'))
            ->take($request->input('length'))->get();
        $data_arr = $records->map(function ($record) {
            return [
                "factor_id"   => $record->factor_id,
                "name"        => $record->personal->typeSearchRemittance(),
                "action_date" => verta($record->action_date)->format('Y/m/d'),
                "status"      => $record->status(),
                "action"      => $record->action(),
            ];
        });
        $response = [
            "draw"                 => intval($draw),
            "iTotalRecords"        => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData"               => $data_arr,
        ];
        return response()->json($response);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getFactorRemove (Request $request): JsonResponse
    {
        $draw = $request->get('draw');
        $searchValue = $request->get('search.value');
        // Total records
        $totalRecords = SellFactor::MyCompany($this->dataCondition)->where('status', 'draft')->count();
        $totalRecordswithFilter = SellFactor::MyCompany($this->dataCondition)
            ->where('factor_id', 'like', '%' . $searchValue . '%')->where('status', 'draft')->count();
        // Fetch records
        $records = SellFactor::orderBy('factor_id', 'desc')
            ->MyCompany($this->dataCondition)
            ->where('status', 'draft')
            ->where('factor_id', 'like', '%' . $searchValue . '%')
            ->select(['factor_id', 'personal_id', 'created_at', 'action_date', 'id', 'status'])
            ->with(['personal', 'factorRemittances'])
            ->skip($request->input('start'))
            ->take($request->input('length'))->get();
        $data_arr = $records->map(function ($record) {
            return [
                "factor_id"   => $record->factor_id,
                "name"        => $record->personal->typeSearchRemittance(),
                "action_date" => verta($record->action_date)->format('Y/m/d'),
                "status"      => $record->status(),
                "action"      => $record->action(),
            ];
        });
        $response = [
            "draw"                 => intval($draw),
            "iTotalRecords"        => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData"               => $data_arr,
        ];
        return response()->json($response);
    }

}
