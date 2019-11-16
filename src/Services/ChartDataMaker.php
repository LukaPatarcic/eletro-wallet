<?php


namespace App\Services;


class ChartDataMaker
{
    public function getAllTypeData($datas): array
    {
        $incomeCount = 0;
        $outcomeCount = 0;
        foreach ($datas as $data) {
            if($data['type'] == 'income') {
                $incomeAmounts[]= $data['amount'];
                $incomeNames[] = $data['name'];
                $incomeCount = $incomeCount+$data['number'];
                continue;
            }
            $outcomeAmounts[]= $data['amount'];
            $outcomeNames[] = $data['name'];
            $outcomeCount = $outcomeCount+$data['number'];
        }
        return [
            'income' => ['names' => $incomeNames, 'amounts' => $incomeAmounts,'number' => $incomeCount],
            'outcome' => ['names' => $outcomeNames, 'amounts' => $outcomeAmounts,'number' => $outcomeCount],
        ];
    }

    public function getOneTypeData($datas): array
    {
        $count = 0;
        foreach ($datas as $data) {
            $amounts[]= $data['amount'];
            $names[] = $data['name'];
            $count = $count + $data['number'];
        }
        return [
            'names' => $names,
            'amounts' => $amounts,
            'number' => $count,
        ];
    }

    public function formatMonthData($data): array
    {
        foreach ($data as $month) {
            foreach ($month as $value) {
                $monthNames[$value] = date("F",strtotime ("2001-".$value."-01"));
            }
        }
        return $monthNames;
    }

    public function getComparisonChartData($data1,$data2): array
    {
//        dd($data1, $data2);
        $count1 = 0;
        $count2 = 0;

        foreach ($data1 as $first) {
            $label1[] = date('d', strtotime($first['groupByDate']));
            $amount1[] = $first['amount'];
            $count1 = $count1 + $first['number'];
        }

        foreach ($data2 as $second) {
            $label2[] = date('d', strtotime($second['groupByDate']));
            $amount2[] = $second['amount'];
            $count2 = $count2 + $second['number'];
        }

        $label = array_intersect_key($label1,$label2);
//        $label = array_unique(array_merge($label1,$label2));


        return [
            'label' => $label,
            'amount1' => $amount1,
            'amount2' => $amount2,
            'number1' => $count1,
            'number2' => $count2,
        ];
    }



}