<?php

namespace AweBooking\PMS\REST\Controllers;

use ArtisUs\Jobs\CreateArtSubmissionProduct;
use ArtisUs\Models\Artist;
use ArtisUs\Models\ArtSubmission;
use ArtisUs\Models\ArtSubmissionPrintSize;
use ArtisUs\Models\Collections\PrintSizeCollection;
use ArtisUs\Models\PrintSizeTemplateGroup;
use ArtisUs\Product\PrintSize;
use ArtisUs\Product\ProductGenerator;
use ArtisUs\Product\RuntimeProductTypeManager;
use ArtisUs\REST\RESTController;
use ArtisUs\System\Database\Connection as DB;
use ArtisUs\Vendor\Illuminate\Support\Collection;
use Exception;
use WC_Product_Variation;
use WP_Error;
use WP_REST_Request;
use WP_REST_Server;
use Symfony\Component\Validator\Constraints;

class ArtSubmissionsController extends RESTController
{
    /**
     * @var string
     */
    protected $rest_base = 'art-submissions';

    /**
     * {@inheritdoc}
     */
    public function register_routes()
    {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            [
                [
                    'methods' => WP_REST_Server::EDITABLE,
                    'callback' => [$this, 'submitArt'],
                    'permission_callback' => $this->currentArtistCan('submit_arts'),
                ],
                'schema' => [$this, 'get_public_item_schema'],
            ]
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/create',
            [
                [
                    'methods' => WP_REST_Server::EDITABLE,
                    'callback' => [$this, 'createNewArt'],
                    'permission_callback' => $this->currentUserCanManage('art_submissions'),
                ],
                'schema' => [$this, 'get_public_item_schema'],
            ]
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/(?P<artwork>[\d]+)/create-print-size',
            [
                [
                    'methods' => WP_REST_Server::EDITABLE,
                    'callback' => [$this, 'createPrintSize'],
                    'permission_callback' => $this->currentUserCanManage('art_submissions'),
                ],
                'schema' => [$this, 'get_public_item_schema'],
            ]
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/(?P<artwork>[\d]+)/add-print-size-from-group',
            [
                [
                    'methods' => WP_REST_Server::EDITABLE,
                    'callback' => [$this, 'addPrintSizeFromGroup'],
                    'permission_callback' => $this->currentUserCanManage('art_submissions'),
                ],
                'schema' => [$this, 'get_public_item_schema'],
            ]
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/(?P<artSubmission>[\d]+)/assign-print-sizes',
            [
                [
                    'methods' => WP_REST_Server::EDITABLE,
                    'callback' => [$this, 'assignPrintSizes'],
                    'permission_callback' => $this->currentUserCanManage('art_submissions'),
                ],
                'schema' => [$this, 'get_public_item_schema'],
            ]
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/(?P<artwork>[\d]+)/update-pricing',
            [
                [
                    'methods' => WP_REST_Server::EDITABLE,
                    'callback' => [$this, 'updatePricing'],
                    'permission_callback' => $this->currentUserCanManage('art_submissions'),
                ],
                'schema' => [$this, 'get_public_item_schema'],
            ]
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/(?P<artSubmission>[\d]+)/approve',
            [
                [
                    'methods' => WP_REST_Server::EDITABLE,
                    'callback' => [$this, 'approveRequest'],
                    'permission_callback' => $this->currentUserCanManage('art_submissions', 'update'),
                ],
                'schema' => [$this, 'get_public_item_schema'],
            ]
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/(?P<artSubmission>[\d]+)/reject',
            [
                [
                    'methods' => WP_REST_Server::EDITABLE,
                    'callback' => [$this, 'rejectRequest'],
                    'permission_callback' => $this->currentUserCanManage('art_submissions', 'update'),
                ],
                'schema' => [$this, 'get_public_item_schema'],
            ]
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/(?P<artwork>[\d]+)',
            [
                [
                    'methods' => WP_REST_Server::DELETABLE,
                    'callback' => [$this, 'deleteArtwork'],
                    'permission_callback' => $this->currentUserCanManage('art_submissions', 'delete'),
                ],
                'schema' => [$this, 'get_public_item_schema'],
            ]
        );
    }

    /**
     * @param WP_REST_Request $request
     * @return mixed
     */
    public function submitArt($request)
    {
        $userId = get_current_user_id();

        $this->validate($request, [
            'title' => [new Constraints\Required(), new Constraints\Length(['min' => 3])],
            'links' => [new Constraints\Required(), new Constraints\Length(['min' => 10])],
        ]);

        $submitOptions = ArtSubmission::getOptions();

        $pendingSubmits = ArtSubmission::query()
            ->where('artist_id', $userId)
            ->whereIn('status', [ArtSubmission::STATUS_PROCESSING])
            ->count();

        if ($pendingSubmits > $submitOptions['max_submissions_per_artist']) {
            return new WP_Error(
                'rest_exists_artist_request',
                __(
                    'You already have a pending art submission. Please wait for us to process it then you can submit new art.',
                    'artisus'
                ),
                ['code' => 400]
            );
        }

        $artSubmission = new ArtSubmission();
        $artSubmission->status = ArtSubmission::STATUS_PROCESSING;
        $artSubmission->artist_id = $userId;
        $artSubmission->title = sanitize_text_field($request->get_param('title'));
        $artSubmission->links = sanitize_textarea_field($request->get_param('links'));
        $artSubmission->save();

        do_action('artisus_art_submission_created', $artSubmission);

        return [
            'status' => 'success',
            'message' => null,
        ];
    }

    /**
     * @param WP_REST_Request $request
     * @return mixed
     */
    public function createNewArt($request)
    {
        $this->validate($request, [
            'title' => [new Constraints\NotNull(), new Constraints\Length(['min' => 3])],
            'artist_id' => [new Constraints\NotNull(), new Constraints\Positive()],
        ]);

        $artist = get_user_by('id', $request->get_param('artist_id'));

        if (!$artist || !Artist::isArtist($artist)) {
            return new WP_Error(
                'rest_request_error',
                __('Unable to create art for this user - he is not an artist. Please check again.', 'artisus')
            );
        }

        $artSubmission = new ArtSubmission();
        $artSubmission->status = ArtSubmission::STATUS_PROCESSING;
        $artSubmission->artist_id = $artist->ID;
        $artSubmission->title = sanitize_text_field($request->get_param('title'));
        $artSubmission->thumbnail_id = absint($request->get_param('thumbnail_id')) ?: null;
        $artSubmission->links = '';
        $artSubmission->save();

        do_action('artisus_art_submission_created', $artSubmission);

        return [
            'status' => 'success',
            'message' => null,
        ];
    }

    /**
     * TODO: Merge with updatePricing
     * @param WP_REST_Request $request
     * @param ArtSubmission $artSubmission
     * @return mixed
     */
    public function assignPrintSizes($request, ArtSubmission $artSubmission)
    {
        $this->validate($request, [
            'sizes' => [new Constraints\NotBlank(), new Constraints\Type('array')],
            'gross_profits' => [new Constraints\NotBlank(), new Constraints\Type('array')],
        ]);

        if ($artSubmission->status !== ArtSubmission::STATUS_PROCESSING) {
            return new WP_Error(
                'rest_request_error',
                __('Unable to assign print sizes for this submission.', 'artisus')
            );
        }

        // Update tags & categories.
        $artSubmission->tags = array_unique(wp_parse_list($request->get_param('tags')));
        $artSubmission->categories = wp_parse_id_list($request->get_param('categories'));;
        $artSubmission->save();

        // Update thumbnail ID.
        if ($request->get_param('thumbnail_id') && wp_attachment_is('image', $request->get_param('thumbnail_id'))) {
            $artSubmission->thumbnail_id = (int) $request->get_param('thumbnail_id');
            $artSubmission->save();
        }

        // Update author pricing.
        if ($request->has_param('type') || $request->has_param('amount')) {
            $artSubmission->amount = (float) wc_format_decimal($request->get_param('amount'), false, true) ?: null;
            $artSubmission->type = 'fixed' === $request->get_param('type') ? 'fixed' : 'percent';

            $artSubmission->save();
        }

        // Print-sizes.
        $printSizes = (new Collection($request->get_param('sizes')))
            ->filter(function ($price, $size) {
                return false !== strpos($size, '/');
            });

        $freightCost = $request->get_param('freight_cost');
        if (!is_array($freightCost)) {
            $freightCost = [];
        }

        $grossProfits = $request->get_param('gross_profits');
        if (!is_array($freightCost)) {
            $grossProfits = [];
        }

        // Check valid of sizes again.
        if ($printSizes->isEmpty()) {
            return new WP_Error('rest_request_error', __('Invalid request data for print sizes', 'artisus'));
        }

        DB::getInstance()->transaction(
            function () use ($freightCost, $grossProfits, $artSubmission, $printSizes) {
                $currentPrintSizes = $artSubmission->printSizes;

                $touchedIds = [];

                foreach ($printSizes as $printSizeName => $price) {
                    [$productType, $size] = array_map('trim', explode('/', $printSizeName, 2));

                    /** @var \ArtisUs\Models\ArtSubmissionPrintSize $existsPrintSize */
                    $existsPrintSize = $currentPrintSizes
                        ->where('name', trim($printSizeName))
                        ->first();

                    if ($existsPrintSize) {
                        $existsPrintSize->update(
                            [
                                'base_price' => (float) wc_format_decimal($price),
                                'freight_cost' => (float) wc_format_decimal($freightCost[$printSizeName] ?? 0),
                                'gross_profit' => !empty($grossProfits[$printSizeName])
                                    ? (float) wc_format_decimal($grossProfits[$printSizeName])
                                    : null,
                            ]
                        );

                        $touchedIds[] = $existsPrintSize->getKey();
                    } else {
                        $touchedIds[] = $artSubmission->printSizes()->create(
                            [
                                'size' => $size,
                                'product_type' => $productType,
                                'base_price' => (float) wc_format_decimal($price),
                                'freight_cost' => (float) wc_format_decimal($freightCost[$printSizeName] ?? 0),
                                'gross_profit' => !empty($grossProfits[$printSizeName])
                                    ? (float) wc_format_decimal($grossProfits[$printSizeName])
                                    : null,
                            ]
                        )->getKey();
                    }
                }

                // Delete old rows.
                if (count($touchedIds) > 0) {
                    $artSubmission->printSizes()
                        ->whereNotIn('id', $touchedIds)
                        ->delete();
                }
            }
        );

        // Reload the model data.
        $artSubmission->refresh();

        // Fire action perform send email, etc...
        do_action(
            'artisus_art_submission_print_sizes_updated',
            $artSubmission,
            $artSubmission->artist_id,
            get_current_user_id()
        );

        return [
            'code' => 'success',
            'data' => $artSubmission->loadMissing('printSizes'),
        ];
    }

    /**
     * @param WP_REST_Request $request
     * @param ArtSubmission $artwork
     * @return mixed
     */
    public function createPrintSize($request, ArtSubmission $artwork)
    {
        $this->validate($request, [
            'size' => [new Constraints\NotBlank(), new Constraints\Regex('/^(.*)\s?x\s?(.*)$/')],
            'product_type' => [new Constraints\NotBlank()],
            'base_price' => [new Constraints\NotBlank(), new Constraints\GreaterThanOrEqual(0)],
            'freight_cost' => [new Constraints\Optional(), new Constraints\GreaterThanOrEqual(0)],
        ]);

        $allowedStatus = [
            ArtSubmission::STATUS_PROCESSING,
            ArtSubmission::STATUS_APPROVED,
        ];

        if (!in_array($artwork->status, $allowedStatus, true)) {
            return new WP_Error(
                'rest_request_error',
                __('Unable to create new print size for this submission.', 'artisus')
            );
        }

        $printSize = $artwork->printSizes()
            ->firstOrCreate(
                [
                    'product_type' => sanitize_text_field($request->get_param('product_type')),
                    'size' => sanitize_text_field($request->get_param('size')),
                ],
                [
                    'generator' => true,
                    'base_price' => (float) wc_format_decimal($request->get_param('base_price')),
                    'freight_cost' => (float) wc_format_decimal($request->get_param('freight_cost')),
                ]
            );

        if (!$printSize->wasRecentlyCreated) {
            return new WP_Error('rest_request_error', 'Print size for this this submission already exists.');
        }

        // Create products.
        $productGenerator = new ProductGenerator(
            new RuntimeProductTypeManager(
                (new PrintSizeCollection([$printSize]))
                    ->toProductTypes()
                    ->toArray()
            ),
            $artwork->tags ?? [],
            $artwork->categories ?? []
        );

        if ($artwork->thumbnail_id) {
            $productGenerator->useDefaultThumbnailID($artwork->thumbnail_id);
        }

        $productGenerator->addVariationProducts($artwork);

        $printSize->refresh();

        return [
            'status' => 'success',
            'data' => array_merge(
                ['printSize' => $printSize->toPrintSize()],
                $printSize->toArray()
            ),
        ];
    }

    /**
     * @param WP_REST_Request $request
     * @param ArtSubmission $artwork
     * @return mixed
     */
    public function addPrintSizeFromGroup($request, ArtSubmission $artwork)
    {
        $this->validate($request, [
            'group_id' => [new Constraints\NotBlank(), new Constraints\GreaterThanOrEqual(0)],
        ]);

        $allowedStatus = [
            ArtSubmission::STATUS_PROCESSING,
            ArtSubmission::STATUS_APPROVED,
        ];

        if (!in_array($artwork->status, $allowedStatus, true)) {
            return new WP_Error(
                'rest_request_error',
                __('Unable to add print-sizes for this artwork.', 'artisus')
            );
        }

        $group = PrintSizeTemplateGroup::query()
            ->where('id', (int) $request->get_param('group_id'))
            ->firstOrFail();

        $templates = $group->templates;

        if (empty($templates)) {
            return new WP_Error(
                'rest_request_error',
                __('Unable to add print-sizes for this artwork.', 'artisus')
            );
        }

        $printSizes = [];
        foreach ($templates as $template) {
            /** @var ArtSubmissionPrintSize $_printSize */
            $_printSize = $artwork->printSizes()->firstOrCreate(
                [
                    'product_type' => $template->product_type,
                    'size' => $template->size,
                ],
                [
                    'generator' => true,
                    'base_price' => $template->production_cost,
                    'freight_cost' => $template->freight_cost,
                ]
            );

            if ($_printSize->wasRecentlyCreated || !$_printSize->product_variation_id) {
                $printSizes[] = $_printSize;

                $updatedSummary[] = sprintf('Added: %s', $_printSize->name);
            } else {
                $updatedSummary[] = sprintf('Skipped: %s', $_printSize->name);
            }
        }

        // Create products.
        $productGenerator = new ProductGenerator(
            new RuntimeProductTypeManager(
                (new PrintSizeCollection($printSizes))
                    ->toProductTypes()
                    ->toArray()
            ),
            $artwork->tags ?? [],
            $artwork->categories ?? []
        );

        if ($artwork->thumbnail_id) {
            $productGenerator->useDefaultThumbnailID($artwork->thumbnail_id);
        }

        $productGenerator->addVariationProducts($artwork);

        if (count($updatedSummary) === 0) {
            $updatedSummary = ['Nothing to update'];
        }

        $artwork->refresh()
            ->loadMissing('artist', 'printSizes')
            ->append(['product_tags', 'product_categories']);

        return [
            'status' => 'success',
            'data' => $artwork,
            'updatedSummary' => '<p>' . implode("</p><p>", $updatedSummary) . '</p>',
        ];
    }

    /**
     * @param WP_REST_Request $request
     * @param ArtSubmission $artwork
     */
    public function updatePricing($request, ArtSubmission $artwork)
    {
        $this->validate($request, [
            'type' => [new Constraints\NotBlank(), new Constraints\Choice(['fixed', 'percent'])],
            'amount' => [new Constraints\NotBlank()],
            'gross_profits' => [new Constraints\NotBlank(), new Constraints\Type('array')],
            'production_prices' => [new Constraints\NotBlank(), new Constraints\Type('array')],
            'freight_cost' => [new Constraints\NotBlank(), new Constraints\Type('array')],
        ]);

        if ($artwork->status !== ArtSubmission::STATUS_APPROVED) {
            return new WP_Error('rest_request_error', __('Cannot update pricing for this submission.', 'artisus'));
        }

        $printSizes = $artwork->printSizes;
        if ($printSizes->isEmpty()) {
            return new WP_Error('rest_request_error', __('Cannot update pricing for this submission.', 'artisus'));
        }

        // Update tags & categories.
        $artwork->tags = array_unique(wp_parse_list($request->get_param('tags')));;
        $artwork->categories = wp_parse_id_list($request->get_param('categories'));
        $artwork->save();

        // Update the artist pricing.
        $artwork->type = $request->get_param('type');
        $artwork->amount = $request->get_param('amount');

        // Update the pricing on printSizes.
        $printSizes = $printSizes->keyBy('id');

        $grossProfits = $request->get_param('gross_profits');
        $productionPrices = $request->get_param('production_prices');
        $freightCost = $request->get_param('freight_cost');

        foreach ($grossProfits as $key => $grossProfit) {
            if ($printSizes->has($key) && $printSizes[$key]->gross_profit !== $grossProfit) {
                $printSizes[$key]->gross_profit = $grossProfit;
            }
        }

        foreach ($productionPrices as $key => $productionPrice) {
            if ($printSizes->has($key) && $printSizes[$key]->base_price !== $productionPrice) {
                $printSizes[$key]->base_price = $productionPrice;
            }
        }

        foreach ($freightCost as $key => $_freightCost) {
            if ($printSizes->has($key) && $printSizes[$key]->freight_cost !== $_freightCost) {
                $printSizes[$key]->freight_cost = $_freightCost;
            }
        }

        $shouldUpdateProductPrices = 0;

        DB::getInstance()->transaction(function () use ($artwork, $printSizes, &$shouldUpdateProductPrices) {
            if ($artwork->isDirty() && $artwork->save()) {
                $shouldUpdateProductPrices++;
            }

            foreach ($printSizes as $printSize) {
                if ($printSize->isDirty() && $printSize->save()) {
                    $shouldUpdateProductPrices++;
                }
            }
        });

        $updatedSummary = [];

        if ($shouldUpdateProductPrices) {
            foreach ($printSizes as $printSize) {
                $product = wc_get_product($printSize->product_variation_id);
                if (!$product || !$product instanceof WC_Product_Variation) {
                    continue;
                }

                $oldPrice = $product->get_regular_price();

                $price = $printSize->toPrintSize()
                    ->getFinalPrice($artwork->amount, $artwork->type);

                $product->set_regular_price($price);
                $product->save();

                if (wc_format_decimal($oldPrice) !== wc_format_decimal($price)) {
                    $updatedSummary[] = sprintf(
                        '#%s: %s -> %s',
                        $product->get_id(),
                        wc_price($oldPrice),
                        wc_price($price)
                    );
                }

                unset($product);
            }
        }

        return [
            'status' => 'success',
            'updatedSummary' => '<p>' . implode("</p><p>", $updatedSummary) . '</p>',
        ];
    }

    /**
     * @param WP_REST_Request $request
     * @return mixed
     */
    public function approveRequest($request, ArtSubmission $artSubmission)
    {
        if ($artSubmission->status === ArtSubmission::STATUS_APPROVED) {
            return new WP_Error('rest_client_error', 'Unable approve this submission.', ['status' => 400]);
        }

        try {
            $artSubmission->approve(
                wp_kses_post($request->get_param('reason'))
            );

            // Dispatch generate product job.
            CreateArtSubmissionProduct::dispatch($artSubmission->getKey());

            return ['status' => 'success'];
        } catch (Exception $e) {
            return new WP_Error('rest_server_error', $e->getMessage(), ['status' => 500]);
        }
    }

    /**
     * @param WP_REST_Request $request
     * @return mixed
     */
    public function rejectRequest($request, ArtSubmission $artSubmission)
    {
        try {
            $artSubmission->reject(
                wp_kses_post($request->get_param('reason'))
            );

            return ['status' => 'success'];
        } catch (Exception $e) {
            return new WP_Error('rest_server_error', $e->getMessage(), ['status' => 500]);
        }
    }

    /**
     * @param WP_REST_Request $request
     * @param ArtSubmission $artwork
     * @return array
     */
    public function deleteArtwork($request, ArtSubmission $artwork)
    {
        $artwork->delete();

        return ['status' => 'success'];
    }
}
