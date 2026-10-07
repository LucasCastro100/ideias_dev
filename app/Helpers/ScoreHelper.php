<?php

namespace App\Helpers;

class ScoreHelper
{
    const MAX_PER_MODALITY = 500;
    const MAX_TOTAL = 2000;
    const DECIMALS = 2;

    public static function roundScore(float $value): float
    {
        return round($value, self::DECIMALS);
    }

    public static function normalizeModalityScore(float $score): float
    {
        return self::roundScore(min(self::MAX_PER_MODALITY, max(0, $score)));
    }

    public static function normalizeTotalScore(float $score): float
    {
        return self::roundScore(min(self::MAX_TOTAL, max(0, $score)));
    }

    public static function calculateTotalScore(float $nonDpTotal, float $bestDp): float
    {
        return self::normalizeTotalScore($nonDpTotal + $bestDp);
    }

    public static function calculateModalityTotal(array $scores, string $level = 'basic', float $scorePerQuestion = 0): float
    {
        $sumTotal = array_sum(array_map('intval', $scores));

        if ($level === 'basic') {
            return self::normalizeModalityScore($sumTotal);
        }

        $totalCalculado = $scorePerQuestion * $sumTotal;
        return self::normalizeModalityScore($totalCalculado);
    }

    public static function calculateDpTotal(array $roundScores): float
    {
        return self::normalizeModalityScore(round(array_sum($roundScores)));
    }

    public static function floatVal(mixed $value): float
    {
        return (float) $value;
    }
}
