<?php

namespace AweBooking\PMS\Dashboard\Pages;

use AweBooking\PMS\Dashboard\InertiaPage;
use AweBooking\System\Inertia\Response;

class DashboardView extends InertiaPage
{
	public function view(): ?Response
	{
        if (!empty($_REQUEST['hello'])) {
            wp_safe_redirect(admin_url('admin.php?page=awebooking-pms&test=Xin Chào'));
            exit;
        }

        return $this->inertia->render('DashboardView', [
            'hello' => $_REQUEST['test'] ?? '',
        ]);
	}
}
