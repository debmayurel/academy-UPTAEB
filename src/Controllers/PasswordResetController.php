<?php

namespace App\Controllers;

use App\Services\AuthenticationService;
use App\Services\PasswordResetService;

class PasswordResetController extends BaseController
{
    public function __construct(
        private AuthenticationService $authService,
        private PasswordResetService $passwordResetService
    ) {
    }

    public function showForgotPassword(): void
    {
        if ($this->authService->isAuthenticated()) {
            $this->redirect('/');
        }

        $this->render('forgot_password');
    }

    public function resetPassword(): void
    {
        $data = $this->getRequestData();
        $result = $this->passwordResetService->resetPassword($data);
        $status = (int) ($result['status'] ?? 200);
        unset($result['status']);

        if (!empty($result['ok'])) {
            $result['redirect'] = '/login';
        }

        $this->json($result, $status);
    }
}
