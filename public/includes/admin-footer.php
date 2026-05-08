</main>

<?php if (!empty($page_scripts) && is_array($page_scripts)): foreach ($page_scripts as $src): ?>
<script src="<?= asset($src) ?>" defer></script>
<?php endforeach; endif; ?>
</body>
</html>
