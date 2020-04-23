<?php

use Web\User\AuthController;
use Web\User\IndexController;

/** @var \Web\View $this; */
$this->set('title', 'Registration');
echo $this->load('header')->render();
if ($this->has('error')) {
    ?>
    <div class="alert alert-danger" role="alert">
        Error: <?php echo $this->get('error') ?>
    </div>
    <?php
}
?>
    <form action="<?php echo $this->url(AuthController::ROUTE_SIGN_IN) ?>" method="post">
        <div class="form-group row">
            <label for="loginName" class="col-sm-2 col-form-label">Login Name:</label>
            <div class="col-sm-10">
                <input type="text" name="loginName" id="loginName" class="form-control"/>
            </div>
        </div>
        <div class="form-group row">
            <span class="col-sm-2 col-form-label">&nbsp;</span>
            <div class="col-sm-10">
                <input type="submit" value="Send" class="btn btn-primary"/>
                <a href="<?php echo $this->url(IndexController::ROUTE_START)?>">Cancel</a>
            </div>
        </div>
    </form>
    <script> document.getElementById('loginName').focus() </script>
<?php
echo $this->load('footer')->render();
