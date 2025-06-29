<nav class="navbar navbar-expand-lg navbar-dark bg-navy">
    <div class="container px-4 px-lg-5 ">
        <button class="navbar-toggler btn btn-sm" type="button" data-toggle="collapse"
            data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
            aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
        <a class="navbar-brand" href="<?php echo base_url ?>">
            <img src="<?php echo validate_image($_settings->info('logo')) ?>" width="30" height="30"
                class="d-inline-block align-top" alt="" loading="lazy">
            <?php echo $_settings->info('short_name') ?>
        </a>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-0">

                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                    <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off">
                    <label class="btn btn-outline-primary btn-sm" for="btnradio1">

                        <li class="nav-item"><a class="nav-link" aria-current="page" href="index.php"
                                style="font-weight:bold;color:#fff;">Home</a></li>
                    </label>


                    <input type="radio" class="btn-check" name="btnradio" id="btnradio3" autocomplete="off">
                    <label class="btn btn-outline-primary btn-sm" for="btnradio3">

                        <li class="nav-item"><a class="nav-link" aria-current="page" href="?p=events"
                                style="font-weight:bold;color:#fff;">Events</a></li>
                    </label>

                    <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off">
                    <label class="btn btn-outline-primary btn-sm " for="btnradio2">

                        <li class="nav-item"><a class="nav-link" aria-current="page" href="?p=causes"
                                style="font-weight:bold;color:#fff;">Our Team</a></li>
                    </label>


                    <input type="radio" class="btn-check" name="btnradio" id="btnradio4" autocomplete="off">
                    <label class="btn btn-outline-primary btn-sm" for="btnradio4">

                        <li class="nav-item"><a class="nav-link" href="<?php echo base_url ?>?p=about"
                                style="font-weight:bold;color:#fff;">About</a></li>
                    </label>

                    <input type="radio" class="btn-check" name="btnradio" id="btnradio5" autocomplete="off">
                    <label class="btn btn-outline-primary btn-sm" for="btnradio5">

                        <li class="nav-item"><a class="nav-link" aria-current="page" href="?p=gallery"
                                style="font-weight:bold;color:#fff;">Gallery</a></li>
                    </label>

                    <?php
          $cat_qry = $conn->query("SELECT * FROM topics where status = 1  limit 3");
          $count_cats = $conn->query("SELECT * FROM topics where status = 1 ")->num_rows;
          while ($crow = $cat_qry->fetch_assoc()):
            ?></label>
                    <input type="radio" class="btn-check" name="btnradio" id="btnradio6" autocomplete="off">
                    <label class="btn btn-outline-primary btn-sm" for="btnradio6">

                        <li class="nav-item"><a class="nav-link" aria-current="page"
                                href="<?php echo base_url ?>?p=articles&t=<?php echo md5($crow['id']) ?>"><?php echo $crow['name'] ?></a>
                        </li>
                        <?php endwhile; ?>
                        <?php if ($count_cats > 3): ?>
                    </label>
                    <li class="nav-item"><a class="nav-link" href="<?php echo base_url ?>?p=view_topics">...</a></li>
                    <?php endif; ?>
            </ul>

            <div class="d-flex align-items-center">
            </div>
        </div>
        <button class="btn btn-primary" type="button" id="login">Join</button>
        -
        <button class="btn btn-success" type="button" id="volunteer_login">Volunteer Login</button>
        -
        <button class="btn btn-warning" type="button" id="manager_login">Manager Login</button>
        -
        <button class="btn btn-primary" type="button" id="checkout">Donate</button>
        <form class="form-inline ml-4 mr-2 pl-2" id="search-form">
            <div class="input-group">
                <input class="form-control form-control-sm form " type="search" placeholder="Search" aria-label="Search"
                    name="search" value="<?php echo isset($_GET['search']) ? $_GET['search'] : "" ?>"
                    aria-describedby="button-addon2">
                <div class="input-group-append">
                    <button class="btn btn-outline-success btn-sm m-0" type="submit" id="button-addon2"><i
                            class="fa fa-search"></i></button>
                </div>
            </div>
        </form>
    </div>
</nav>
<script>
$(function() {
    $('#login-btn').click(function() {
        uni_modal("", "login.php")
    })
    $('#navbarResponsive').on('show.bs.collapse', function() {
        $('#mainNav').addClass('navbar-shrink')
    })
    $('#navbarResponsive').on('hidden.bs.collapse', function() {
        if ($('body').offset.top == 0)
            $('#mainNav').removeClass('navbar-shrink')
    })

    $('#search-form').submit(function(e) {
        e.preventDefault()
        var sTxt = $('[name="search"]').val()
        if (sTxt != '')
            location.href = './?p=search&search=' + sTxt;
    })
    $('#login').click(function() {
        uni_modal('Login', 'login.php')
    })
    $('#volunteer_login').click(function() {
        window.location.href = 'volunteer_login.php'
    })
    $('#manager_login').click(function() {
        window.location.href = 'manager/login.php'
    })
})
</script>
<script>
$(function() {
    $('#checkout-btn').click(function() {
        window.location.href = 'donation.php'
    })
    $('#navbarResponsive').on('show.bs.collapse', function() {
        $('#mainNav').addClass('navbar-shrink')
    })
    $('#navbarResponsive').on('hidden.bs.collapse', function() {
        if ($('body').offset.top == 0)
            $('#mainNav').removeClass('navbar-shrink')
    })
    $('#checkout').click(function() {
        window.location.href = 'donation.php'
    })
})
</script>