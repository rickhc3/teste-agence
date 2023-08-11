<?php

namespace App\Http\Controllers;

use App\Http\Resources\ConsultantResource;
use Illuminate\Http\Request;
use App\Models\Consultant;

class ConsultantsController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/consultants",
     *      summary="Lista de consultores ativos",
     *      tags={"Consultores"},
     *      @OA\Response(
     *          response=200,
     *          description="Sucesso",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/ConsultantResource")
     *          ),
     *      ),
     * )
     */

    public function showConsultants()
    {
        $consultants = $this->getActiveConsultantsWithPermissions()->get();
        return ConsultantResource::collection($consultants);
    }

    /**
     * @OA\Get(
     *      path="/api/consultants/net-revenue",
     *      summary="Receita líquida e mais informações sobre receitas dos consultores",
     *      tags={"Consultores"},
     *      @OA\Parameter(
     *          name="users[]",
     *          in="query",
     *          description="Nomes de usuários dos consultores",
     *          @OA\Schema(type="array", @OA\Items(type="string"))
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
     *                  @OA\Property(property="co_usuario", type="string"),
     *                  @OA\Property(property="no_usuario", type="string"),
     *                  @OA\Property(property="net_revenue", type="string"),
     *                  @OA\Property(property="brut_salario", type="string"),
     *                  @OA\Property(property="comission", type="string"),
     *                  @OA\Property(property="profit", type="string"),
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
        $userIds = $request->input('users');
        $startAt = $request->input('start_at');
        $endAt = $request->input('end_at');

        $consultantsQuery = $this->getActiveConsultantsWithPermissions();

        if (!empty($userIds)) {
            $consultantsQuery->whereIn('co_usuario', $userIds);
        }

        $consultants = $consultantsQuery->with(['orderServices.invoices' => function ($query) use ($startAt, $endAt) {
            if ($startAt !== null && $endAt !== null) {
                $query->whereBetween('data_emissao', [$startAt, $endAt]);
            }
        }, 'salary'])->get();

        $result = $consultants->map(function ($consultant) use ($startAt, $endAt) {
            $netRevenue = $this->calculateNetRevenue($consultant);

            $fixedCost = $consultant->salary ? $consultant->salary->brut_salario : 0;

            $comission = $this->calculateComission($consultant);

            $profit = $netRevenue - ($fixedCost + $comission);

            return [
                'co_usuario' => $consultant->co_usuario,
                'no_usuario' => $consultant->no_usuario,
                'net_revenue' => number_format($netRevenue, 2, '.', ','),
                'brut_salario' => number_format($fixedCost, 2, '.', ','),
                'comission' => number_format($comission, 2, '.', ','),
                'profit' => number_format($profit, 2, '.', ','),
            ];
        });

        return response()->json($result);
    }

    private function getActiveConsultantsWithPermissions()
    {
        return Consultant::whereHas('permissions', function ($query) {
            $query->where('co_sistema', 1)
                ->where('in_ativo', 'S')
                ->whereIn('co_tipo_usuario', [0, 1, 2]);
        });
    }

    private function calculateNetRevenue($consultant)
    {
        return $consultant->orderServices->sum(function ($orderService) {
            $taxes = ($orderService->invoices->sum('valor') * ($orderService->invoices->sum('total_imp_inc') / 100));
            return $orderService->invoices->sum('valor') - $taxes;
        });
    }

    private function calculateComission($consultant)
    {
        return $consultant->orderServices->sum(function ($orderService) {
            $taxes = ($orderService->invoices->sum('valor') * ($orderService->invoices->sum('total_imp_inc') / 100));
            $percentageComission = $orderService->invoices->sum('comissao_cn') / 100;
            $total = $orderService->invoices->sum('valor') - $taxes;
            return $total * $percentageComission;
        });
    }
}
