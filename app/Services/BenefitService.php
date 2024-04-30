<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class BenefitService
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

    public function getBenefits()
    {
        return $this->benefits->map(function ($value, $key) {
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
        });
    }

    public function groupByYear(Collection $data)
    {
        return $data->groupBy(function ($item) {
            $year = date('Y', strtotime($item['fecha']));

            return $year;
        });
    }

    public function filterBenefits(Collection $data)
    {
        $res = collect();

        $data->each(function ($value, $key) use ($res) {
            $filtered = $value->filter(function ($benefit) {
                $filter = $this->filters->first(function ($value) use ($benefit) {
                    return $value['id_programa'] === $benefit['id_programa'];
                });

                return $benefit['monto'] >= $filter['min'] && $benefit['monto'] <= $filter['max'];
            });

            $res[] = [
                'year' => $key,
                'num' => count($filtered),
                'beneficios' => $filtered->values(),
            ];
        });

        return $res;
    }
}
