<?php

use Web\Communication\MessagesController;
use Web\User\AuthController;

/** @var \Web\View $this; */
$this->set('title', 'Phoenix chat');
echo $this->load('header')->render();
?>
    <div class="row">
        <div class="col-8">
            <div id="messages" class="form-control" style="height: 500px; margin-bottom: 10px; overflow-y: scroll"></div>
        </div>
        <div class="col-4">
            <div id="members"></div>
        </div>
    </div>
<?php
if ($this->get('profileId', null)) {
?>
    <form id="sendForm" action="<?php echo $this->url(MessagesController::ROUTE_SEND_MESSAGE) ?>" method="post" class="row">
        <div class="col-8">
            <div class="form-group row">
                <label for="messageText" class="col-sm-2 col-form-label">Message:</label>
                <div class="col-sm-10">
                    <input type="text" name="messageText" id="messageText" class="form-control"/>
                </div>
            </div>
        </div>
        <div class="col-4">
            <input type="submit" value="Send" id="sendButton" class="btn btn-primary"/>
            <a href="<?php echo $this->url(AuthController::ROUTE_LOG_OUT)?>">Log Out</a>
        </div>
    </form>
<?php
} else {
?>
    <nav class="nav">
        <a class="nav-link" href="<?php echo $this->url(AuthController::ROUTE_LOG_IN)?>">Log In</a>
        <a class="nav-link" href="<?php echo $this->url(AuthController::ROUTE_SIGN_IN)?>">Sign In</a>
    </nav>
<?php
}
?>

<?php
echo $this->load('footer')->render();
