<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerResource;
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
     *                  @OA\Property(property="net_revenue", type="string"),
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

        $customersQuery = $this->getActiveCustomers();

        if (!empty($customerIds)) {
            $customersQuery->whereIn('co_cliente', $customerIds);
        }

        $customers = $customersQuery->with(['invoices' => function ($query) use ($startAt, $endAt) {
            if ($startAt !== null && $endAt !== null) {
                $query->whereBetween('data_emissao', [$startAt, $endAt]);
            }
        }])->get();

        $result = $customers->map(function ($customer) use ($startAt, $endAt) {
            $netRevenue = $this->calculateNetRevenue($customer);

            return [
                'customer_id' => $customer->co_cliente,
                'customer_name' => $customer->no_fantasia,
                'net_revenue' => number_format($netRevenue, 2, '.', ','),
            ];
        });

        return response()->json($result);
    }

    private function getActiveCustomers()
    {
        return Customer::where('tp_cliente', '=', 'A');
    }

    private function calculateNetRevenue($customer)
    {
        $taxes = ($customer->invoices->sum('valor') * ($customer->invoices->sum('total_imp_inc') / 100));
        return $customer->invoices->sum('valor') - $taxes;
    }
}
