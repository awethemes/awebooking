<?php

namespace AweBooking\PMS\Repositories;

use ArtisUs\Contracts\ReportRepository;
use ArtisUs\Models\ArtistCommission;
use ArtisUs\System\Database\Connection as DB;
use ArtisUs\System\DateTime;

class EloquentReportRepository implements ReportRepository
{
	/**
	 * {@inheritdoc}
	 */
	public function getLifetimeArtistCommissions($artistId): float
	{
		return (float) ArtistCommission::query()->where('artist_id', $artistId)->sum('profit');
	}

	/**
	 * {@inheritdoc}
	 */
	public function queryArtworkSalesByArtist($artistId)
	{
		$prefix = DB::getInstance()->getTablePrefix();

		return DB::getInstance()
			->table('wc_order_stats')
			->join('wc_order_product_lookup', 'wc_order_product_lookup.order_id', '=', 'wc_order_stats.order_id')
			->leftJoin('art_commissions', 'art_commissions.order_item_id', '=', 'wc_order_product_lookup.order_item_id')
			->select('wc_order_stats.order_id', 'wc_order_stats.status', 'wc_order_stats.date_created')
			->selectRaw("SUM({$prefix}wc_order_product_lookup.product_qty) as num_items_sold")
			->selectRaw("SUM({$prefix}wc_order_product_lookup.product_gross_revenue) as total_sales")
			->selectRaw("SUM({$prefix}wc_order_product_lookup.product_net_revenue) as net_total")
			->selectRaw("SUM({$prefix}art_commissions.profit) as commissions")
			->whereNotIn('wc_order_stats.status', ['wc-trash', 'wc-pending', 'wc-failed', 'wc-cancelled'])
			->where('art_commissions.artist_id', $artistId)
			->groupBy('wc_order_stats.order_id')
			->orderByDesc('wc_order_stats.date_created')
			->paginate(15, ['*'], 'current-page');
	}

	/**
	 * {@inheritdoc}
	 */
	public function getArtistThisMonthRevenue($artistId)
	{
		$prefix = DB::getInstance()->getTablePrefix();

		return DB::getInstance()
			->table('wc_order_product_lookup')
			->join('wc_order_stats', 'wc_order_stats.order_id', '=', 'wc_order_product_lookup.order_id')
			->leftJoin('art_commissions', 'art_commissions.order_item_id', '=', 'wc_order_product_lookup.order_item_id')
			->selectRaw("COUNT(DISTINCT {$prefix}wc_order_product_lookup.order_id) as total_order")
			->selectRaw("SUM({$prefix}wc_order_product_lookup.product_gross_revenue) as total_sales")
			->selectRaw("SUM({$prefix}wc_order_product_lookup.product_net_revenue) as net_total") // net_revenue
			->selectRaw("SUM({$prefix}wc_order_product_lookup.product_qty) as total_items_sold")
			->selectRaw("SUM({$prefix}art_commissions.profit) as commissions")
			->whereNotIn('wc_order_stats.status', ['wc-trash', 'wc-pending', 'wc-failed', 'wc-cancelled'])
			->where('art_commissions.artist_id', $artistId)
			->where('wc_order_stats.date_created', '>=', DateTime::now()->startOfMonth())
			->where('wc_order_stats.date_created', '<=', DateTime::now()->endOfMonth())
			->first();
	}
}
