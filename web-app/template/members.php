<?php /** @var \Web\View $this; */
$statusToClass = [
    'inactive' => 'badge-secondary',
    'active' => 'badge-success',
    'away' => 'badge-warning',
];
foreach ($this->get('members', []) as $member) { ?>
    <div>
        <b><?php echo htmlspecialchars($member['loginName'])?></b> <span class="badge badge-pill <?php echo $statusToClass[$member['status']]?>"><?php echo $member['status']?></span>
    </div>
<?php
}
