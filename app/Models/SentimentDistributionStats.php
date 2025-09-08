<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SentimentDistributionStats extends Model
{
    protected $table = 'sentiment_full_report';
    public $timestamps = false;
    
    protected $fillable = [
        'bulan',
        'sentimen',
        'periode',
        'comment',
        'link_konten',
        'is_spam',
        'created_at',
        'total_comments_month',
        'positive_count_month',
        'neutral_count_month', 
        'negative_count_month',
        'first_period_month',
        'last_period_month'
    ];
    
    // Get summary data untuk header report
    public function getSummaryData($bulan = null)
    {
        $query = self::query();
        
        if ($bulan && $bulan !== 'all') {
            $query->where('bulan', $bulan);
        }
        
        $data = $query->first();
        
        if (!$data) return null;
        
        $total = $bulan === 'all' ? self::count() : $data->total_comments_month;
        $positive = $bulan === 'all' ? self::where('sentimen', 'Positif')->count() : $data->positive_count_month;
        $neutral = $bulan === 'all' ? self::where('sentimen', 'Netral')->count() : $data->neutral_count_month;
        $negative = $bulan === 'all' ? self::where('sentimen', 'Negatif')->count() : $data->negative_count_month;
        
        return [
            'bulan' => $bulan === 'all' ? 'Semua Bulan' : $data->bulan,
            'total_comments' => $total,
            'positive_count' => $positive,
            'neutral_count' => $neutral,
            'negative_count' => $negative,
            'positive_percentage' => $total > 0 ? round(($positive / $total) * 100, 1) : 0,
            'neutral_percentage' => $total > 0 ? round(($neutral / $total) * 100, 1) : 0,
            'negative_percentage' => $total > 0 ? round(($negative / $total) * 100, 1) : 0,
            'period_range' => $bulan === 'all' ? 'Seluruh Periode' : $data->first_period_month . ' - ' . $data->last_period_month,
            'dominant_sentiment' => $positive >= $neutral && $positive >= $negative ? 'Positif' : 
                                  ($neutral >= $positive && $neutral >= $negative ? 'Netral' : 'Negatif')
        ];
    }}
