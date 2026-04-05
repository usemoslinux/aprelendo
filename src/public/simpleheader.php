<?php
// SPDX-License-Identifier: GPL-3.0-or-later

$header_cta_href = '/login';
$header_cta_label = 'Get started';

if (isset($curpage) && $curpage === 'login') {
    $header_cta_href = '/register';
    $header_cta_label = 'Create account';
} elseif (isset($curpage) && ($curpage === 'register' || $curpage === 'forgotpassword')) {
    $header_cta_href = '/login';
    $header_cta_label = 'Log in';
}
?>
<div class="d-flex flex-column full-vh">
    <header>
        <nav class="navbar navbar-expand-md navbar-light">
            <div class="container mtb">
                <!-- Brand -->
                <a class="navbar-brand" href="/"></a>

                <!-- Toggler Button -->
                <button class="navbar-toggler" aria-label="toggler button" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapsibleNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Navbar links -->
                <div class="collapse navbar-collapse" id="collapsibleNavbar">
                    <ul class="navbar-nav ms-auto mt-3 mt-md-auto pe-3">
                        <li class="nav-item my-2 me-md-2"><a class="nav-link" href="/#hiw">How it works</a></li>
                        <li class="nav-item my-2 me-md-2"><a class="nav-link" href="/donate">Donate</a></li>
                        <li class="nav-item my-2">
                            <a class="nav-link" id="login-menu" href="<?php echo $header_cta_href; ?>">
                                <?php echo $header_cta_label; ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
