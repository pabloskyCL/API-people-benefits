<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

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

    public function index() {

        return [$this->benefits,$this->filters,$this->dataSheet];
    }
}