<?php

namespace App\Http\Controllers;

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
    private $benefits;

    private $filters;

    private $dataSheet;

    public function __construct()
    {
        $benefitsRes = Http::get('https://run.mocky.io/v3/399b4ce1-5f6e-4983-a9e8-e3fa39e1ea71')->json();
        $this->benefits = collect($benefitsRes['data']);

        $filtersRes = Http::get('https://run.mocky.io/v3/06b8dd68-7d6d-4857-85ff-b58e204acbf4')->json();
        $this->filters = collect($filtersRes['data']);

        $dataSheetRes = Http::get('https://run.mocky.io/v3/c7a4777f-e383-4122-8a89-70f29a6830c0')->json();
        $this->dataSheet = collect($dataSheetRes['data']);
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
        $data = collect();

        $this->benefits->map(function ($value, $key) {
            $year = date('Y', strtotime($value['fecha']));
            $value['ano'] = $year;
            $filter = $this->filters->filter(function ($filter, $key) use ($value) {
                return $filter['id_programa'] == $value['id_programa'];
            });

            $dataSheet = $this->dataSheet->filter(function ($ds, $key) use ($filter) {
                return $ds['id'] == $filter->first()['ficha_id'];
            });

            $value['ficha'] = (object) $dataSheet->values()[0];

            return $value;
        })->groupBy(function ($item) {
            $year = date('Y', strtotime($item['fecha']));

            return $year;
        })->each(function ($value, $key) use ($data) {
            $filtered = $value->filter(function ($benefit) {
                $filter = $this->filters->first(function ($value) use ($benefit) {
                    return $value['id_programa'] === $benefit['id_programa'];
                });

                return $benefit['monto'] >= $filter['min'] && $benefit['monto'] <= $filter['max'];
            });

            $data[] = [
                'year' => $key,
                'num' => count($filtered),
                'beneficios' => $filtered->values(),
            ];
        });

        return [
            'code' => 200,
            'success' => true,
            'data' => $data,
        ];
    }
}
