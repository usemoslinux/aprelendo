<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

final class AuthGuard
{
    private static ?self $current_guard = null;

    private \PDO $pdo;
    private User $user;
    private UserAuth $user_auth;
    private bool $user_is_logged = false;
    private bool $was_checked = false;

    /**
     * Constructor
     *
     * @param \PDO|null $pdo
     */
    public function __construct(?\PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::connection();
        $this->user = new User($this->pdo);
        $this->user_auth = new UserAuth($this->user);
    }

    /**
     * Returns the shared auth guard for the current request.
     *
     * @param \PDO|null $pdo
     * @return self
     */
    public static function current(?\PDO $pdo = null): self
    {
        if (self::$current_guard === null) {
            self::$current_guard = new self($pdo);
        }

        return self::$current_guard;
    }

    /**
     * Requires and returns the logged-in user for a full page request.
     *
     * @param \PDO|null $pdo
     * @return User
     */
    public static function requirePageUser(?\PDO $pdo = null): User
    {
        return self::current($pdo)->requirePage();
    }

    /**
     * Requires and returns the logged-in user for an AJAX request.
     *
     * @param \PDO|null $pdo
     * @return User
     */
    public static function requireAjaxUser(?\PDO $pdo = null): User
    {
        return self::current($pdo)->requireAjax();
    }

    /**
     * Returns the current user object, loading it from the auth token if present.
     *
     * @return User
     */
    public function loadUser(): User
    {
        if ($this->was_checked) {
            return $this->user;
        }

        $this->user_is_logged = $this->user_auth->isLoggedIn();
        $this->setConnectionTimeZone($this->user->time_zone);
        $this->was_checked = true;

        return $this->user;
    }

    /**
     * Checks whether the current request has a valid logged-in user.
     *
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        $this->loadUser();
        return $this->user_is_logged;
    }

    /**
     * Returns the UserAuth instance used by this guard.
     *
     * @return UserAuth
     */
    public function getUserAuth(): UserAuth
    {
        return $this->user_auth;
    }

    /**
     * Requires login and redirects anonymous users to the login page.
     *
     * @return User
     */
    public function requirePage(): User
    {
        if (!$this->isLoggedIn()) {
            self::redirectToLogin();
        }

        return $this->user;
    }

    /**
     * Requires login and returns JSON 401 for anonymous users.
     *
     * @return User
     */
    public function requireAjax(): User
    {
        if (!$this->isLoggedIn()) {
            self::sendUnauthorizedJson();
        }

        return $this->user;
    }

    /**
     * Checks whether the current request expects an AJAX-style auth response.
     *
     * @return bool
     */
    public static function shouldReturnJson(): bool
    {
        $script_name = $_SERVER['SCRIPT_NAME'] ?? '';
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        $requested_with = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '');
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';

        return strpos($script_name, '/ajax/') !== false
            || strpos($request_uri, '/ajax/') !== false
            || $requested_with === 'xmlhttprequest'
            || strpos($accept, 'application/json') !== false;
    }

    /**
     * Redirects anonymous page requests to the login page.
     *
     * @return void
     */
    public static function redirectToLogin(): void
    {
        header('Location:/login');
        exit;
    }

    /**
     * Sends the standard unauthenticated JSON response.
     *
     * @return void
     */
    public static function sendUnauthorizedJson(): void
    {
        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'error_msg' => 'Please log in to continue.'
        ]);
        exit;
    }

    /**
     * Applies the user's time zone to DB date/time operations for this request.
     *
     * @param string $time_zone
     * @return void
     */
    private function setConnectionTimeZone(string $time_zone): void
    {
        $quoted_time_zone = $this->pdo->quote($time_zone);

        if ($quoted_time_zone === false) {
            return;
        }

        $this->pdo->exec("SET time_zone={$quoted_time_zone};");
    }
}
