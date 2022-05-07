<?php

namespace AweBooking\PMS\REST\Controllers;

use ArtisUs\Models\PrintSizeTemplate;
use ArtisUs\Models\PrintSizeTemplateGroup;
use ArtisUs\REST\RESTController;
use WP_Error;
use WP_REST_Request;
use WP_REST_Server;
use Symfony\Component\Validator\Constraints;

class PrintSizeTemplateController extends RESTController
{
    /**
     * @var string
     */
    protected $rest_base = 'print-size-templates';

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
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => [$this, 'show'],
                    'permission_callback' => $this->currentUserCanManage('sizing_templates', 'show'),
                ],
                'schema' => [$this, 'get_public_item_schema'],
            ]
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            [
                [
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => [$this, 'create'],
                    'permission_callback' => $this->currentUserCanManage('sizing_templates', 'create'),
                ],
                'schema' => [$this, 'get_public_item_schema'],
            ]
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/(?P<template>[\d]+)/update',
            [
                [
                    'methods' => WP_REST_Server::EDITABLE,
                    'callback' => [$this, 'update'],
                    'permission_callback' => $this->currentUserCanManage('sizing_templates', 'update'),
                ],
                'schema' => [$this, 'get_public_item_schema'],
            ]
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/(?P<template>[\d]+)',
            [
                [
                    'methods' => WP_REST_Server::DELETABLE,
                    'callback' => [$this, 'delete'],
                    'permission_callback' => $this->currentUserCanManage('sizing_templates', 'delete'),
                ],
                'schema' => [$this, 'get_public_item_schema'],
            ]
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/group',
            [
                [
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => [$this, 'createGroup'],
                    'permission_callback' => $this->currentUserCanManage('sizing_templates', 'create'),
                ],
                'schema' => [$this, 'get_public_item_schema'],
            ]
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/group/(?P<group>[\d]+)',
            [
                [
                    'methods' => WP_REST_Server::DELETABLE,
                    'callback' => [$this, 'deleteGroup'],
                    'permission_callback' => $this->currentUserCanManage('sizing_templates', 'delete'),
                ],
                'schema' => [$this, 'get_public_item_schema'],
            ]
        );
    }

    /**
     * @return mixed
     */
    public function show()
    {
        return PrintSizeTemplateGroup::query()
            ->with('templates')
            ->withCount('templates')
            ->limit(250)
            ->get()
            ->map(function ($group) {
                $group->setRelation('templates', $group->templates->toProductTypes());

                return $group;
            });
    }

    /**
     * @param WP_REST_Request $request
     * @return mixed
     */
    public function create($request)
    {
        $this->validate($request, [
            'group_id' => [new Constraints\NotBlank(), new Constraints\GreaterThanOrEqual(0)],
            'size' => [new Constraints\NotBlank()],
            'product_type' => [new Constraints\NotBlank()],
            'production_cost' => [new Constraints\NotBlank(), new Constraints\GreaterThanOrEqual(0)],
            'freight_cost' => [new Constraints\NotBlank(), new Constraints\GreaterThanOrEqual(0)],
        ]);

        $data = [
            'group_id' => absint($request->get_param('group_id')),
            'product_type' => sanitize_text_field($request->get_param('product_type')),
            'size' => sanitize_text_field($request->get_param('size')),
            'production_cost' => (float) wc_format_decimal($request->get_param('production_cost')),
            'freight_cost' => (float) wc_format_decimal($request->get_param('freight_cost')),
        ];

        if (PrintSizeTemplate::query()
            ->where('group_id', $data['group_id'])
            ->where('product_type', $data['product_type'])
            ->where('size', $data['size'])
            ->exists()
        ) {
            return new WP_Error('rest_error', 'Duplicate print size!');
        }

        $data['position'] = 1 + PrintSizeTemplate::query()
                ->where('group_id', $data['group_id'])
                ->where('product_type', $data['product_type'])
                ->max('position');

        $template = (new PrintSizeTemplate())->fill($data);
        $template->saveOrFail();

        return [
            'status' => 'success',
            'data' => $template->toPrintSize()
        ];
    }

    public function createGroup($request)
    {
        $this->validate($request, [
            'name' => [new Constraints\NotBlank()],
        ]);

        $name = sanitize_text_field($request->get_param('name'));
        if (PrintSizeTemplateGroup::query()->where('name', $name)->exists()) {
            return new WP_Error('rest_error', 'Duplicate group name!');
        }

        $group = (new PrintSizeTemplateGroup())->fill(['name' => $name]);
        $group->save();

        return [
            'status' => 'success',
            'data' => $group
        ];
    }

    /**
     * @param WP_REST_Request $request
     * @param PrintSizeTemplate $template
     * @return mixed
     */
    public function update($request, PrintSizeTemplate $template)
    {
        return ['status' => 'success'];
    }

    /**
     * @param PrintSizeTemplate $template
     * @return array
     */
    public function delete(PrintSizeTemplate $template)
    {
        $template->delete();

        return ['status' => 'success'];
    }

    /**
     * @param PrintSizeTemplateGroup $group
     * @return array
     */
    public function deleteGroup(PrintSizeTemplateGroup $group)
    {
        $group->delete();

        return ['status' => 'success'];
    }
}
