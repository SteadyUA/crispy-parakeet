<?php
/** @var \Web\View $this; */
$this->set('title', 'Error');
echo $this->load('header')->render();
?>
    <p>
        <?php echo $this->get('error'); ?>
    </p>
<?php
echo $this->load('footer')->render();
