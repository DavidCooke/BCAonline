<div class="importedUsers form">
    <?php echo $this->Session->flash('auth'); ?>
    <h2><?php echo __('Report of Repeated Import Lines for Group Members'); ?></h2>

    <p>This report shows the lines in the imported file where the combined BCA No/Organisation/Status occur more than once for group members.</p>
    <p>There shouldn't be any.</p>

    <h3><?php
        $line_count = count($repeatedLines);
        if ($line_count == 0) {
            echo "There are no repeated lines.";
        } elseif ($line_count == 1) {
            echo "There is 1 repeated line.";
        } else {
            echo "There are $line_count repeated lines.";
        }
    ?></h3>

    <?php
    for ($c1 = 0; $c1 < $line_count; $c1++) {
        $organisations = explode(",", $repeatedLines[$c1][0]['organisations']);

        for ($c2 = 0; $c2 < $repeatedLines[$c1][0]['row_count']; $c2++) {
        ?>
        <dl>
        <?php echo $repeatedLines[$c1]['ImportedUser']['bca_no'] . " - " .
            //$repeatedLines[$c1]['ImportedUser']['class'] . " - " .
            $organisations[$c2];
        ?>
        </dl>
        <?php } ?>
        <dl>&nbsp;</dl>
    <?php } ?>

</div>
<div class="actions">
    <h3><?php echo __('Actions'); ?></h3>
    <ul>
        <li><?php if ($line_count <> 0) {echo $this->Html->link(__('Send As Email'), array('action'=>'email_group_repeated_lines'));} ?> </li>
        <li><?php if ($line_count <> 0) {echo $this->Form->postLink(__('Tidy'), array('action' => 'tidy_group_repeated_lines'), null,
        __('Are you sure you remove all the duplicate records?'));} ?></li>
        <li><?php echo $this->Html->link(__('Return'), array('action'=>'index')); ?> </li>
    </ul>
</div>