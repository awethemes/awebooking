<?php

namespace AweBooking\PMS\REST\Controllers;

use ArtisUs\Artist\Onboard\Question;
use ArtisUs\Artist\Onboard\QuestionManager;
use ArtisUs\Email\ArtistRequestApproved;
use ArtisUs\Email\ArtistRequestRejected;
use ArtisUs\Models\Artist;
use ArtisUs\Models\ArtistRequest;
use ArtisUs\Models\ArtistRequestQuestion;
use ArtisUs\REST\RESTController;
use ArtisUs\Roles;
use WC_Customer;
use WP_Error;
use WP_REST_Request;
use WP_REST_Server;
use WP_User;
use Symfony\Component\Validator\Constraints;

use function ArtisUs\resolve;

class ArtistRequestsController extends RESTController
{
    /**
     * @var string
     */
    protected $rest_base = 'artist';

    /**
     * @var QuestionManager
     */
    protected $questionManager;

    /**
     * @param QuestionManager $questionManager
     */
    public function __construct(QuestionManager $questionManager)
    {
        $this->questionManager = $questionManager;
    }

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
                    'callback' => [$this, 'createRequest'],
                    'permission_callback' => [$this, 'createRequestPermissionCheck'],
                ],
                'schema' => [$this, 'get_public_item_schema'],
            ]
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/(?P<request>[\d]+)/approve',
            [
                [
                    'methods' => WP_REST_Server::EDITABLE,
                    'callback' => [$this, 'approveRequest'],
                    'permission_callback' => $this->currentUserCanManage('artists_requests', 'update'),
                ],
                'schema' => [$this, 'get_public_item_schema'],
            ]
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/(?P<request>[\d]+)/reject',
            [
                [
                    'methods' => WP_REST_Server::EDITABLE,
                    'callback' => [$this, 'rejectRequest'],
                    'permission_callback' => $this->currentUserCanManage('artists_requests', 'update'),
                ],
                'schema' => [$this, 'get_public_item_schema'],
            ]
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/(?P<artistRequest>[\d]+)/update',
            [
                [
                    'methods' => WP_REST_Server::EDITABLE,
                    'callback' => [$this, 'updateArtist'],
                    'permission_callback' => $this->currentUserCanManage('artists_requests', 'update'),
                ],
                'schema' => [$this, 'get_public_item_schema'],
            ]
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/(?P<artistRequest>[\d]+)',
            [
                [
                    'methods' => WP_REST_Server::DELETABLE,
                    'callback' => [$this, 'deleteArtist'],
                    'permission_callback' => $this->currentUserCanManage('artists_requests', 'delete'),
                ],
                'schema' => [$this, 'get_public_item_schema'],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createRequestPermissionCheck()
    {
        return !Artist::isArtist(wp_get_current_user());
    }

    /**
     * @param WP_REST_Request $request
     * @return mixed
     */
    public function createRequest($request)
    {
        $this->validateCreateRequest($request);

        /** @var WP_User $user */
        $user = wp_get_current_user();

        // Avoid assign an admin or manager to artis role.
        if ($user->ID && !ArtistRequest::userCanCreateRequest($user)) {
            return new WP_Error('rest_request_error', __('You are not to able create an artist request.', 'artisus'));
        }

        if ($user->ID === 0) {
            $existsRequests = ArtistRequest::getByEmail($request->get_param('email'));
        } else {
            $existsRequests = ArtistRequest::getByCustomer($user->ID);
        }

        if ($existsRequests && $existsRequests->status === ArtistRequest::STATUS_NEW) {
            return new WP_Error(
                'rest_exists_artist_request',
                __('You already have a previous request. Please wait for us to review it.', 'artisus'),
                ['code' => 400]
            );
        }

        if ($existsRequests && $existsRequests->status === ArtistRequest::STATUS_APPROVED) {
            return new WP_Error(
                'rest_exists_artist_request',
                __(
                    'You have a request that has been approved before. If you get into trouble with your account please contact to administrator.',
                    'artisus'
                ),
                ['code' => 400]
            );
        }

        // Create new user.
        if ($user->ID === 0) {
            $email = strtolower($request->get_param('email'));

            if (email_exists($email)) {
                return new WP_Error(
                    'rest_request_error',
                    __('An account is already registered with your email address. Please try again.', 'artisus')
                );
            }

            $user = $this->createNewUserByEmail($email);
            if (is_wp_error($user)) {
                return $user;
            }
        }

        $questionsData = $request->get_param('questions') ?: [];
        $gstRegistered = $questionsData['has_gst'] ?? false;

        $artistRequest = new ArtistRequest();
        $artistRequest->status = ArtistRequest::STATUS_NEW;
        $artistRequest->customer_id = $user->ID;
        $artistRequest->email = $user->user_email;
        $artistRequest->name = $user->display_name;
        $artistRequest->has_gst = (bool) $gstRegistered;
        $artistRequest->save();

        foreach ($this->questionManager->getQuestions() as $question) {
            $artistRequest->questions()->save(
                new ArtistRequestQuestion(
                    [
                        'question' => $question->id,
                        'answer' => $question->sanitizeRawValue($questionsData[$question->id] ?? ''),
                    ]
                )
            );
        }

        $this->populateUserData($user, $request, $questionsData);

        do_action('artisus_artist_request_created', $artistRequest);

        return ['status' => 'success'];
    }

    /**
     * @param string $email
     * @return WP_Error|WP_User
     */
    protected function createNewUserByEmail($email)
    {
        // Force registration generate password.
        add_filter('pre_option_woocommerce_registration_generate_password', function () {
            return 'yes';
        });

        $username = wc_create_new_customer_username($email);

        $userId = wc_create_new_customer($email, $username);

        if (is_wp_error($userId)) {
            return $userId;
        }

        return new WP_User($userId);
    }

    /**
     * @param WP_User $user
     * @param WP_REST_Request $request
     * @param array $questionsData
     */
    protected function populateUserData($user, $request, array $questionsData)
    {
        // Perform save the information to the account.
        $customer = new WC_Customer($user->ID);

        if ($fullName = $request->get_param('name')) {
            $nameParts = array_map('trim', explode(' ', $fullName, 2));
            [$firstName, $lastName] = [$nameParts[0], $nameParts[1] ?? null];

            if (!$customer->get_first_name()) {
                $customer->set_first_name($firstName);
            }

            if ($lastName && !$customer->get_last_name()) {
                $customer->set_last_name($lastName);
            }

            $customer->set_display_name($fullName);
        }

        if (!$customer->get_billing_address() && !empty($questionsData['address'])) {
            $customer->set_billing_address($questionsData['address']);
        }

        if (!$customer->get_billing_phone() && !empty($questionsData['phone'])) {
            $customer->set_billing_phone($questionsData['phone']);
        }

        $customer->save();
    }

    /**
     * @param WP_REST_Request $request
     * @return void
     * @throws \ArtisUs\REST\Exception\ValidationException
     */
    protected function validateCreateRequest($request)
    {
        $questionsConstraints = $this->questionManager->getQuestions()
            ->keyBy('id')
            ->map(function (Question $question) {
                return $question->resolveConstraints();
            })->filter()->toArray();

        $rules = [
            'questions' => new Constraints\Collection($questionsConstraints),
        ];

        if (get_current_user_id() === 0) {
            $rules = array_merge($rules, [
                'name' => [new Constraints\NotBlank(), new Constraints\Length(['min' => 3])],
                'email' => [new Constraints\NotBlank(), new Constraints\Email()],
            ]);
        }

        $this->validate($request, $rules);
    }

    /**
     * @param WP_REST_Request $request
     * @return mixed
     */
    public function approveRequest($request)
    {
        /** @var ArtistRequest $artistRequest */
        $artistRequest = ArtistRequest::query()
            ->findOrFail($request->get_param('request'));

        $user = new WP_User($artistRequest->customer_id);

        // Avoid assign an admin or manager to artis role.
        if (!ArtistRequest::userCanCreateRequest($user)) {
            return new WP_Error(
                'rest_request_error',
                __('This request from an administrator or manager. Cannot assign to artist role.', 'artisus')
            );
        }

        $approved = $artistRequest->approve(
            wp_kses_post($request->get_param('reason'))
        );

        if ($approved) {
            $user = new WP_User($artistRequest->customer_id);

            // Remove current role and add role.
            foreach ($user->roles as $roe) {
                $user->remove_role($roe);
            }

            $user->add_role(Roles::ARTIST);

            // Init WooCommerce email if not loaded.
            if (!class_exists('WC_Email')) {
                WC()->mailer();
            }

            resolve(ArtistRequestApproved::class)
                ->trigger($artistRequest->customer_id, $artistRequest);

            return ['status' => 'success'];
        }

        return new WP_Error(
            'rest_server_error',
            __('Ops, something bad happened. Please try again!', 'artisus')
        );
    }

    /**
     * @param WP_REST_Request $request
     * @return mixed
     */
    public function rejectRequest($request)
    {
        /** @var ArtistRequest $artistRequest */
        $artistRequest = ArtistRequest::query()
            ->findOrFail($request->get_param('request'));

        $artistRequest->reject(
            wp_kses_post($request->get_param('reason'))
        );

        // Init WooCommerce email if not loaded.
        if (!class_exists('WC_Email')) {
            WC()->mailer();
        }

        resolve(ArtistRequestRejected::class)
            ->trigger($artistRequest->customer_id, $artistRequest);

        return ['status' => 'success'];
    }

    /**
     * @param WP_REST_Request $request
     * @param ArtistRequest $artistRequest
     * @return mixed
     */
    public function updateArtist($request, ArtistRequest $artistRequest)
    {
        $this->validate(
            $request,
            [
                'artist_bio' => [new Constraints\Optional([new Constraints\Length(['min' => 3])])],
                'cover_pic_id' => [new Constraints\Optional([new Constraints\Type('numeric')])],
                'profile_pic_id' => [new Constraints\Optional([new Constraints\Type('numeric')])],
                'display_priority' => [new Constraints\Optional([new Constraints\Type('numeric')])],
                'has_gst' => [new Constraints\Optional([new Constraints\Type('boolean')])],
            ]
        );

        $artistRequest->display_priority = $request->get_param('display_priority') ?? 0;
        $artistRequest->has_gst = $request->get_param('has_gst') ?? false;
        $artistRequest->save();

        $user = $artistRequest->artist;

        update_user_meta($user->getKey(), 'artist_bio', $request->get_param('artist_bio'));
        update_user_meta($user->getKey(), 'cover_pic_id', $request->get_param('cover_pic_id'));
        update_user_meta($user->getKey(), 'profile_pic_id', $request->get_param('profile_pic_id'));

        return ['status' => 'success'];
    }

    /**
     * @param ArtistRequest $artistRequest
     * @return array
     */
    public function deleteArtist(ArtistRequest $artistRequest)
    {
        $artistRequest->delete();

        return ['status' => 'success'];
    }
}
