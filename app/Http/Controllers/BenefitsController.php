<?php

namespace App\Http\Controllers;

use App\Services\BenefitService;
use Illuminate\Support\Facades\Http;

/**
 * @OA\Info(
 *             title="Beneficios agrupados por año, ya filtrados",
 *             version="1.0",
 *             description="Prueba tecnica, consumo de end points: benficios, filtros y fichas. Se filtra cada beneficio segun su filtro asociado, para agruparlos por año"
 * )
 *
 * @OA\Server(url="http://localhost")
 */
class BenefitsController extends Controller
{
    private $benefitServices;

    public function __construct(BenefitService $benefitServices)
    {
        $this->benefitServices = $benefitServices;
    }

    /**
     * Beneficios filtrados y con su ficha, agrupados por año.
     *
     * @OA\Get(
     *     path="/yearBenefits",
     *     tags={"Beneficios"},
     *     summary="Filtra los beneficios y se les asigna una ficha, agrupando los beneficios por año.",
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="code",
     *                 type="integer",
     *                 example=200
     *             ),
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *
     *                 @OA\Items(
     *
     *                     @OA\Property(
     *                         property="year",
     *                         type="integer",
     *                         example=2023
     *                     ),
     *                     @OA\Property(
     *                         property="num",
     *                         type="integer",
     *                         example=8
     *                     ),
     *                     @OA\Property(
     *                         property="beneficios",
     *                         type="array",
     *
     *                         @OA\Items(
     *
     *                             @OA\Property(
     *                                 property="id_programa",
     *                                 type="integer",
     *                                 example=147
     *                             ),
     *                             @OA\Property(
     *                                 property="monto",
     *                                 type="integer",
     *                                 example=40656
     *                             ),
     *                             @OA\Property(
     *                                 property="fecha_recepcion",
     *                                 type="string",
     *                                 example="09/11/2023"
     *                             ),
     *                             @OA\Property(
     *                                 property="fecha",
     *                                 type="string",
     *                                 example="2023-11-09"
     *                             ),
     *                             @OA\Property(
     *                                 property="ano",
     *                                 type="string",
     *                                 example="2023"
     *                             ),
     *                             @OA\Property(
     *                                 property="ficha",
     *                                 type="object",
     *                                 @OA\Property(
     *                                     property="id",
     *                                     type="integer",
     *                                     example=922
     *                                 ),
     *                                 @OA\Property(
     *                                     property="nombre",
     *                                     type="string",
     *                                     example="Emprende"
     *                                 ),
     *                                 @OA\Property(
     *                                     property="url",
     *                                     type="string",
     *                                     example="emprende"
     *                                 ),
     *                                 @OA\Property(
     *                                     property="categoria",
     *                                     type="string",
     *                                     example="trabajo"
     *                                 ),
     *                                 @OA\Property(
     *                                     property="descripcion",
     *                                     type="string",
     *                                     example="Fondos concursables para nuevos negocios"
     *                                 )
     *                             )
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function yearBenefits()
    {
        $data = $this->benefitServices->groupByYear($this->benefitServices->getBenefits());

        return [
            'code' => 200,
            'success' => true,
            'data' => $this->benefitServices->filterBenefits($data),
        ];
    }
}
