<?php /** @var \Web\View $this; */?>
<?php foreach ($this->get('messages', []) as $message) { ?>
    <div>
        <?php echo date("H:i:s", $message['createdAt']) ?>
        <b><?php echo htmlspecialchars($message['loginName'])?></b>: <?php echo htmlspecialchars($message['messageText'])?>
    </div>
<?php } ?>
