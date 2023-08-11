<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/invoices/order-dates",
     *      summary="Get unique order dates from invoices",
     *      tags={"Invoices"},
     *      @OA\Response(
     *          response=200,
     *          description="Successful response with unique order dates",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="string",
     *                  example="2023-01"
     *              )
     *          )
     *      )
     * )
     */
    public function getUniqueOrderDates()
    {
        $uniqueDates = Invoice::distinct('data_emissao')->pluck('data_emissao');

        $uniqueDates = Invoice::distinct('data_emissao')->pluck('data_emissao')->map(function ($date) {
            return \Carbon\Carbon::parse($date)->format('Y-m');
        });

        $uniqueDates = array_values(array_unique($uniqueDates->toArray()));

        return response()->json($uniqueDates);
    }
}
