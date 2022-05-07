<?php

namespace AweBooking\PMS\Repositories;

use AweBooking\Contracts\StaffRepository as StaffRepositoryContract;
use AweBooking\Contracts\ServiceRepository as ServiceRepositoryContract;
use AweBooking\Contracts\AppointmentRepository as AppointmentRepositoryContract;
use AweBooking\Contracts\AvailabilityRepository as AvailabilityRepositoryContract;
use AweBooking\Models\Appointment;
use AweBooking\Traits\InteractsWithDateTime;
use AweBooking\Vendor\Illuminate\Database\Eloquent\Builder;
use LogicException;
use Throwable;
use WP_Error;
use WP_User;

class AppointmentRepository implements AppointmentRepositoryContract
{
    use InteractsWithDateTime;

    /**
     * @var AvailabilityRepositoryContract|AvailabilityRepository
     */
    protected $availabilityRepository;

    /**
     * @var StaffRepositoryContract|StaffRepository
     */
    protected $staffRepository;

    /**
     * @var ServiceRepositoryContract|ServiceRepository
     */
    protected $serviceRepository;

    public function __construct(
        StaffRepositoryContract $staffRepository,
        ServiceRepositoryContract $serviceRepository,
        AvailabilityRepositoryContract $availabilityRepository
    ) {
        $this->staffRepository = $staffRepository;
        $this->serviceRepository = $serviceRepository;
        $this->availabilityRepository = $availabilityRepository;
    }

    /**
     * @param array $data
     * @param WP_User $actor
     * @param string $context
     * @return Appointment|WP_Error
     */
    public function create(array $data, WP_User $actor, string $context = 'dashboard')
    {
        $defaults = [
            'status' => Appointment::STATUS_RESERVED,
            'start_date' => null,
            'end_date' => null,
            'staff_id' => 0,
            'service_id' => 0,

            'is_custom_service' => false,
            'custom_service_name' => null,
            'custom_service_price' => null,

            'contact_id' => 0,
            'contact_name' => null,
            'contact_email' => null,
            'contact_phone' => null,

            'internal_note' => '',
            'created_via' => $context,
        ];

        $data = array_merge($defaults, $data);

        try {
            [$startDate, $endDate] = $this->parseDateRange($data['start_date'], $data['end_date']);
        } catch (LogicException $e) {
            return new WP_Error('date_range', $e->getMessage());
        }

        $staff = $this->staffRepository->findById($data['staff_id']);
        if ($staff === null) {
            return new WP_Error('staff_not_found', __('Staff not found', 'awepointment'));
        }

        $service = $this->serviceRepository->findById($data['service_id']);
        if ($service === null) {
            return new WP_Error('service_not_found', __('Service not found', 'awepointment'));
        }

        $duration = $service->getTotalDuration();

        $isStaffAvailable = $this->availabilityRepository
            ->isStaffAvailable($staff, $startDate, $endDate, $duration);

        if (is_wp_error($isStaffAvailable)) {
            return $isStaffAvailable;
        }

        if ($isStaffAvailable !== true) {
            return new WP_Error(
                'staff_not_available',
                __('Staff is not available', 'awepointment')
            );
        }

        $appointment = new Appointment();
        $appointment->status = Appointment::STATUS_RESERVED;
        $appointment->staff()->associate($staff);
        $appointment->service()->associate($service);

        $appointment->start_date = $startDate;
        $appointment->end_date = $endDate;
        $appointment->is_custom_service = false;
        $appointment->custom_service_name = null;
        $appointment->custom_service_price = null;
        $appointment->internal_note = sanitize_textarea_field($data['internal_note']);
        $appointment->created_by_id = $actor->ID;
        $appointment->created_by = $actor->display_name;
        $appointment->created_via = $data['created_via'];

        try {
            $appointment->saveOrFail();

            return $appointment;
        } catch (Throwable $e) {
            return new WP_Error('saving_error', $e->getMessage(), ['code' => 500]);
        }
    }

    /**
     * @param int $id
     * @param string[] $columns
     * @return Appointment|null
     */
    public function findById($id, $columns = ['*']): ?Appointment
    {
        return Appointment::query()->find($id, $columns);
    }

    /**
     * {@inheritdoc}
     */
    public function createQueryBuilder(): Builder
    {
        return Appointment::query();
    }
}
