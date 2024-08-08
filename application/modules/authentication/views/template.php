<!doctype html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Gleh Store">
    <meta name="theme-color" content="#f9ca24">
    <meta name="google" content="notranslate" />
    <title>Helpdesk Apps</title>
    <link href="<?php echo base_url('assets/backend/plugins/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet" id="style" />
    <link href="<?php echo base_url('assets/backend/css/style.css') ?>" rel="stylesheet" />
    <link href="<?php echo base_url('assets/backend/css/dark-style.css') ?>" rel="stylesheet" />
    <link href="<?php echo base_url('assets/backend/css/transparent-style.css') ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/backend/css/skin-modes.css') ?>" rel="stylesheet" />
    <link href="<?php echo base_url('assets/backend/css/icons.css') ?>" rel="stylesheet" />
    <link href="<?php echo base_url('assets/backend/colors/color1.css') ?>" id="theme" rel="stylesheet" type="text/css" media="all" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/backend/jquery-easy-loading/dist/jquery.loading.min.css') ?>">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://code.jquery.com/jquery.min.js"></script>
    <script src="<?php echo base_url('assets/backend/jquery-easy-loading/dist/jquery.loading.min.js') ?>"></script>
    <script type="text/javascript" charset="utf-8" async defer>
        function updateCSRF(value) {
            return $('input[name=csrf_myapp]').val(value);
        }
    </script>
    <style>
        body,
        .login-img::before {
            background: #010100;
        }

        body {
            overflow-y: scroll;
        }

        body::-webkit-scrollbar {
            display: none;
        }

        body {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .sweet-alert button {
            background-color: #1e272e !important;
            min-width: 80px !important;
            font-weight: bold;
        }

        .sweet-alert p {
            color: #000 !important;
        }
    </style>
</head>

<body class="app sidebar-mini ltr">

    <?php echo $this->template->content ?>

    <script src="<?php echo base_url('assets/backend/js/jquery.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/backend/plugins/bootstrap/js/popper.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/backend/plugins/bootstrap/js/bootstrap.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/backend/js/show-password.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/backend/js/generate-otp.js') ?>"></script>

    <script src="<?php echo base_url('assets/backend/plugins/sweet-alert/sweetalert.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/backend/js/sweet-alert.js') ?>"></script>

    <script src="<?php echo base_url('assets/backend/plugins/p-scroll/perfect-scrollbar.js') ?>"></script>
    <script src="<?php echo base_url('assets/backend/js/themeColors.js') ?>"></script>
    <script src="<?php echo base_url('assets/backend/js/custom.js') ?>"></script>
</body>

</html>