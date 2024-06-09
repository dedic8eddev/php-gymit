<div class="pagination">
    <ul>
        <?php for($i=1;$i<=$lastPage;$i++): ?>
            <?php if ($page === $i) : ?>
                <li class="disabled">
                    <span><?php echo $i; ?></span>
                </li>
            <?php else: ?>
                <li><a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
            <?php endif; ?>
        <?php endfor; ?>
    </ul>
</div>