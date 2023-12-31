<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Customer;

class CustomersController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/customers",
     *      summary="Lista de clientes ativos",
     *      tags={"Clientes"},
     *      @OA\Response(
     *          response=200,
     *          description="Sucesso",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/CustomerResource")
     *          ),
     *      ),
     * )
     */

    public function showCustomers()
    {
        $customers = $this->getActiveCustomers()->get();
        return CustomerResource::collection($customers);
    }

    /**
     * @OA\Get(
     *      path="/api/customers/net-revenue",
     *      summary="Receita líquida dos clientes",
     *      tags={"Clientes"},
     *      @OA\Parameter(
     *          name="customers",
     *          in="query",
     *          description="IDs dos clientes",
     *          @OA\Schema(type="array", @OA\Items(type="integer"))
     *      ),
     *      @OA\Parameter(
     *          name="start_at",
     *          in="query",
     *          description="Data de início (formato: YYYY-MM-DD)",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="end_at",
     *          in="query",
     *          description="Data de término (formato: YYYY-MM-DD)",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Sucesso",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  @OA\Property(property="customer_id", type="integer"),
     *                  @OA\Property(property="customer_name", type="string"),
     *                  @OA\Property(property="months", type="object",
     *                      @OA\AdditionalProperties(
     *                          @OA\Property(property="net_revenue", type="string")
     *                      )
     *                  ),
     *              )
     *          ),
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Erro de validação",
     *      ),
     * )
     */


     public function showNetRevenue(Request $request)
     {
         $customerIds = $request->input('customers');
         $startAt = $request->input('start_at');
         $endAt = $request->input('end_at');

         if ($startAt !== null) {
            $startAt = Carbon::parse($startAt)->firstOfMonth()->format('Y-m-d');
        }

        if ($endAt !== null) {
            $endAt = Carbon::parse($endAt)->lastOfMonth()->format('Y-m-d');
        }

         $customersQuery = $this->getActiveCustomers();

         if (!empty($customerIds)) {
             $customersQuery->whereIn('co_cliente', $customerIds);
         }

         $customers = $customersQuery->with(['invoices' => function ($query) use ($startAt, $endAt) {
             if ($startAt !== null && $endAt !== null) {
                 $query->whereBetween('data_emissao', [$startAt, $endAt]);
             }
         }])->get();

         $result = [];

         foreach ($customers as $customer) {
             $customerData = [
                 'customer_id' => $customer->co_cliente,
                 'customer_name' => $customer->no_fantasia,
                 'months' => []
             ];

             $startDate = Carbon::parse($startAt);
             $endDate = Carbon::parse($endAt);

             while ($startDate->lte($endDate)) {
                 $month = $startDate->format('Y-m');

                 $netRevenue = $this->calculateNetRevenueForMonth($customer, $startDate);

                 $customerData['months'][$month] = [
                     'net_revenue' => number_format($netRevenue, 2, ',', '.'),
                 ];

                 $startDate->addMonth();
             }

             $result[] = $customerData;
         }

         return response()->json($result);
     }

     private function calculateNetRevenueForMonth($customer, $date)
     {
         $invoices = $customer->invoices->filter(function ($invoice) use ($date) {
             return Carbon::parse($invoice->data_emissao)->format('Y-m') === $date->format('Y-m');
         });

         /* if ($invoices->isEmpty()) {
             return 0;
         } */

         $taxes = ($invoices->sum('valor') * ($invoices->sum('total_imp_inc') / 100));
         return $invoices->sum('valor') - $taxes;
     }

    private function getActiveCustomers()
    {
        return Customer::where('tp_cliente', '=', 'A');
    }

    private function calculateNetRevenue($customer)
    {
        $invoices = $customer->invoices->filter(function ($invoice) {
            return Carbon::parse($invoice->data_emissao);
        });

        if ($invoices->isEmpty()) {
            return 0;
        }

        $taxes = ($invoices->sum('valor') * ($invoices->sum('total_imp_inc') / 100));
        return $invoices->sum('valor') - $taxes;
    }
}
